<?php

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

if($redis->get('cleaned') < time() - 60){
	try {
		$db = new PDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
	} catch (PDOException $e) {
	    //$app['errors'][]= $e->getMessage();
	    throw new Exception('Service is unavailable', 513);
	}


	$devices = $db->query("SELECT companies_devices.id
		FROM companies_devices
		INNER JOIN companies ON companies.id = companies_devices.company_id
		WHERE companies.active=1")->fetchAll(PDO::FETCH_ASSOC);

	$redis->del('updates');

	foreach($devices as $d){
		$redis->lpush('updates', $d['id']);
	}

	$redis->set('cleaned',time());
}