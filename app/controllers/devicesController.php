<?php

class devicesController extends Controller {
	public function indexAction(){
		$data = array(
			'title'=>"Devices"
		);
		$data['headercolor'] = '99ff99';
		$data['devices'] = $this->user->devices;
		$comp = $this->user->getCompanyDetails();
		$data['subscription'] = $comp['plan_id'];
		$this->render('devices/index.html.twig', $data);
	}
	public function viewAction($id){
		$data=array(
			'headercolor'=>'66cc66',
		);
		if(!isset($this->user->devices[$id])){
			$_SESSION['errors'][] = "You don't have permission to view that device";
			session_write_close();
			header("Location: ".PROTOCOL.ROOT."/devices");
		}
		if($this->redis){
			$data['average_loss'] = json_decode($this->redis->get('cache.averages'), true);
		} else {
			$totals = $this->db->query("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
			FROM devices_history
			INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
			WHERE devices_history.duration > 0")->fetch(PDO::FETCH_ASSOC);

			$d = ($totals['Duration'] / 60);
			$averages['RxV1'] = $totals['RxV1'] / $d;
			$averages['RxA1'] = $totals['RxA1'] / $d;
			$averages['RxV2'] = $totals['RxV2'] / $d;
			$averages['TxV1'] = $totals['TxV1'] / $d;
			$averages['TxA1'] = $totals['TxA1'] / $d;
			$averages['TxV2'] = $totals['TxV2'] / $d;
			$data['average_loss'] = $averages;
		}

		$time_limit = 0;
		$loss7 = array();
		$loss30 = array();
		$loss60 = array();
		$loss90 = array();
		$loss120 = array();
		$loss = array();
		$call_counts = array();
		$min_loss = -0.1;
		/*
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.Duration > $time_limit");
		$stmt->execute(array(':id'=>$id));
		$losses = $stmt->fetch(PDO::FETCH_ASSOC);
		$duration = $losses['Duration'] / 60;
		unset($losses['Duration']);
		foreach($losses as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$l[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			} else {
				unset($data['average_loss'][$name]);
			}
		}

		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][0] = $res['count'];

		*/

		/*
		*	Last 7 days
		*/
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(7*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);

		//print_r($stmt->errorInfo());
		$duration = $res['Duration'] / 60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$loss7[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			}
		}
		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(7*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][7] = $res['count'];
		/*
		*	Last 30 days
		*/
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(30*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		//print_r($stmt->errorInfo());
		$duration = $res['Duration'] / 60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$loss30[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			}
		}

		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(30*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][30] = $res['count'];
		/*
		*	Last 60 days
		*/
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(60*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		//print_r($stmt->errorInfo());
		$duration = $res['Duration'] / 60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$loss60[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			}
		}
		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.StartTime > from_unixtime(unix_timestamp()-(60*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][60] = $res['count'];
		/*
		*	Last 90 days
		*/
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id AND devices_history.StartTime > from_unixtime(unix_timestamp()-(90*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		//print_r($stmt->errorInfo());
		$duration = $res['Duration'] / 60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$loss90[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			}
		}
		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.StartTime > from_unixtime(unix_timestamp()-(90*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][90] = $res['count'];

		/*
		*	Last 120 days
		*/
		$stmt = $this->db->prepare("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM(  `Duration` ) AS Duration
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.StartTime > from_unixtime(unix_timestamp()-(120*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);

		//print_r($stmt->errorInfo());
		$duration = $res['Duration'] / 60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if($duration > 0 AND $loss / $duration > $min_loss){
				$loss120[$name] = array("name"=>$name, "loss"=>$loss / $duration);
			}
		}
		$stmt = $this->db->prepare("SELECT count(*) AS count
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id =:id
AND devices_history.StartTime > from_unixtime(unix_timestamp()-(120*24*60*60))
AND devices_history.duration > $time_limit");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_counts'][120] = $res['count'];

		/*
		*	Last Call
		*/
		$stmt = $this->db->prepare("SELECT  `RxV1PktsLost` AS RxV1,  `RxA1PktsLost` AS RxA1,  `RxV2PktsLost` AS RxV2,  `TxV1PktsLost` AS TxV1,  `TxA1PktsLost` AS TxA1,  `TxV2PktsLost` AS TxV2,  `Duration`
FROM devices_history
INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
WHERE companies_devices.id = :id
AND devices_history.duration > $time_limit
ORDER BY devices_history.id DESC
LIMIT 1
");
		$stmt->execute(array(':id'=>$id));

		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$duration = $res['Duration'];
		$d = $duration/60;
		unset($res['Duration']);
		foreach($res as $name=>$loss){
			if(isset($loss120[$name]) OR isset($loss90[$name]) OR isset($loss60[$name]) OR isset($loss30[$name]))
				$loss0[$name] = array("name"=>$name, "loss"=>($d > 0)?$loss / $d:0);
		}

		$duration_scale = "seconds";
		if($duration > 60){
			$duration = $duration / 60;
			$duration_scale = "minutes";
			if($duration > 60){
				$duration = $duration / 60;
				$duration_scale = "hours";
					if($duration > 24){
					$duration = $duration / 24;
					$duration_scale = "days";
				}
			}
		}


		/*
		*	Active Call
		*/
		$stmt = $this->db->prepare("SELECT active_calls.*
FROM active_calls
INNER JOIN companies_devices ON companies_devices.hash = active_calls.device_id
WHERE companies_devices.id = :id
ORDER BY active_calls.id DESC
LIMIT 1
");
		$stmt->execute(array(':id'=>$id));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);

		if(count($res) > 0 AND $res){
			$active_duration = (int)$res['Duration'];
			$d = $active_duration/60;
			//unset($res['Duration']);
			foreach($res as $name=>$loss){
				$active_call[$name] =$loss;
			}
		} else {
			$active_duration = 0;
			$active_call=false;
		}
		$active_duration_scale = "seconds";
		if($active_duration > 60){
			$active_duration = $active_duration / 60;
			$active_duration_scale = "minutes";
			if($active_duration > 60){
				$active_duration = $active_duration / 60;
				$active_duration_scale = "hours";
				if($active_duration > 24){
					$active_duration = $active_duration / 24;
					$active_duration_scale = "days";
				}
			}
		}


		/*
		*	Finished with history queries
		*/
		$data['packetnames'] = array(
			'TxV1'=>'Video1 Transmit',
			'TxA1'=>'Audio Transmit',
			'TxV2'=>'Video2 Transmit',
			'RxV1'=>'Video1 Receive',
			'RxA1'=>'Audio Receive',
			'RxV2'=>'Video2 Receive'
		);


		$data['loss0'] = $loss0;
		$data['loss7'] = $loss7;
		$data['loss30'] = $loss30;
		$data['loss60'] = $loss60;
		$data['loss90'] = $loss90;
		$data['loss120'] = $loss120;
		$data['duration']['count'] = $duration;
		$data['duration']['scale'] = $duration_scale;
		$data['active_duration']['count'] = $active_duration;
		$data['active_duration']['scale'] = $active_duration_scale;
		$data['active_call'] = $active_call;
		foreach($data as $key=>$val){
			if(is_array($val))
				ksort($data[$key]);
		}
		/*
		ksort($data['loss0']);
		ksort($data['loss1']);
		ksort($data['loss7']);
		ksort($data['loss30']);
		ksort($data['loss60']);
		ksort($data['loss90']);
		ksort($data['average_loss']);
		ksort($data['loss120']);
		*/
		$data['loss_names'] = array_keys($data['average_loss']);
		$data['device'] = $this->user->devices[$id];
		if($this->redis){
			$avg = json_decode($this->redis->get('cache.device_averages'), true);
			$data['device_averages'] = $avg[$data['device']['model']];
		} else {
			$averages = array();
			$model = $data['device']['model'];
			$totals = $this->db->query("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM( devices_history.duration ) AS Duration
			FROM devices_history
			INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
			INNER JOIN devices ON devices.id = devices_history.device_id
			WHERE devices_history.duration >0
			AND devices.model = '$model'")->fetch(PDO::FETCH_ASSOC);
			echo "<!--";print("SELECT SUM(  `RxV1PktsLost` ) AS RxV1, SUM(  `RxA1PktsLost` ) AS RxA1, SUM(  `RxV2PktsLost` ) AS RxV2, SUM(  `TxV1PktsLost` ) AS TxV1, SUM( `TxA1PktsLost` ) AS TxA1, SUM(  `TxV2PktsLost` ) AS TxV2, SUM( devices_history.duration ) AS Duration
			FROM devices_history
			INNER JOIN companies_devices ON companies_devices.hash = devices_history.device_id
			INNER JOIN devices ON devices.id = devices_history.device_id
			WHERE devices_history.duration >0
			AND devices.model = '$model'");echo"-->";
				$d = ($totals['Duration'] / 60);
			$averages['RxV1'] = $totals['RxV1'] / $d;
			$averages['RxA1'] = $totals['RxA1'] / $d;
			$averages['RxV2'] = $totals['RxV2'] / $d;
			$averages['TxV1'] = $totals['TxV1'] / $d;
			$averages['TxA1'] = $totals['TxA1'] / $d;
			$averages['TxV2'] = $totals['TxV2'] / $d;
			$data['device_averages'] = $averages;
		}
		$this->render('devices/view.html.twig', $data);
	}
	public function verifyAction($id){
		$data = array(
			'headercolor'=>'00ff00'
		);
		$data['device'] = $this->user->devices[$id];
		if($data['device']['verified'] == 1){
			header("Location: ".PROTOCOL.ROOT .'/devices/view/' . $id);
			exit(0);
		}
		if(isset($_POST['confirm']) && $_POST['confirm'] == 'true'){
			$confirm = substr( sha1($id . $this->user->getCompany() . microtime(true)), 0,8);
			$options = array(
				'code'=>$confirm,
				'id'=>$id
			);
			$this->sqs->send_message(VERIFY_URL, json_encode($options));
			$stmt = $this->db->prepare("UPDATE companies_devices SET verify_code = :confirm, verify_sent = 1 WHERE device_id = :device AND company_id = :company");
			$stmt->execute(array(
				':confirm'=>$confirm,
				':device'=>$id,
				':company'=>$this->user->getCompany()
			));
			$this->user->updateDevices();
			$data['device'] = $this->user->devices[$id];
		}
		if(isset($_POST['verify']) && $_POST['verify'] == 'true'){
			if($_POST['code'] == $data['device']['verify_code']){
				$stmt = $this->writedb->prepare("UPDATE companies_devices SET verified = 1 WHERE company_id = :comp AND device_id = :dev");
				$res = $stmt->execute(array(
					':comp'=>$this->user->getCompany(),
					':dev'=>$id
				));
				if($res){
					$this->user->updateDevices();
					$data['device'] = $this->user->devices[$id];
				}
			}
		}
		$this->render("devices/verify.html.twig", $data);
	}
	public function editAction($id){
		if($this->user->getPlan() == "Basic"){
			$_SESSION['errors'][] = "You mumst upgrade your subscription to edit devices";
			session_write_close();
			header("Location: ".PROTOCOL.ROOT."/devices/view/".$id);
			exit();
		}
		$data = array(
			'headercolor'=>'66ff66'
		);
		if(!isset($this->user->devices[$id])){
			$_SESSION['errors'][] = "You don't have permission to view that device";
			session_write_close();
			header("Location: ".PROTOCOL.ROOT."/devices");
		}
		$data['device'] = $this->user->devices[$id];
		if(isset($_POST['section'])){
			$section = $_POST['section'];
			unset($_POST['section']);
			$time = time();
			$edited = false;
			$edit_stmt = $this->db->prepare("INSERT INTO edits (id, device_id, verb, object, target, details,added, by) VALUES (:id, :device, :verb, :object, :target, :details, :added, :user)");
			switch($section){
				case "calls":
					//echo "<!--";print_r($_POST);echo "-->";
					$options = array(
						':device'=>$id,
						':verb'=>'set',
						':object'=>'call',
						':by'=>$this->user->getID()
					);
					foreach($_POST as $key=>$var){
						if($data['device'][$key] != $var){
							$options[':id']='edit-' . substr(hash('sha512', $id . microtime(true)), 0,10);
							$options[':details']=$var;
							$options[':added']=$time;
							$edited = true;
							switch($key){
								case "outgoing_call_bandwidth":
									$options[':target']='max-speed -o';
									break;
								case "incoming_call_bandwidth":
									$options[':target']='max-speed -i';
									break;
								case "outgoing_total_bandwidth":
									$options[':target']='total-bw -o';
									break;
								case "incoming_total_bandwidth":
									$options[':target']='total-bw -i';
									break;
								case "auto_bandwidth":
									$options[':target'] = 'auto-bandwidth';
									break;
								case "max_calltime":
									$options[':target'] = 'max-time';
									break;
								case "max_redials":
									$options[':target'] = 'max-redial-entries';
									break;
								case "auto_answer":
									$options[':target'] = 'auto-answer';
									break;
								case "auto_answer_mute":
									$options[':target'] = 'auto-mute';
									break;
								case "auto_answer_multiway":
									$options[':target'] = 'auto-multiway';
									break;
							}
							$edit_stmt->execute($options);
						}
					}
					break;
				case "audio":
					$options = array(
						':device'=>$id,
						':verb'=>'set',
						':object'=>'audio',
						':by'=>$this->user->getID()
					);
					foreach($_POST as $key=>$var){
						if($key == 'audio_codecs')
							$key = 'audio_codecs_short';
						echo"<!--$key\n";
						print_r($data['device'][$key]);
						print_r($var);
						echo"-->";
						if($data['device'][$key] != $var AND $data['device'][$key]){
							$options[':id']='edit-' . substr(hash('sha512', $id . microtime(true)), 0,10);
							$options[':details']=$var;
							$options[':added']=$time;
							$edited = true;
							switch($key){
								case "audio_active_microphone":
									$options[':target']='active-mic';
									break;
								case "audio_codecs":
									$options[':target']='codecs';
									$options[':details'] = implode(' ',$var);
									break;
								case "active_microphone_volume":
									$options[':target']="gain";
									$options[':details']=$var;
									break;
								case "line_in_volume":
									$options[':object']="volume";
									$options[':target']="line-in";
									$options[':details']=$var;
									break;
								case "audio_mute_device":
									$options[':target']="mute-device";
									$options[':details']=$var;
									break;
								case "video_call_audio_output":
									$options[':target']='video-output';
									$options[':details']=$var;
									break;
								case "audio_call_audio_output":
									$options[':target']='audio-output';
									$options[':details']=$var;
									break;
								case "line_out_treble":
									$options[':target']="eq -t";
									$options[':details']=$var;
									break;
								case "line_out_bass":
									$options[':target']="eq -b";
									$options[':details']=$var;
									break;
								case "ring_tone_volume":
									$options[':object']="volume";
									$options[':target']="ring-tone";
									$options[':details'] = $var;
									break;
								case "status_tone_volume":
									$options[':object']="volume";
									$options[':target']="status-tone";
									$options[':details'] = $var;
									break;
								case "dtmf_tone_volume":
									$options[':object']="volume";
									$options[':target']="dtmf";
									$options[':details'] = $var;
									break;
							}
							$edit_stmt->execute($options);
						}
					}
					break;
				case "telepresence":
					$options = array(
						':device'=>$id,
						':verb'=>'set',
						':object'=>'system',
						':by'=>$this->user->getID()
					);
					foreach($_POST as $key=>$var){
						if($data['device'][$key] != $var){
							$options[':id']='edit-' . substr(hash('sha512', $id . microtime(true)), 0,10);
							$options[':details']=$var;
							$options[':added']=$time;
							$edited = true;
							switch($key){
								case "camera_lock":
									$options[':object']="camera";
									$options[':target']='lock';
									break;
								case "telepresence":
									$options[':target']='telepresence';
									break;
							}
							$edit_stmt->execute($options);
						}
					}
					break;
				case "video-control":
					$options = array(
						':device'=>$id,
						':verb'=>'set',
						':object'=>'video',
						':by'=>$this->user->getID()
					);
					foreach($_POST as $key=>$var){
						if($data['device'][$key] != $var){
							$options[':id']='edit-' . substr(hash('sha512', $id . microtime(true)), 0,10);
							$options[':details']=$var;
							$options[':added']=$time;
							$edited = true;
							switch($key){
								case "camera_far_control":
									$options[':object']="camera";
									$options[':target']='far-control';
									break;
								case "camera_far_set_preset":
									$options[':object']="camera";
									$options[':target']='far-set-preset';
									break;
								case "camera_far_use-preset":
									$options[':object']="camera";
									$options[':target']='far-use-preset';
									break;
							}
							$edit_stmt->execute($options);
						}
					}
					break;
			}
		}

		$data['device'] = $this->user->devices[$id];
		$this->render('devices/edit.html.twig', $data);
	}
	public function addAction(){
		$data = array(
			'headercolor'=>'339933'
		);
		if(isset($_POST['action']) && $_POST['action'] == 'add'){

			$options = array(
				':ip'=>$_POST['ip'],
				':password'=>$_POST['device_pass'],
				':company'=>$this->user->getCompany()
			);

			$options[':id'] = 'dev-' . substr(hash('sha512', $options[':ip'] . microtime(true)), 0,10);
			$stmt = $this->writedb->prepare("INSERT INTO companies_devices (`id`,`company_id`,`ip`,`password`, `own`, `verified`) VALUES (:id, :company,:ip, :password, 1, 1)");
			$res = $stmt->execute($options);
			if(!$res){
				$_SESSION['errors'][] = "Failed to add the device";
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
			}
			//$id = $options[':id'];
			//$stmt = $this->writedb->prepare("INSERT INTO companies_devices (`company_id`,`device_id`,`own`) VALUES (:company, :id, 1)");
			/*
			$res = $stmt->execute(array(
				':company'=>$this->user->getCompany(),
				':id'=>$id
			));
			*/
/*
			if(!$res){
				$_SESSION['errors'][] = "Failed to add the device";
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
			}
			*/
			if(!isset($_SESSION['errors'])){
				$_SESSION['flash'][] = "Successfully added the device!";
			}
			//echo"<!--";print_r($options);echo"-->";
		}
		$data['devices'] = $this->user->devices;
		$comp = $this->user->getCompanyDetails();
		$data['subscription'] = $comp['plan_id'];
		$this->render('devices/add.html.twig', $data);
	}
	public function deleteAction(){
		$data = array(
			'headercolor'=>'cc6666'
		);
		if(isset($_POST['id'])){
			//delete the device if my company owns it
			$id = $_POST['id'];

			$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE id = :id AND company_id = :company AND own = 1");
			$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			$affected = $stmt->rowCount();
			if($affected > 0){
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE id = :id");
				$stmt->execute(array(':id'=>$id));
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$id));
				$stmt = $this->writedb->prepare("DELETE FROM devices_history WHERE device_id = :id");
				$stmt->execute(array(':id'=>$id));
			} else {
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE id = :id AND company_id = :company");
				$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			}
			$company = $this->user->getCompanydetails();
			if($company['plan_id'] != 'plan-sdfb834rdfg'){
				$this->user->updateDevices();
				$c = Stripe_Customer::retrieve($company['customer_id']);
				$c->updateSubscription(array("prorate" => true, "plan" => strtolower($company['planName'])."-".$this->user->deviceCount()));
			}
			header("Location: ".PROTOCOL.ROOT . "/devices/delete");

			//redirect to clear post

		}

		$data['devices'] = $this->user->devices;
		$this->render('devices/delete.html.twig', $data);
	}
}