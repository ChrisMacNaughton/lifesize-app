<?php
echo "Starting up Online Checker!\n\n";
require_once 'common.php';

$stmt = $db->prepare(	"SELECT * FROM devices WHERE online_check < :time AND active=1 ORDER BY updated LIMIT 1");
$updateStmt = $db->prepare("UPDATE devices SET online_check = :updated, online = :status WHERE id = :id");
$last_run = time();
while(true) {
	$time = time();
	$stmt->execute(array(':time'=>$time - 30));
	if ($stmt->rowCount() != 0) {
	$device = $stmt->fetch(PDO::FETCH_ASSOC);
	
		echo $device['id'] . ": " . $device['name'] . "\n\n";
	$stmt->closeCursor();
	//print_r($device);
	$ssh = new NET_SSH2($device['ip']);
	if (!$ssh->login('auto', $device['password'])) {
		echo $device['name'] . " is Offline.\n";
		$updateStmt->execute(array(
			':updated'=>$time,
			':status'=>0,
			':id'=>$device['id']
		));
		$updateStmt->closeCursor();
	} else {
		echo $device['name'] . " is Online.\n";
		$updateStmt->execute(array(
			':updated'=>$time,
			':status'=>1,
			':id'=>$device['id']
		));
		$updateStmt->closeCursor();
	}
	$ssh = null;
	echo "\n";
	echo "Memory Usage: ".memory_get_usage() . "\n\n";
	} else {
		sleep(5);
	}
	
	if ($last_run <= ($time - 120)) {
		try {
		    $db = new PDO($dsn, $dbuser, $dbpassword);
		} catch (PDOException $e) {
			sleep(15 * rand(1,3));
			try {
		    $db = new PDO($dsn, $dbuser, $dbpassword);
			} catch (PDOException $e) {
			    die("Error connecting to the database: " .  $e->getMessage());
			}
		}
		
		echo "Checking if to continue\n";
		$result = $db->query("SELECT value FROM settings WHERE setting = 'continue'")->fetch(PDO::FETCH_ASSOC);
		if ($result['value'] == 0) {
			break;
		}
		$last_run = $time;
	}
	if (rand(1,5) == 1)
		sleep(rand(1,5));
}

echo "Closing Down";