<?php

if(!isset($argv))
	die("Must be run from the command line");

$worker_id = getmypid().'-'.substr(sha1(rand(-1000,1000)), 0,5);
require_once 'mySSH.php';
require_once (dirname(__FILE__).'/../vendor/autoload.php');
error_reporting(E_ALL^E_USER_NOTICE);
/* used functions*/
function clean($command){
	$res = explode(chr(0x0a), $command);
	
	if($res[2] == 'ok,00')
		return $res[0];
	else
		return false;
}
function to_seconds($duration) {
	$i = explode(':', $duration);
	return ($i[0] * 60 * 60) + ($i[1] * 60) + $i[2];
}


$start = time();
$max_runtime = (60 * 60) + rand(60,600);
$end = $start + $max_runtime;

require dirname(__FILE__).'/../system/config.php';
//require '../system/classes/loggedPDO.php';
try {
	$db = new PDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
} catch (PDOException $e) {
    //$app['errors'][]= $e->getMessage();
    throw new Exception('Service is unavailable', 513);
}
$ses = new AmazonSES($options);
$res = $db->query("SELECT value FROM settings WHERE setting = 'max_alarms'")->fetch(PDO::FETCH_ASSOC);
$max_updaters = $res['value'];
$time = (int)time() - 60;
$query = "SELECT count(distinct worker_id) AS count FROM updater_log WHERE `type` = 'alarms' AND `time` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];

if ($current_devices >= $max_updaters){
	die('Already at max updaters of ' . $max_updaters . " ( $current_devices )\n");
}

$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'alarms_version'")->fetch(PDO::FETCH_ASSOC);
$worker_version = $res['version'];

$log_stmt = $db->prepare("INSERT INTO updater_log (time, worker_id, message, detail, type) VALUES (:time, :id, :message, :detail, 'alarms')");

$log_stmt->execute(array(
		':time'=>time(),
		':id'=>$worker_id,
		':message'=>"Initialized",
		':detail'=>''
	));
$stmt = $db->prepare("SELECT devices.name AS devicename, devices_alarms . * , alarms.name AS alarmname, companies_devices.ip AS ip
FROM devices_alarms
INNER JOIN companies_devices ON companies_devices.id = devices_alarms.device_id
INNER JOIN devices ON companies_devices.hash = devices.id
INNER JOIN alarms ON alarms.id = devices_alarms.alarm_id
WHERE devices_alarms.active =1");
$users_stmt = $db->prepare("SELECT users.name, users.email, users.id, users.updated
FROM users
INNER JOIN users_alarms ON users_alarms.user_id = users.id
WHERE users_alarms.enabled = 1
AND users_alarms.notified < :time
AND users_alarms.alarm_id = :alarm
AND users_alarms.device_id = :device
AND users_alarms.notified < :updated");
$update_stmt = $db->prepare("UPDATE users_alarms SET notified = :time WHERE alarm_id = :alarm AND device_id = :device AND user_id = :user");
$messages = array(
	'Offline'=>"Hi, %s
\tYour video conferencing device, %s (%s), went offline on %s.

The ControlVC Team

\tIf you'd like to change your settings about which alarms to enable, please login to https://app.control.vc and modify your enabled alarms!"
);
print("Starting Loop\n");
while(time() <= $end){
	$stmt->execute();
	$alarms = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(count($alarms) == 0){
		sleep(1);
	} else {
		foreach($alarms as $alarm){
			//print_r($alarm);
			$opts = array(
				':time'=>$alarm['updated'],
				':alarm'=>$alarm['alarm_id'],
				':device'=>$alarm['device_id'],
				':updated'=>$alarm['updated']
			);
			//print_r($opts);
			$users_stmt->execute($opts);
			$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
			print_r($users);
			if(count($users) > 0){
				//print_r($users);
				foreach ($users as $user){
					print_r($user);
					/*
					$response = $ses->send_email(
					    'support@control.vc', // Source (aka From)
					    array('ToAddresses' => array( // Destination (aka To)
					        $user['email']
					    )),
					    array( // Message (short form)
					        'Subject.Data' => 'Device Alarm!',
					        'Body.Text.Data' => sprintf($messages[$alarm['alarmname']], $user['name'], $alarm['devicename'], $alarm['ip'], date('M. d Y \a\t h:i a', $alarm['updated']))
					    )
					);
					if($response->isOK()){
						$update_stmt->execute(array(
							':time'=>time(),
							':alarm'=>$alarm['alarm_id'],
							':device'=>$alarm['device_id'],
							':user'=>$user['id']
						));
					}
					*/
				}
			}
			/*
			$response = $ses->send_email(
			    'support@control.vc', // Source (aka From)
			    array('ToAddresses' => array( // Destination (aka To)
			        $alarm['email']
			    )),
			    array( // Message (short form)
			        'Subject.Data' => 'Device Alarm!',
			        'Body.Text.Data' => sprintf($messages[$alarm['alarmname']], $alarm['username'], $alarm['devicename'], $alarm['deviceip'], date('M. d Y \a\t h:i a', $alarm['updated']))
			    )
			);
			if($response->isOK()){
				$update_stmt->execute(array(
					':time'=>time(),
					':alarm'=>$alarm['alarm_id'],
					':device'=>$alarm['device_id'],
					':user'=>$alarm['user_id']
				));
			}
			*/
		}
		sleep(1);
	}
}
