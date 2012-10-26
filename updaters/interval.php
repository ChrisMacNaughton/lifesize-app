<?php
set_time_limit(0);
if(!isset($argv))
	die("Must be run from the command line");
if(isset($argv[1]) AND $argv[1] == "-f"){
	define('RESET', true);
} else
	define('RESET', false);

date_default_timezone_set("UTC");

error_reporting(E_ALL^E_USER_NOTICE);

require 'predis/autoload.php';
Predis\Autoloader::register();
require dirname(__FILE__).'/../system/config.php';


$single_server = array(
    'host'     => $redis_server,
    'port'     => 6379,
);

$redis = new Predis\Client($single_server);
$redis->auth($redis_pass);
try {
	$db = new PDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
} catch (PDOException $e) {
    throw new Exception('Service is unavailable', 513);
}
$log_stmt = $db->prepare("INSERT INTO updater_log (time, worker_id, message, detail, type) VALUES (unix_timestamp(), :id, :message, :detail, 'interval')");
$id = 'interval-'.getmypid().'-'.substr(sha1(rand(-1000,1000)), 0,5);
$log_stmt->execute(array(
	':id'=>$id,
	':message'=>"Initialized Interval",
	':detail'=>''
	));
if(RESET OR $redis->get('cleaned') < time() - 1800){
	print("Updating Updaters List\n");
	$redis->set('cleaned',time());
	$devices = $db->query("SELECT companies_devices.id
FROM companies_devices
INNER JOIN companies ON companies.id = companies_devices.company_id
INNER JOIN devices ON devices.id = companies_devices.hash
WHERE companies.active =1
ORDER BY devices.updated")->fetchAll(PDO::FETCH_ASSOC);

	$redis->del('updates');

	foreach($devices as $d){
		$redis->lpush('updates', $d['id']);
	}

	
}

if(RESET OR $redis->get('stats_generated') < time() - 15 * 60){
	$redis->set('stats_generated', time());
	
	print("Updating Stats\n");
	/*
	SELECT AVG( (
`RxV1PctLoss` +  `RxA1PctLoss` +  `RxV2PctLoss`
) /  `Duration` ) AS RxPctLoss, AVG( (
`TxV1PctLoss` +  `TxA1PctLoss` +  `TxV2PctLoss`
) /  `Duration` ) AS TxPctLoss
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
*/
	$start = microtime(true);
	$totals = $db->query("SELECT AVG(  `RxV1PctLoss` ) AS RxV1, AVG(  `RxA1PctLoss` ) AS RxA1, AVG(  `RxV2PctLoss` ) AS RxV2, AVG(  `TxV1PctLoss` ) AS TxV1, AVG( `TxA1PctLoss` ) AS TxA1, AVG(  `TxV2PctLoss` ) AS TxV2
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE devices_history.duration > 0")->fetch(PDO::FETCH_ASSOC);
	/*
	$d = ($totals['Duration'] / 60);
	$averages['RxV1'] = $totals['RxV1'] / $d;
	$averages['RxA1'] = $totals['RxA1'] / $d;
	$averages['RxV2'] = $totals['RxV2'] / $d;
	$averages['TxV1'] = $totals['TxV1'] / $d;
	$averages['TxA1'] = $totals['TxA1'] / $d;
	$averages['TxV2'] = $totals['TxV2'] / $d;
	*/
	print_r($db->query("SELECT * 
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE device_id =  'ac0f7e0640a62f23fd12251432fed7e8bcf5850f'")->fetchAll(PDO::FETCH_ASSOC));
	$averages = $totals;
	print_r($averages);
	//$redis->set('cache.averages', json_encode($averages));
}
exit();
if(RESET OR $redis->get('device_stats_generated') < time() - 15 * 60){
	$averages = array();
	print("Updating device stats!\n");
	$redis->set('device_stats_generated', time());

	$start = microtime(true);
	$models = $db->query("SELECT distinct model as model FROM devices WHERE model IS NOT null")->fetchAll(PDO::FETCH_ASSOC);

	foreach($models as $model){
		$model = $model['model'];
		$totals = $db->query("SELECT SUM(  `RxV1PctLoss` ) AS RxV1, SUM(  `RxA1PctLoss` ) AS RxA1, SUM(  `RxV2PctLoss` ) AS RxV2, SUM(  `TxV1PctLoss` ) AS TxV1, SUM( `TxA1PctLoss` ) AS TxA1, SUM(  `TxV2PctLoss` ) AS TxV2, SUM( devices_history.duration ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
INNER JOIN devices ON devices.id = devices_history.device_id
WHERE devices_history.duration >0
	AND devices.model = '$model'")->fetch(PDO::FETCH_ASSOC);
		$d = ($totals['Duration'] / 60);
	$averages[$model]['RxV1'] = $totals['RxV1'] / $d;
	$averages[$model]['RxA1'] = $totals['RxA1'] / $d;
	$averages[$model]['RxV2'] = $totals['RxV2'] / $d;
	$averages[$model]['TxV1'] = $totals['TxV1'] / $d;
	$averages[$model]['TxA1'] = $totals['TxA1'] / $d;
	$averages[$model]['TxV2'] = $totals['TxV2'] / $d;
	}
	print_r($averages);
	//$redis->set('cache.device_averages', json_encode($averages));
	//$redis->del('device_stats_generated');
}

if($redis->get('companies_cleaned') < time() - 30 * 60){
	require_once (dirname(__FILE__).'/../vendor/autoload.php');

	Stripe::setApiKey($stripe_key);
	$customers = Stripe_Customer::all();
	$stmt = $db->prepare("UPDATE companies SET plan_id = :id, last4=:last4 WHERE customer_id = :customer_id");
	$plan_stmt = $db->prepare("SELECT id FROM subscriptions WHERE name = :name");
	foreach($customers['data'] as $customer){
		$customer_id = $customer['id'];
		$plan_id = $customer['subscription']['plan']['name'];
		//print("Plan ID: $plan_id\n");
		if(strpos($plan_id, '-')){
			$plan = explode('-',$plan_id);
			$plan_name = $plan[0];
		} else {
			$plan_name = $plan_id;
		}
		//print("Plan Name: $plan_name\n");
		$last4 = $customer['active_card']['last4'];
		$plan_stmt->execute(array(':name'=>$plan_name));
		$s = $plan_stmt->fetch(PDO::FETCH_ASSOC);
		$id = $s['id'];
		//print_r($s);
		$options = array(':id'=>$id, ':customer_id'=>$customer_id, ':last4'=>$last4);

		$stmt->execute($options);
		//print_r($options);
	}
}