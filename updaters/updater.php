<?php
if(!isset($argv))
	die("Must be run from the command line");
date_default_timezone_set("UTC");
$worker_id = getmypid().'-'.substr(sha1(rand(-1000,1000)), 0,5);
require_once 'mySSH.php';
error_reporting(E_ALL^E_USER_NOTICE);
/* used functions*/
function clean($command){
	$res = explode(chr(0x0a), $command);
	
	if($res[2] == 'ok,00')
		return $res[0];
	else
		return false;
}
function assign($clean, $name, $device){
	global $ssh;
	$res = clean($ssh->exec($clean));
	if($res){
		$ret = $res;
	} else {
		$ret = '';
	}
	return $ret;
}
function to_seconds($duration) {
	$i = explode(':', $duration);
	return ($i[0] * 60 * 60) + ($i[1] * 60) + $i[2];
}


$start = time();
$max_runtime = (60 * 60) + rand(60,600);
$end = $start + $max_runtime;

require dirname(__FILE__).'/../system/config.php';
require dirname(__FILE__).'/../system/classes/loggedPDO.php';
try {
	$db = new loggedPDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
} catch (PDOException $e) {
    //$app['errors'][]= $e->getMessage();
    throw new Exception('Service is unavailable', 513);
}

$res = $db->query("SELECT value FROM settings WHERE setting = 'continue'")->fetch(PDO::FETCH_ASSOC);
if($res['value'] != 1 AND DEV_ENV === false){
	exit();
}

$res = $db->query("SELECT value FROM settings WHERE setting = 'max_updaters'")->fetch(PDO::FETCH_ASSOC);
$max_updaters = $res['value'];
$time = (int)time() - 60;
$query = "SELECT count(distinct worker_id) AS count FROM updater_log WHERE `type` = 'updater' AND `time` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];

if (($current_devices == $max_updaters OR $current_devices > $max_updaters) AND !DEV_ENV){
	die('Already at max updaters of ' . $max_updaters . " ( $current_devices )\n");
}

$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
$worker_version = $res['version'];

$stmt = $db->prepare("SELECT CD.id, CD.ip, CD.password, CD.own, CD.verified, CD.hash, D.online, D.serial, D.updating
FROM companies_devices AS CD
INNER JOIN devices AS D ON CD.hash = D.id
INNER JOIN companies ON CD.company_id = companies.id
WHERE D.updated <= ( UNIX_TIMESTAMP() - companies.interval * 60 -5 ) 
AND D.updating < ( UNIX_TIMESTAMP() -30 ) 
AND CD.checked < unix_timestamp() - 30
ORDER BY D.updated, CD.id
LIMIT 1");
/*
SELECT D.online, D.serial, D.updating, cd.ip, cd.password
FROM  `devices` AS D
LEFT JOIN companies_devices AS cd ON D.id = cd.hash
WHERE D.updating <= ( UNIX_TIMESTAMP( ) -30 ) 
AND D.updated <= ( UNIX_TIMESTAMP( ) -60 ) 
AND D.serial !=  'New Device'
ORDER BY D.updated
*/
$rsrv = $db->prepare("UPDATE devices SET updating = unix_timestamp() WHERE id = :id AND updating = :updating");
$offline_stmt = $db->prepare("UPDATE devices SET online = 0, updated = unix_timestamp() WHERE id = :id");
$checked_stmt = $db->prepare("UPDATE companies_devices SET checked = unix_timestamp() WHERE id = :id");
$serial_stmt = $db->prepare("SELECT * FROM devices WHERE `serial` = :serial");
$new_serial = $db->prepare("UPDATE devices SET `serial` = :serial WHERE id = :id");
$new_license = $db->prepare("UPDATE devices SET `licensekey` = :license WHERE id = :id");
$change_stmt = $db->prepare("UPDATE companies_devices SET device_id = :id WHERE device_id = :old_id AND company_id = :company");
$remove_stmt = $db->prepare("DELETE FROM devices WHERE id = :id");
$remove_stmt2 = $db->prepare("DELETE FROM companies_devices WHERE device_id = :id");
$update_stmt = $db->prepare("UPDATE devices 
	SET name = :name,
	make=:make,
	model=:model,
	in_call=:call,
	version=:version,
	licensekey=:license,
	updated=unix_timestamp(),
	type=:type,
	serial=:serial,
	online=:online,
	auto_answer=:auto_answer,
	auto_answer_mute=:auto_answer_mute,
	incoming_call_bandwidth=:incoming_call,
	outgoing_call_bandwidth=:outgoing_call,
	incoming_total_bandwidth=:incoming_total,
	outgoing_total_bandwidth=:outgoing_total,
	auto_bandwidth=:auto_bw,
	max_calltime=:max_calltime,
	max_redials=:max_redials,
	auto_answer_multiway=:auto_multiway,
	audio_codecs = :codecs,
	audio_active_microphone = :active_mic,
	camera_lock = :lock,
	telepresence=:telepresence,
	camera_far_control = :far_control,
	camera_far_use_preset = :far_use,
	camera_far_set_preset = :far_set
	WHERE id = :id");
$new_device_stmt = $db->prepare("INSERT INTO devices
	SET id=:id,
	name = :name,
	make=:make,
	model=:model,
	in_call=:call,
	version=:version,
	licensekey=:license,
	updated=unix_timestamp(),
	type=:type,
	serial=:serial,
	online=:online,
	auto_answer=:auto_answer,
	auto_answer_mute=:auto_answer_mute,
	incoming_call_bandwidth=:incoming_call,
	outgoing_call_bandwidth=:outgoing_call,
	incoming_total_bandwidth=:incoming_total,
	outgoing_total_bandwidth=:outgoing_total,
	auto_bandwidth=:auto_bw,
	max_calltime=:max_calltime,
	max_redials=:max_redials,
	auto_answer_multiway=:auto_multiway,
	audio_codecs = :codecs,
	audio_active_microphone = :active_mic,
	camera_lock = :lock,
	telepresence=:telepresence,
	camera_far_control = :far_control,
	camera_far_use_preset = :far_use,
	camera_far_set_preset = :far_set");
$check_for_hash = $db->prepare("SELECT count(*) AS count FROM devices WHERE id = :id");
$update_stmt2 = $db->prepare("UPDATE companies_devices SET hash = :hash WHERE id = :id");
$cleanup = $db->prepare("UPDATE devices SET updated = 0, updating=0 WHERE id = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'");
$log_stmt = $db->prepare("INSERT INTO updater_log (time, worker_id, message, detail, type) VALUES (:time, :id, :message, :detail, 'updater')");
$history_start_stmt = $db->prepare("SELECT id FROM devices_history WHERE device_id = :id ORDER BY id DESC limit 1");
$history_stmt = $db->prepare("INSERT INTO devices_history VALUES(:1,:2,:3,:4,:5,:6,:7,:8,:9,:10,:11,:12,:13,:14,:15,:16,:17,:18,:19,:20,:21,:22,:23,:24,:25,:26,:27,:28,:29,:30,:31,:32,:33,:34,:35,:36,:37,:38,:39,:40,:41,:42,:43,:44,:45,:46,:47,:48,:49, :50)");

$log_stmt->execute(array(
		':time'=>time(),
		':id'=>$worker_id,
		':message'=>"Initialized",
		':detail'=>''
	));

$cleanup_log_stmt = $db->prepare("DELETE FROM updater_log WHERE `time` < (:time - 86400)");
$offline_alarm = $db->prepare("UPDATE devices_alarms SET active = :active WHERE device_id = :id AND alarm_id = 'alarm-jfu498hf'");
$high_loss_stmt = $db->prepare("UPDATE devices_alarms SET active = :active WHERE device_id = :id AND alarm_id = 'alarm-abwo7froseb'");
$get_edits_stmt = $db->prepare("SELECT * FROM edits WHERE device_id = :id AND completed = 0 ORDER BY added");
$edit_completed_stmt = $db->prepare("UPDATE edits SET completed = 1 WHERE id = :id");;
while(time() <= $end){

	$time = time();
	$cleanup_log_stmt->execute(array(':time'=>$time));

	$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
	$current_version = $res['version'];
	if($worker_version != $current_version){
		$log_stmt->execute(array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Closing",
						':detail'=>"New worker version"
					));
		exit(0);
	}
	$log_stmt->execute(array(
		':time'=>$time,
		':id'=>$worker_id,
		':message'=>"Checking for available device",
		':detail'=>''
	));
	print("$time: Checking for available device\n");
	$stmt->execute();
	$device = $stmt->fetch(PDO::FETCH_ASSOC);
	print_r($device);
	//print_r($stmt->errorInfo());
	if(empty($device)){
		sleep(5);
		continue;
	}
	$checked_stmt->execute(array(':id'=>$device['id']));
	//print("Trying to reserve " . $device['id'] . "\n");
	if($device['serial'] != "New Device"){
		$rsrv->execute(array(
			':id'=>$device['hash'],
			':updating'=>$device['updating']
		));
		$res = $rsrv->rowCount();
	} else {
		$res = 1;
	}
	if ($res) {
		print("Locked!\n");

		print($device['id'] . "\n");
		$update_start_time = microtime(true);
		$ssh = new mySSH($device['ip']);
		$pw = ($device['password'] != '')?$device['password'] : 'lifesize';
		if(!$ssh->login('auto', $pw)){
			$offline_stmt->execute(array(':id'=>$device['hash']));
			//print_r($offline_stmt->errorInfo());
			$log_stmt->execute(array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Tried",
						':detail'=>$device['id']
					));
			if($device['online'] == 0){
				$offline_alarm->execute(array(
					':id'=>$device['id'],
					':active'=>1
				));
			}
		} else {
			print("Logged in!\n");

			//echo $serial . " => " . $id;exit("\n\n");
			
			$get_edits_stmt->execute(array(':id'=>$device['id']));
			$edits = $get_edits_stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($edits as $edit){
				$command = $edit['verb'] . ' ' . $edit['object'] . ' ' . $edit['target'] . ' ' . $edit['details'];
				$res = explode(chr(0x0a), $ssh->exec($command));
				//print("Edit: ".$command . "\n");
				if(array_search('ok,00', $res)){
					$edit_completed_stmt->execute(array(':id'=>$edit['id']));
				}
			}
			

			
			
			//print_r($options);
			//print(sprintf("New data for %s is\n\tOnline: %s\n\tName: %s\n\tMake: %s\n\tModel: %s\n\tIn Call:%s\n\tVersion:%s\n", $device['name'], $online, $name, $make, $model, $in_call, $version));
			$ssh->setTimeout(2);
			$ssh->write("get system name\nget audio codecs\nget system serial\nget system model\nstatus call active\nget call auto-answer\nget call auto-mute\nget call max-speed\nget call total-bw\nget call auto-bandwidth\nget call max-time\nget call max-redial-entries\nget call auto-multiway\nget audio active-mic\nget system telepresence\nget camera lock\nget camera far-control\nget camera far-set-preset\nget camera far-use-preset\nget system licensekey -t maint\nget system version\n\n");
			$ssh->read('$');

			$gathered = microtime(true) - $start;
			$raw = $ssh->read();
			//print($raw . "\n");
			$res = explode("\n",implode(explode("ok,00", $raw)));
			print_r($res);

			$model = explode(',',$res[13]);

			$version = explode(',',$res[80]);
			$bw = explode(',',$res[28]);
				$incoming_call_bandwidth = ($bw[0] != "auto")?$bw[0]:0;
				$outgoing_call_bandwidth = ($bw[1] != "auto")?$bw[1]:0;

			$bw = explode(',', $res[32]);
				$incoming_total_bandwidth = ($bw[0] != "auto")?$bw[0]:0;
				$outgoing_total_bandwidth = ($bw[1] != "auto")?$bw[1]:0;
			$lock = explode(',', $res[60]);
			$lock = $lock[1];
			//print_r($res);
			$options = array(
				':id'=>sha1($res[9]),					
				':name'=>$res[1],
				':make'=>$model[0],
				':model'=>$model[1],
				':call'=>(strlen($res[17]) < 10)?0:1,
				':version'=>$version[1],
				':license'=>$res[76],
				':type'=>'camera',
				':online'=>1,
				':auto_answer'=>$res[20],
				':auto_answer_mute'=>$res[24],
				':incoming_call'=>$incoming_call_bandwidth,
				':outgoing_call'=>$outgoing_call_bandwidth,
				':incoming_total'=>$incoming_total_bandwidth,
				':outgoing_total'=>$outgoing_total_bandwidth,
				':auto_bw'=>$res[36],
				':max_calltime'=>$res[40],
				':max_redials'=>$res[44],
				':auto_multiway'=>$res[48],
				':codecs'=>$res[5],
				':active_mic'=>$res[52],
				':lock'=>$lock,
				':telepresence'=>$res[56],
				':far_control'=>$res[64],
				':far_use'=>$res[68],
				':far_set'=>$res[72],
				':serial'=>$res[9]
			);

			$update_options = array(
				':hash'=>$options[':id'],
				':id'=>$device['id']
			);
			$update_stmt2->execute($update_options);
			$check_for_hash->execute(array(':id'=>$options[':id']));
			$count = $check_for_hash->fetch(PDO::FETCH_ASSOC);
			print_r($options);
			if($count['count'] == 0){
				$res = $new_device_stmt->execute($options);
				print("New!\n");
			} else{
				$res = $update_stmt->execute($options);
				print("Updating!\n");
			}

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
			$history_start_stmt->execute(array(':id'=>$options[':id']));
			$start = $history_start_stmt->fetch(PDO::FETCH_ASSOC);
			$start = $start['id'];
			print("Getting history\n");
			foreach ($hist as $call) {
				$history = explode("|", $call);
				if (count($history) > 5) {
					if ($history[0] > $start) {
						array_unshift($history, $options[':id']);

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
						$data[':11'] = to_seconds($data[':11']);
						$history_stmt->execute($data);
						//print_r($data);
					}	
					//print_r($history_stmt->errorInfo());echo"\n";
				}
				$print = false;
			}
			$update_time = microtime(true) - $update_start_time;
			//print_r($update_stmt->errorInfo());
			//print("Updated: " . $name . " at " . time() . "(Hash is ".$device['hash'] . " | generated: ".$id.")(quitting at " . $end . ")\n");
			if($res){
				//print(sprintf("%s:%s| Updated %s (%s)\n", $time, $worker_id,$device['id'], $name));
				$log_stmt->execute(array(
					':time'=>$time,
					':id'=>$worker_id,
					':message'=>"Updated",
					':detail'=>$device['id'] . " | Took ". $update_time ." seconds"
				));
			} else{
				//print(sprintf("Error updating %s:\n", $device['id']));print_r($update_stmt->errorInfo());
			}
			//echo $licensekey . "\n";
		}
		$cleanup->execute();
		$ssh = null;
	}
}

$log_stmt->execute(array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Closing",
						':detail'=>"Max Execution time reached"
					));