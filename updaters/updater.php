<?php
if(!isset($argv))
	die("Must be run from the command line");

require_once 'mySSH.php';

$start = time();
$max_runtime = (60 * 60) + rand(60,600);


require '../system/config.php';
require '../system/classes/loggedPDO.php';
try {
	$db = new loggedPDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
} catch (PDOException $e) {
    //$app['errors'][]= $e->getMessage();
    throw new Exception('Service is unavailable', 513);
}
$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
$version = $res['version'];

$stmt = $db->prepare("SELECT devices.* FROM devices INNER JOIN companies_devices ON devices.id = companies_devices.device_id INNER JOIN companies ON companies.id = companies_devices.company_id WHERE devices.updated <= (:now - companies.interval * 60) AND companies.active = 1 AND updating < :updating ORDER BY updated LIMIT 1");
$rsrv = $db->prepare("UPDATE devices SET updating = :time WHERE id = :id AND updating = :updating");

while(time() < $start + $max_runtime){
	$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
	$current_version = $res['version'];
	if($version != $current_version){
		print("New worker version\n");
		exit(0);
	}
	$time = time();
	$stmt->execute(array(':now'=>$time, ':updating'=>$time - 30));
	$device = $stmt->fetch(PDO::FETCH_ASSOC);

	print_r($device);
	exit(0);
}