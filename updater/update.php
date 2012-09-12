<?php
define('START', time());
ignore_user_abort(true);
$type = "Updater-web";
require_once('common.php');
$time = (int)time() - 60;
$res = $db->query("SELECT value FROM settings WHERE setting = 'max_updaters'")->fetch(PDO::FETCH_ASSOC);
$max_updaters = $res['value'];
$query = "SELECT count(distinct updater_id) AS count FROM updater_log WHERE type = 'Updater' AND `timestamp` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];

if ($current_devices == $max_updaters OR $current_devices > $max_updaters){
	echo "\n\n";
	die('Already at max updaters of ' . $max_updaters . " ( $current_devices ) ");
}
$stmt = $db->prepare("SELECT devices.* FROM devices INNER JOIN companies ON devices.company_id = companies.id WHERE updated < (:now - companies.interval * 60) AND companies.active = 1 AND devices.active=1 AND updating < :updating ORDER BY updated LIMIT 1");
$rsrv = $db->prepare("UPDATE devices SET updating = :time WHERE id = :id AND updating = :updating");
$updateStmt = $db->prepare("UPDATE devices SET updated = :updated, duration = :duration, status = :status, name = :name, model_id = :model, software_version_id = :version, screenshot = :screenshot, online = 1 WHERE id = :id");
$last_run = time();
$history_stmt = $db->prepare("INSERT INTO devices_history VALUES(:1,:2,:3,:4,:5,:6,:7,:8,:9,:10,:11,:12,:13,:14,:15,:16,:17,:18,:19,:20,:21,:22,:23,:24,:25,:26,:27,:28,:29,:30,:31,:32,:33,:34,:35,:36,:37,:38,:39,:40,:41,:42,:43,:44,:45,:46,:47,:48,:49, :50)");
$ok = "
ok,00
";
$email = new AmazonSES($options);
$duration_stmt = $db->prepare("SELECT duration FROM devices_history WHERE device_id = :id");
$make_stmt = $db->prepare("SELECT id FROM makes WHERE name = :name");
$model_stmt = $db->prepare("SELECT id FROM models WHERE name = :name AND make_id = :id");
$history_start_stmt = $db->prepare("SELECT id FROM devices_history WHERE device_id = :id ORDER BY id DESC limit 1");
$prev_offline = $db->prepare("SELECT online FROM devices WHERE id = :id");
$alarms_stmt = $db->prepare("SELECT user_id, last_notified FROM devices_alarms WHERE enabled = 1 AND alarm_id = 'alarm-jfu498hf' AND device_id = :id");
$alarm_user = $db->prepare("SELECT email, name FROM users WHERE id = :id");
$alarms_devices_update = $db->prepare("UPDATE devices_alarms SET last_notified = :time, active = 1 WHERE alarm_id = :alarm AND device_id = :device AND user_id = :user");
$alarms_disable = $db->prepare("UPDATE devices_alarms SET active = 0 WHERE alarm_id = :alarm AND device_id = :device");
while(true){
	$time = time();
	$stmt->execute(array(':now'=>$time, ':updating'=>$time - 30));
	if ($stmt->rowCount() != 0) {

	$device = $stmt->fetch(PDO::FETCH_ASSOC);
	$rsrv->execute(array(
		':time'=>$time,
		':id'=>$device['id'],
		':updating'=>$device['updating']
		));
	$res = $rsrv->rowCount();
	if ($res) {
	ulog($updater_log, "Checking", $device['id'] . ' | ' . $device['name']);
	$stmt->closeCursor();
	//print_r($device);
	$ssh = new NET_SSH2($device['ip']);
	$rand = hash('sha512', rand(1,1000) . time());
	$updateId = 'upd-' . substr($rand, 0, 10);
	$query = "INSERT INTO device_updates (id, device_id, updated) VALUES ('$updateId','". $device['id']. "', '" . time() ."')";
	//echo $query . "\n";
	$db->query($query);
	if (!$ssh->login('auto', $device['password'])) {
		#ulog($updater_log, $device['id'] . " is Offline");
		$prev_offline->execute(array(':id'=>$device['id']));
		$res = $prev_offline->fetch(PDO::FETCH_ASSOC);
		if ($res['online'] == 0) {
			//device is offline twice
			$alarms_stmt->execute(array(':id'=>$device['id']));
			$alarms = $alarms_stmt->fetchAll(PDO::FETCH_ASSOC);
			//echo "\n";print_r($alarms);echo"\n";
			foreach ($alarms as $alarm) {
				if ($alarm['last_notified'] < $time - 60 * 5) {
					$alarm_user->execute(array(':id'=>$alarm['user_id']));
					$user = $alarm_user->fetch(PDO::FETCH_ASSOC);
					$alarm_user->closeCursor();
					//echo "User:" ;print_r($user);
					$change_settings = "If you'd like to change your settings about which alarms to enable, please login to https://app.control.vc and modify your enabled alarms!";
					$message = sprintf("Hello, %s\n
	Your device: %s (IP: %s) is offline at %s!
	Thank you!
	\n%s

	%s", $user['name'], $device['name'], $device['ip'], date('m/d/Y h:i:s a T', time()), $signature, $change_settings);
					
					$response = $email->send_email(
						$from,
						array('ToAddresses'=>array(
							$user['email'],
						)),
						array(
							'Subject.Data' => $subject,
							'Body.Text.Data' => $message
						)
					);
					if ($response)
						$alarms_devices_update->execute(array(
							':time'=>$time,
							':alarm'=>'alarm-jfu498hf',
							':user'=>$alarm['user_id'],
							':device'=>$device['id']
							));
					//print_r($response);
				}
			}
			$alarms_stmt->closeCursor();
			//exit();
		}
		$db->query("UPDATE devices SET updated = $time, online = 0 WHERE id = '" . $device['id'] . "'");
	} else {
		$alarms_disable->execute(array(
							':alarm'=>'alarm-jfu498hf',
							':device'=>$device['id']
							));
		//print_r($alarms_disable->errorInfo());
		//print_r($alarms_disable);
		ulog($updater_log, "Updating ", $device['id'] . " | " . $device['name']);
		$ssh->exec("set system licensekey -u");
		$edits = $db->query("SELECT * FROM edits WHERE device_id = '" . $device['id'] . "' AND completed = 0")->fetchAll(PDO::FETCH_ASSOC);
		
		//print_r($edits);
		
		foreach ($edits as $edit) {
			//print_r($edit);
			
			$req = "set " .$edit['object'] . ' ' . $edit['target'] . ' "' . $edit['detail'] . '"';
			$res = $ssh->exec($req);
			
			$res = explode(chr(0x0a), $res); $res = $res[1];
			//var_dump($res);
			if ($res == "ok,00")
				$db->query("UPDATE edits SET completed = 1 WHERE id = '" . $edit['id'] . "'");
			//echo "SSH Result ($req): ";print_r($res); echo"\n"
		}
		$res = explode(chr(0x0a), $ssh->exec("get system name"));
		$name = $res[0];
		$res = explode(chr(0x0a), $ssh->exec("status call active"));
		$status = ($res[0] == "") ? 11 : 12;
		if ($status == 12) {
			$res = explode(chr(0x0a), $ssh->exec("get video input-snapshot hdmi0"));
			for ($i=0; $i<4; $i++)
				array_pop($res);
			$screenshot =implode('', $res);
		} else {
			$screenshot = "iVBORw0KGgoAAAANSUhEUgAAAaAAAADqCAIAAABr4AawAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAURSURBVHhe7dABDQAAAMKg909tDwcRKAwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMGDAgAEDBgwYMPA2MHXmAAEaVWwgAAAAAElFTkSuQmCC";
		}
		$res = explode(chr(0x0a), $ssh->exec("get system version"));
		$res = explode('_', $res[0]);
		//echo "Version:";
		//print_r($res);
		$version = explode(' ',$res[count($res)-1]);
		$version = $version[0];
		//echo $version;
		//echo "\n\n";
		$res = explode(chr(0x0a), $ssh->exec('get system model'));
		
		$res = explode(',', $res[0]);
		$make_stmt->execute(array(':name'=>$res[0]));
		$make = $make_stmt->fetch(PDO::FETCH_ASSOC);
		$make_stmt->closeCursor();
		$make = $make['id'];
		$model_stmt->execute(array(':name'=>$res[1], ':id'=>$make));
		$model = $model_stmt->fetch(PDO::FETCH_ASSOC);
		$model_stmt->closeCursor();
		$model = $model['id'];
		/*
		get call history
		*/
		$locale = explode(chr(0x0a), $ssh->exec('get locale gmt-offset'));
		$change = str_split($locale[0]);
		//print_r($change);
		$timezone['direction'] = $change[0];
		$timezone['change'] = $change[2] * 60 * 60;
		$hist = explode(chr(0x0a), $ssh->exec("status call history -f -X -D |"));
		//print_r($hist);
		$print = false;
		$history_start_stmt->execute(array(':id'=>$device['id']));
		$start = $history_start_stmt->fetch(PDO::FETCH_ASSOC);
		$start = $start['id'];
		foreach ($hist as $call) {
			$history = explode("|", $call);
			if (count($history) > 5) {
				if ($history[0] > $start) {
					array_unshift($history, $device['id']);
					
					foreach ($history as $key=>$value) {
						//echo $key . " : " . $value . "\n";
						$id = ':' . ($key + 1);
						//echo $id."\n\n";
						$data[$id] = $value;
					}
					$tmp = ($timezone['direction'] == '-') ? strtotime($data[':9']) + $timezone['change'] :strtotime($data[':9']) - $timezone['change'];
					$data[':9'] = date('Y-m-d H:i:s',$tmp);
					$tmp = ($timezone['direction'] == '-') ? strtotime($data[':10']) + $timezone['change'] :strtotime($data[':10']) - $timezone['change'];
					$data[':10'] = date('Y-m-d H:i:s',$tmp);
					$history_stmt->execute($data);
				}	
				//print_r($history_stmt->errorInfo());echo"\n";
			}
			$print = false;
		}
		
		$duration_stmt->execute(array(':id'=>$device['id']));
		$durations = $duration_stmt->fetchAll(PDO::FETCH_ASSOC);
		$duration = 0;
		foreach ($durations as $dur) {
			$duration += to_seconds($dur['duration']);
		}
		$data = array(
			':updated'=>$time,
			':id'=>$device['id'],
			':status'=>$status,
			':name'=>$name,
			':model'=>$model,
			':version'=>$version,
			':screenshot'=>$screenshot,
			':duration'=>$duration
		);
		//print_r($data);
		$updateStmt->execute($data);
		$errors = $db->errorInfo();
		if ($errors[0] != 00000)
			print_r($db->errorInfo());
		$updateStmt->closeCursor();
	}
	$data = null;
	$ssh = null;
	}
	
	} else {
		sleep(rand(1,5));
	}
	
	if ($last_run < $time - 120) {
		//ulog($updater_log, "Memory Usage: ".memory_get_usage());
		$db = null;
		try {
		    $db = new PDO($dsn, $dbuser, $dbpassword);
		} catch (PDOException $e) {
			break;
		}

		$result = $db->query("SELECT value FROM settings WHERE setting = 'continue'")->fetch(PDO::FETCH_ASSOC);
		if ($result['value'] == 0) {
			break;
		}
		$last_run = $time;
	}

	if((time() - START) > (10 * 60)) break;
}

ulog($updater_log, "Closing Down");