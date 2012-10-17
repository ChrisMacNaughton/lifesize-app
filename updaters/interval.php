<?php
set_time_limit(0);
if(!isset($argv))
	die("Must be run from the command line");
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
if($redis->get('cleaned') < time() - 1800){
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

if($redis->get('stats_generated') < time() - 15 * 60){
	$redis->set('stats_generated', time());
	
	print("Updating Stats\n");
	/*
	SELECT AVG( (
`RxV1PktsLost` +  `RxA1PktsLost` +  `RxV2PktsLost`
) /  `Duration` ) AS RxPctLoss, AVG( (
`TxV1PktsLost` +  `TxA1PktsLost` +  `TxV2PktsLost`
) /  `Duration` ) AS TxPctLoss
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
*/
	$start = microtime(true);
	$totals = $db->query("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE devices_history.duration > 0")->fetch(PDO::FETCH_ASSOC);

	$d = ($totals['Duration'] / 60);
	$averages['RxV1'] = $totals['RxV1'] / $d;
	$averages['RxA1'] = $totals['RxA1'] / $d;
	$averages['RxV2'] = $totals['RxV2'] / $d;
	$averages['TxV1'] = $totals['TxV1'] / $d;
	$averages['TxA1'] = $totals['TxA1'] / $d;
	$averages['TxV2'] = $totals['TxV2'] / $d;
	$redis->set('cache.averages', json_encode($averages));
}

if($redis->get('device_stats_generated') < time() - 15 * 60){
	print("Updating device stats!\n");
	$redis->set('device_stats_generated', time());
	
	print("Updating Stats\n");

	$start = microtime(true);
	$models = $db->query("SELECT distinct model as model FROM devices WHERE model IS NOT null")->fetchAll(PDO::FETCH_ASSOC);

	foreach($models as $model){
		$model = $model['model'];
		$totals = $db->query("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM( devices_history.duration ) AS Duration
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
	//print_r($averages);
	$redis->set('cache.device_averages', json_encode($averages));
	//$redis->del('device_stats_generated');
}

