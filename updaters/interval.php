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

if($redis->get('cleaned') < time() - 3600){
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
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id")->fetch(PDO::FETCH_ASSOC);

	$d = ($totals['Duration'] / 60);
	$averages['RxV1'] = $totals['RxV1'] / $d;
	$averages['RxA1'] = $totals['RxA1'] / $d;
	$averages['RxV2'] = $totals['RxV2'] / $d;
	$averages['TxV1'] = $totals['TxV1'] / $d;
	$averages['TxA1'] = $totals['TxA1'] / $d;
	$averages['TxV2'] = $totals['TxV2'] / $d;
	//print("Global Averages:\n");
	//print_r($averages);
	//$final = microtime(true);
	//$total = $final - $start;
	//print("Generated in " . round($total, 4) . " seconds\n");
	//$redis->del('stats_generated');
	$redis->set('cache.averages', json_encode($averages));
}