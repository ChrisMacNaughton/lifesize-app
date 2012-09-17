<?
if(function_exists(newrelic_disable_autorun)){
	newrelic_disable_autorun();
	newrelic_ignore_transaction();
}
require_once 'vendor/autoload.php';
date_default_timezone_set('UTC');
define("COMPANY_NAME", 'ControlVC');
//common.php
define('UPDATER_ID', sha1($type . ' ' . rand(1,1000) . microtime(true)));
ulog(false, "Starting up!");
#echo "Updater: " . UPDATER_ID;
include 'config.php';
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
require_once 'Net/SSH2.php';
error_reporting(E_ALL^E_NOTICE^E_USER_NOTICE);
//connect to the mysql db instance of RDS
$dsn = 'mysql:dbname=vcdb;host=vcdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';
ulog(false, "Connecting to the database");
try {
    $db = new PDO($dsn, $dbuser, $dbpassword);
} catch (PDOException $e) {
    die(ulog(false, "Error connecting to the database: " .  $e->getMessage()));
}
$updater_log = $db->prepare("INSERT INTO updater_log (updater_id, type, `timestamp`, action, detail) VALUES (:id, :type, :time, :action, :detail)");

//ulog(false, "Connected!");
$email = new AmazonSES($options);

$signature = 'The ' . COMPANY_NAME . ' Team';

$subject = "Device Alarm!";	
$from = "no-reply@control.vc";
function to_seconds($duration) {
	$i = explode(':', $duration);
	return ($i[0] * 60 * 60) + ($i[1] * 60) + $i[2];
}

function ulog($updater_log, $action, $detail = '') {
	global $type;
	if($updater_log) {
		$updater_log->execute(array(
			':id'=>UPDATER_ID,
			'type'=>$type,
			':time'=>time(),
			':action'=>$action,
			':detail'=>$detail
			));
		//print_r($updater_log->errorInfo());
	} else {
		$message = $action . " " . $detail;
		//echo "[$type ".UPDATER_ID."](".time().")||" . $message . "\n";
		//$log = fopen('log', 'a');
		//fwrite($log, "[Updater ".UPDATER_ID."](".time().")||" . $message . "\n");
		//fclose($log);
	}
}