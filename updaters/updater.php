<?php

if(!isset($argv))
	die("Must be run from the command line");

if(!isset($argv) OR (isset($argv[1]) AND $argv[1] == "debug"))
	define("DEBUG", true);
else
	define("DEBUG", false);

if(!isset($argv))
	header("Content-Type: text/plain");

date_default_timezone_set("UTC");
$worker_id = getmypid().'-'.substr(sha1(rand(-1000,1000)), 0,5);
require_once 'mySSH.php';
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
	if(DEBUG)print("$name:");
	if(DEBUG)print_r($res);
	if(DEBUG)print("\n");
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





require dirname(__FILE__).'/../system/classes/loggedPDO.php';
//print($dbhost);
try {
	$db = new PDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
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
/*
$query = "SELECT count(distinct worker_id) AS count FROM updater_log WHERE `type` = 'updater' AND `time` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];
*/
$server_id = gethostname();
$current_devices = $redis->get("workers.$server_id.count");
if (($current_devices == $max_updaters OR $current_devices > $max_updaters) AND !DEV_ENV){
	die('Already at max updaters of ' . $max_updaters . " ( $current_devices )\n");
}
$redis->incr("workers.$server_id.count");
$redis->incr('workers.count');
$res = $db->query("SELECT value AS version FROM `settings` WHERE `setting` = 'worker_version'")->fetch(PDO::FETCH_ASSOC);
$worker_version = $res['version'];

$stmt = $db->prepare("SELECT CD.id,
CD.ip,
companies.active,
CD.password,
CD.own,
CD.verified,
CD.hash,
D.online,
D.serial,
D.updating,
D.updated,
D.incoming_total_bandwidth,
D.outgoing_total_bandwidth,
D.duration,
D.line_out_bass,
D.line_out_treble,
D.line_in_volume,
D.active_microphone_volume,
D.audio_mute_device,
D.voice_call_audio_output,
D.video_call_audio_output,
D.status_tone_volume,
D.ring_tone_volume,
D.dtmf_tone_volume
FROM companies_devices AS CD
INNER JOIN companies ON companies.id = CD.company_id
INNER JOIN devices AS D ON CD.hash = D.id
WHERE CD.id = :id");
/*
SELECT D.online, D.serial, D.updating, cd.ip, cd.password
FROM  `devices` AS D
LEFT JOIN companies_devices AS cd ON D.id = cd.hash
WHERE D.updating <= ( UNIX_TIMESTAMP( ) -30 ) 
AND D.updated <= ( UNIX_TIMESTAMP( ) -60 ) 
AND D.serial !=  'New Device'
ORDER BY D.updated
*/
$duration_stmt = $db->prepare("UPDATE devices SET duration = :duration WHERE id = :id");
$rsrv = $db->prepare("UPDATE devices SET updating = unix_timestamp() WHERE id = :id AND updating = :updating");
$offline_stmt = $db->prepare("UPDATE devices SET online = 0, updated = unix_timestamp() WHERE id = :id");
$online_stmt = $db->prepare("UPDATE devices SET online = 1, updated = unix_timestamp() WHERE id = :id");
$checked_stmt = $db->prepare("UPDATE companies_devices SET checked = unix_timestamp() WHERE id = :id");
$serial_stmt = $db->prepare("SELECT * FROM devices WHERE `serial` = :serial");
$new_serial = $db->prepare("UPDATE devices SET `serial` = :serial WHERE id = :id");
$new_license = $db->prepare("UPDATE devices SET `licensekey` = :license WHERE id = :id");
$change_stmt = $db->prepare("UPDATE companies_devices SET device_id = :id WHERE device_id = :old_id AND company_id = :company");
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
	camera_far_set_preset = :far_set,
	line_out_treble=:line_out_treble,
	line_out_bass=:line_out_bass,
	active_microphone_volume=:active_microphone_volume,
	line_in_volume=:line_in_volume,
	audio_mute_device=:audio_mute_device,
	voice_call_audio_output=:voice_call_audio_output,
	video_call_audio_output=:video_call_audio_output,
	status_tone_volume=:status_tone_volume,
	ring_tone_volume=:ring_tone_volume,
	dtmf_tone_volume=:dtmf_tone_volume
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
	camera_far_set_preset = :far_set,
	line_out_treble=:line_out_treble,
	line_out_bass=:line_out_bass,
	active_microphone_volume=:active_microphone_volume,
	line_in_volume=:line_in_volume,
	audio_mute_device=:audio_mute_device,
	voice_call_audio_output=:voice_call_audio_output,
	video_call_audio_output=:video_call_audio_output,
	status_tone_volume=:status_tone_volume,
	ring_tone_volume=:ring_tone_volume,
	dtmf_tone_volume=:dtmf_tone_volume");
$check_for_hash = $db->prepare("SELECT count(*) AS count FROM devices WHERE id = :id");
$update_stmt2 = $db->prepare("UPDATE companies_devices SET hash = :hash WHERE id = :id");
$cleanup = $db->prepare("UPDATE devices SET online=0, licensekey='', updated = 0, updating=0, `serial` = 'New Device' WHERE id = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'");
$log_stmt = $db->prepare("INSERT INTO updater_log (time, worker_id, message, detail, type) VALUES (:time, :id, :message, :detail, 'updater')");
$update_log_stmt = $db->prepare("INSERT INTO updater_log (time, worker_id, message, detail, type, update_time) VALUES (:time, :id, :message, :detail, 'updater',:update_time)");
$history_start_stmt = $db->prepare("SELECT id FROM devices_history WHERE device_id = :id ORDER BY id DESC limit 1");
$history_stmt = $db->prepare("INSERT INTO devices_history VALUES(:1,:2,:3,:4,:5,:6,:7,:8,:9,:10,:11,:12,:13,:14,:15,:16,:17,:18,:19,:20,:21,:22,:23,:24,:25,:26,:27,:28,:29,:30,:31,:32,:33,:34,:35,:36,:37,:38,:39,:40,:41,:42,:43,:44,:45,:46,:47,:48,:49, :50)");
$update_device_hash = $db->prepare("UPDATE companies_devices SET hash = :hash WHERE id = :id");
$log_stmt->execute(array(
		':time'=>time(),
		':id'=>$worker_id,
		':message'=>"Initialized",
		':detail'=>''
	));

$cleanup_log_stmt = $db->prepare("DELETE FROM updater_log WHERE `time` < (:time - 86400)");
$check_offline_alarm = $db->prepare("SELECT count(*) AS count FROM devices_alarms WHERE device_id = :id AND alarm_id = 'alarm-jfu498hf'");
$new_offline_alarm = $db->prepare("INSERT INTO devices_alarms (`active`,`device_id`,`alarm_id`)
VALUES(:active, :id, 'alarm-jfu498hf')");
$update_offline_alarm = $db->prepare("UPDATE devices_alarms SET active = :active
	WHERE device_id = :id AND alarm_id = 'alarm-jfu498hf'");
$high_loss_stmt = $db->prepare("UPDATE devices_alarms SET active = :active WHERE device_id = :id AND alarm_id = 'alarm-abwo7froseb'");
$get_edits_stmt = $db->prepare("SELECT * FROM edits WHERE device_id = :id AND completed = 0 ORDER BY added");
$edit_completed_stmt = $db->prepare("UPDATE edits SET completed = 1 WHERE id = :id");


$start = time();
$max_runtime = (60 * 60) + rand(0,6000);
if(DEV_ENV){
	$max_runtime = 120;
}
$end = $start + $max_runtime;

while(time() <= $end){
	$times = array();
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
		break;
	}
	$log_stmt->execute(array(
		':time'=>$time,
		':id'=>$worker_id,
		':message'=>"Checking for available device",
		':detail'=>''
	));
	//print("$time: Checking for available device\n");

	$device = $redis->brpoplpush('updates','updates', 5);
	//print_r($device);
	if(!is_null($device)){
		
		//print("\tUpdating $device!\n");
		$hash = $device;
		$stmt->execute(array(':id'=>$hash));
		//print("id: $hash\n");
		$device = $stmt->fetch(PDO::FETCH_ASSOC);
		//print_r($device);
		if($device['active'] == 0){
			print(microtime(true) . " | Inactive company, skipping " . $device['name'] . "\n");
			continue;
		}
		$duration = $device['duration'];
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
			//print("Locked!\n");

			
			if(DEBUG)print($device['id'] . "\n");
			$update_start_time = microtime(true);
			$ssh = new mySSH($device['ip']);
			if($ssh===false){
				$offline_stmt->execute(array(':id'=>$device['hash']));
			}
			$pw = ($device['password'] != '')?$device['password'] : 'lifesize';
			//if(DEBUG) print("Logging in with auto: $pw\n");

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
					$check_offline_alarm->execute(array(':id'=>$device['id']));
					$res = $check_offline_alarm->fetch(PDO::FETCH_ASSOC);
					if($res['count'] != 0){
						$update_offline_alarm->execute(array(
							':id'=>$device['id'],
							':active'=>1
						));
					}else{
						$new_offline_alarm->execute(array(
							':id'=>$device['id'],
							':active'=>1
						));
					}
				}
			} else {
				$check_offline_alarm->execute(array(':id'=>$device['id']));
					$res = $check_offline_alarm->fetch(PDO::FETCH_ASSOC);
					if($res['count'] != 0){
						$update_offline_alarm->execute(array(
							':id'=>$device['id'],
							':active'=>0
						));
					}else{
						$new_offline_alarm->execute(array(
							':id'=>$device['id'],
							':active'=>0
						));
					}
				$res = explode(chr(0x0a), $ssh->exec("get system serial"));
				$serial = $res[0];

				$device_id = sha1($serial);

				if($device['hash'] != $device_id){
					$update_device_hash->execute(array(':hash'=>$device_id, ':id'=>$device['id']));
					continue;
				}
				$updated = time() - $device['updated'];
				if($updated < 60){
					if(DEBUG) print("Device was updated $updated seconds ago\n");
					sleep(5);
					continue;
				}

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
				//print_r($edits);
				
				//print_r($options);
				//print(sprintf("New data for %s is\n\tOnline: %s\n\tName: %s\n\tMake: %s\n\tModel: %s\n\tIn Call:%s\n\tVersion:%s\n", $device['name'], $online, $name, $make, $model, $in_call, $version));
				/*
				*	START GET CONFIG
				*/
				$settings = array();
				$options = array(
					':id'=>'',					
					':name'=>'',
					':make'=>'',
					':model'=>'',
					':call'=>'',
					':version'=>'',
					':license'=>'',
					':type'=>'',
					':serial'=>'',
					':online'=>'',
					':auto_answer'=>'',
					':auto_answer_mute'=>'',
					':incoming_call'=>'',
					':outgoing_call'=>'',
					':incoming_total'=>'',
					':outgoing_total'=>'',
					':auto_bw'=>'',
					':max_calltime'=>'',
					':max_redials'=>'',
					':auto_multiway'=>'',
					':codecs'=>'',
					':active_mic'=>'',
					':lock'=>'',
					':telepresence'=>'',
					':far_control'=>'',
					':far_use'=>'',
					':far_set'=>'',
					':line_out_treble'=>'',
					':line_out_bass'=>'',
					':active_microphone_volume'=>'',
					':line_in_volume'=>"0",
					':audio_mute_device'=>"",
					':video_call_audio_output'=>'',
					':voice_call_audio_output'=>'',
					':status_tone_volume'=>'',
					':ring_tone_volume'=>'',
					':dtmf_tone_volume'=>'',
				);
				$entries = array(
					':active_mic'=>'active-mic',
					':auto_answer'=>'auto-answer',
					':auto_answer_mute'=>'auto-mute',
					'max'=>'max-speed',
					'total'=>'total-bw',
					':auto_bw'=>'auto-bandwidth',
					':max_calltime'=>'max-time',
					':max_redials'=>'max-redial-entries',
					':auto_multiway'=>'auto-multiway',
					':codecs'=>'codecs',
					':audio_active_microphone'=>'active-mic',
					':telepresence'=>'telepresence',
					':lock'=>'lock',
					':far_control'=>'far-control',
					':far_set'=>'far-set-preset',
					':far_use'=>'far-use-preset',
					':audio_mute_device'=>'mute-device',
					':voice_call_audio_output'=>'audio-output',
					':video_call_audio_output'=>'video-output',
					':dtmf_tone_volume'=>'dtmf',
					':ring_tone_volume'=>'ring-tone',
					':status_tone_volume'=>'status-tone',
					'eq'=>'eq',
					':active_microphone_volume'=>'gain',
					':line_in_volume'=>'line-in'
				);
				$res = $ssh->exec("get config");
				$array = explode("\n", $res);
				foreach($array as $entry){
					//print("$entry\n");
					if(substr($entry, 0, 1)!= '#'){
						$tmp = explode(" ", $entry);
						$key = (isset($tmp[2]))?array_search($tmp[2], $entries):false;
						if($tmp[0] == "set" and !($key===false)){
							$tmp = explode(' ',$entry);
							$name = $tmp[1];
							unset($tmp[0]);
							unset($tmp[1]);
							$setting = $tmp[2];
							unset($tmp[2]);
							$vals = str_replace('"', '', implode(' ',$tmp));
							$settings[$name][$key] = $vals;
						}
					}
				}
				//if(DEBUG) print_r($settings);
				foreach($settings as $cat=>$cats){
					foreach($cats as $name=>$setting){
						if($cat == "audio" AND $name == ":codecs"){
							$settings['audio'][':codecs'] = json_encode(explode(' ', $setting));
						}
						if($cat == "call" AND $name == "max"){
							unset($settings['call']['max']);
							$res = explode(' ',$setting);
							$settings['call'][':incoming_call'] = ($res[1] == "auto")?0:$res[1];
							$settings['call'][':outgoing_call'] = ($res[3] == "auto")?0:$res[3];
						}
						if($cat == "call" AND $name == "total"){
							unset($settings['call']['total']);
							$res = explode(' ',$setting);
							$settings['call'][':incoming_total'] = ($res[1] == "" OR $res[1] == "auto")?0:$res[1];
							$settings['call'][':outgoing_total'] = ($res[3] == "" OR $res[1] == "auto")?0:$res[3];
						}
						if($cat == "audio" AND $name == "eq"){
							unset($settings['audio']['eq']);
							$res = explode(' ',$setting);
							$settings['audio'][':line_out_bass'] = $res[1];
							$settings['audio'][':line_out_treble'] = $res[3];
						}
						if($cat == "camera" AND $name == "lock"){
							$res = explode(' ', $setting);
							$settings['camera']['lock'] = $res[2];
						}
						
					}
				}
				foreach($settings as $cat=>$cats){
					foreach($cats as $name=>$setting){
						$options[$name]=$settings[$cat][$name];
					}
				}


				/*
				*	END GET CONFIG
				*/

				$online = 1;	
				//get licensekey from device
				$res = ($ssh->exec('get system licensekey -t maint'));
				if($res){
					$licensekey = explode(chr(0x0a), $res);
					$licensekey = $licensekey[0];
					$new_license->execute(array(':license'=>$licensekey,':id'=>$device['hash']));
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
				if(array_search('ok,00', $res) AND $res[0] == ''){
					$in_call = ($res[0] == '')?0 : 1;
					$high_loss_stmt->execute(array(
						':id'=>$device['id'],
						':active'=>0
					));
				} else {
					$in_call = 1;
					/*
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
					*/
				}
				$type = "camera";

				$update_time = microtime(true) - $update_start_time;
				$options[':id']=sha1($serial);				
				$options[':name']=$name;
				$options[':make']=$make;
				$options[':model']=$model;
				$options[':call']=$in_call;
				$options[':version']=$version;
				$options[':license']=$licensekey;
				$options[':type']=$type;
				$options[':serial']=$serial;
				$options[':online']=$online;
				
				
				$update_options = array(
					':hash'=>$options[':id'],
					':id'=>$device['id']
				);
				$update_stmt2->execute($update_options);
				$check_for_hash->execute(array(':id'=>$options[':id']));
				$count = $check_for_hash->fetch(PDO::FETCH_ASSOC);

				//if(DEBUG) print_r($options);
				//if(DEBUG) print("Count: " .count($options) . "\n");
				
				if($count['count'] == 0){
					$res = $new_device_stmt->execute($options);
					//print_r($new_device_stmt->errorInfo());
					//print("New!\n");
				} else{
					$res = $update_stmt->execute($options);
					//print_r($update_stmt->errorInfo());
					//print("Updating!\n");
				}
				
				//if(DEBUG) break;
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
				//print("Getting history\n");
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
							$duration += $data[':11'];
							$history_stmt->execute($data);
							//print_r($data);
						}	
						//print_r($history_stmt->errorInfo());echo"\n";
					}
					$print = false;
				}

				$duration_stmt->execute(array(':duration'=>$duration, ':id'=>$options[':id']));
				//print_r($duration_stmt->errorInfo());
				$update_time = microtime(true) - $update_start_time;
				$times['update-time'] = $update_time;
				//print_r($update_stmt->errorInfo());
				//print("Updated: " . $name . " at " . time() . "(Hash is ".$device['hash'] . " | generated: ".$id.")(quitting at " . $end . ")\n");
				if($res){
					//print(sprintf("%s:%s| Updated %s (%s)\n", $time, $worker_id,$device['id'], $name));
					
					$updated_log_stmt = array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Updated",
						':detail'=>$device['id'],
						':update_time'=>$update_time
				} else{
					//print(sprintf("Error updating %s:\n", $device['id']));print_r($update_stmt->errorInfo());
				}
				
			}
			//echo $licensekey . "\n";
		}
		$cleanup->execute();
		$ssh = null;
		if(DEBUG){
			print_r($times);
		}
	}
}

$redis->decr('workers.count');
$redis->decr("workers.$server_id.count");
$log_stmt->execute(array(
						':time'=>$time,
						':id'=>$worker_id,
						':message'=>"Closing",
						':detail'=>"Max Execution time reached"
					));

print("Closing!\n");
//if(DEBUG)	print_r($db->printlog());
print("\n");