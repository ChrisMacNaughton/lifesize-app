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
if($res['value'] != 1){
	exit();
}

$res = $db->query("SELECT value FROM settings WHERE setting = 'max_updaters'")->fetch(PDO::FETCH_ASSOC);
$max_updaters = $res['value'];
$time = (int)time() - 60;
$query = "SELECT count(distinct worker_id) AS count FROM updater_log WHERE `type` = 'updater' AND `time` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];

if ($current_devices == $max_updaters OR $current_devices > $max_updaters){
	die('Already at max updaters of ' . $max_updaters . " ( $current_devices )\n");
}

$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
$worker_version = $res['version'];

$stmt = $db->prepare("SELECT CD.id, CD.ip, CD.password, CD.own, CD.verified, CD.hash, D.online, D.serial, D.updating
FROM companies_devices AS CD
INNER JOIN devices AS D ON CD.hash = D.id
INNER JOIN companies ON CD.company_id = companies.id
WHERE D.updated <= ( UNIX_TIMESTAMP( ) - companies.interval *60 -5 ) 
AND D.updating < ( UNIX_TIMESTAMP( ) -30 ) 
ORDER BY D.updated
LIMIT 1");
$rsrv = $db->prepare("UPDATE devices SET updating = unix_timestamp() WHERE id = :id AND updating = :updating");
$offline_stmt = $db->prepare("UPDATE devices SET online = 0, updated = unix_timestamp() WHERE id = :id");
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
	//print_r($device);
	//print_r($stmt->errorInfo());
	if(empty($device)){
		sleep(5);
		continue;
	}
	print("Trying to reserve " . $device['id'] . "\n");
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
		print("Succeeded!\n");

		$ssh = new mySSH($device['ip']);
		$pw = ($device['password'] != '')?$device['password'] : 'lifesize';
		if(!$ssh->login('auto', $pw)){
			$offline_stmt->execute(array(':id'=>$device['hash']));
			print_r($offline_stmt->errorInfo());
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
			$offline_alarm->execute(array(
					':id'=>$device['id'],
					':active'=>0
				));
			$res = explode(chr(0x0a), $ssh->exec("get system serial"));
			$serial = $res[0];

			$id = sha1($serial);

			//echo $serial . " => " . $id;exit("\n\n");
			
			$get_edits_stmt->execute(array(':id'=>$device['id']));
			$edits = $get_edits_stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($edits as $edit){
				$command = $edit['verb'] . ' ' . $edit['object'] . ' ' . $edit['target'] . ' ' . $edit['details'];
				$res = explode(chr(0x0a), $ssh->exec($command));
				print("Edit: ".$command . "\n");
				if(array_search('ok,00', $res)){
					$edit_completed_stmt->execute(array(':id'=>$edit['id']));
				}
			}
			//print_r($edits);
			$online = 1;	
			//get licensekey from device
			$res = ($ssh->exec('get system licensekey -t maint'));
			if($res){
				$licensekey = explode(chr(0x0a), $res);
				$licensekey = $licensekey[0];
				$new_license->execute(array(':license'=>$licensekey,':id'=>$device['id']));
			}
			//get name from device
			$name = assign('get system name', 'name', $device);
			
			$res = clean($ssh->exec('get system model'));
			if($res){
				$dev = explode(',', $res);
				$make = $dev[0]; $model = $dev[1];
			} else {
				$make = $device['make'];
				$model = $device['model'];
			}
			$res = explode(chr(0x0a), $ssh->exec('get system version'));
			if(array_search('ok,00', $res)){
				$res = explode(',', $res[0]);
				$version = $res[1];
			} else {
				$version = $device['version'];
			}
			$res = explode(chr(0x0a), $ssh->exec('status call active'));
			if(array_search('ok,00', $res)){
				$in_call = ($res[0] == '')?0 : 1;
				$high_loss_stmt->execute(array(
					':id'=>$device['id'],
					':active'=>0
				));
			} else {
				$in_call = $device['in_call'];
				$active_call = explode(chr(0x0a), $ssh->exec('status call active'));
				$active_call = explode(',',$active_call[0]);
				$call_time = to_seconds($active_call[10]);

				$call_stats = explode(chr(0x0a), $ssh->exec('status call statistics'));
				$call_stats = explode(',',$call_stats[0]);

				$cumu_pkt_loss = $call_stats[12] + $call_stats[17] + $call_stats[21] + $call_stats[27];

				$pkt_loss = $call_stats[11] + $call_stats[16] + $call_stats[20] + $call_stats[26];

				$loss_per_sec = $cumu_pkt_loss / $call_time;

				if($loss_per_sec > 5){
					$high_loss_stmt->execute(array(
						':id'=>$device['id'],
						':active'=>1
					));
				} else {
					$high_loss_stmt->execute(array(
						':id'=>$device['id'],
						':active'=>0
					));
				}
			}
			//get auto-answer from device
			$auto_answer = assign('get call auto-answer', 'auto_answer', $device);

			$auto_answer_mute = assign('get call auto-mute', 'auto_answer_mute', $device);
			//get max-call-speed-mute from device
			$res = clean($ssh->exec('get call max-speed'));
			if($res){
				$res = explode(',', $res);
				$incoming_call_bandwidth = ($res[0] != "auto")?$res[0]:0;
				$outgoing_call_bandwidth = ($res[1] != "auto")?$res[1]:0;
			} else {
				$incoming_call_bandwidth = $device['incoming_call_bandwidth'];
				$outgoing_call_bandwidth = $device['outgoing_call_bandwidth'];
			}
			//get max-bw from device
			$res = clean($ssh->exec('get call total-bw'));
			if($res){
				$res = explode(',', $res);
				$incoming_total_bandwidth = ($res[0] != "")?$res[0]:0;
				$outgoing_total_bandwidth = ($res[1] != "")?$res[1]:0;
			} else {
				$incoming_total_bandwidth = $device['incoming_total_bandwidth'];
				$outgoing_total_bandwidth = $device['outgoing_total_bandwidth'];
			}
			//auto-bandwitdh
			$res = clean($ssh->exec('get call auto-bandwidth'));
			if($res){
				$auto_bandwidth = ($res == 'on')?'on':'off';
			} else {
				$auto_bandwidth = $device['auto_bandwidth'];
			}
			//max_calltime
			$max_calltime = assign('get call max-time', 'max_calltime', $device);

			//max_redials
			$max_redials = assign('get call max-redial-entries','max_redials', $device);
			
			//auto_multiway
			$auto_multiway = assign('get call auto-multiway', 'auto_multiway', $device);

			//audio_codecs
			$res = clean($ssh->exec('get audio codecs'));
			if($res){
				$res = explode(' ', rtrim($res));
				//$res = 
				$codecs = json_encode($res);
			} else {
				$codecs = $device['audio_codecs'];
			}

			//audio active mic
			$active_mic = assign('get audio active-mic', 'audio_active_microphone', $device);
			
			$telepresence = assign('get system telepresence', 'telepresence', $device);

			$lock = assign("get camera lock", 'camera_lock', $device);

			$far_control = assign("get camera far-control", 'camera_far_control', $device);

			$far_set_preset = assign("get camera far-set-preset", 'camera_far_set_preset', $device);

			$far_use_preset = assign("get camera far-use-preset", 'camera_far_use_preset', $device);

			if(strpos($lock, ',')){
				$lock = explode(',',$lock);
				$lock = $lock[1];
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
						$data[':11'] = to_seconds($data[':11']);
						$history_stmt->execute($data);
						//print_r($data);
					}	
					//print_r($history_stmt->errorInfo());echo"\n";
				}
				$print = false;
			}
			$type = "camera";
			//print(sprintf("New data for %s is\n\tOnline: %s\n\tName: %s\n\tMake: %s\n\tModel: %s\n\tIn Call:%s\n\tVersion:%s\n", $device['name'], $online, $name, $make, $model, $in_call, $version));
			$options = array(
				':id'=>$id,					
				':name'=>$name,
				':make'=>$make,
				':model'=>$model,
				':call'=>$in_call,
				':version'=>$version,
				':license'=>$licensekey,
				':type'=>$type,
				':online'=>$online,
				':auto_answer'=>$auto_answer,
				':auto_answer_mute'=>$auto_answer_mute,
				':incoming_call'=>$incoming_call_bandwidth,
				':outgoing_call'=>$outgoing_call_bandwidth,
				':incoming_total'=>$incoming_total_bandwidth,
				':outgoing_total'=>$outgoing_total_bandwidth,
				':auto_bw'=>$auto_bandwidth,
				':max_calltime'=>$max_calltime,
				':max_redials'=>$max_redials,
				':auto_multiway'=>$auto_multiway,
				':codecs'=>$codecs,
				':active_mic'=>$active_mic,
				':lock'=>$lock,
				':telepresence'=>$telepresence,
				':far_control'=>$far_control,
				':far_use'=>$far_use_preset,
				':far_set'=>$far_set_preset
			);
			//print_r($options);
			$res = $update_stmt->execute($options);
			//print_r($update_stmt->errorInfo());
			//print("Updated: " . $name . " at " . time() . "(quitting at " . $end . ")\n");
			if($res){
				//print(sprintf("%s:%s| Updated %s (%s)\n", $time, $worker_id,$device['id'], $name));
				$log_stmt->execute(array(
					':time'=>$time,
					':id'=>$worker_id,
					':message'=>"Updated",
					':detail'=>$device['id']
				));
			} else{
				//print(sprintf("Error updating %s:\n", $device['id']));print_r($update_stmt->errorInfo());
			}
			//echo $licensekey . "\n";
		}
		$ssh = null;
	}
}

$log_stmt->execute(array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Closing",
						':detail'=>"Max Execution time reached"
					));