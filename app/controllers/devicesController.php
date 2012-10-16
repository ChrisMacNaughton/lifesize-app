<?php

class devicesController extends Controller {
	public function indexAction(){
		$data = array(
			'title'=>"Devices"
		);
		$data['headercolor'] = '99ff99';
		$data['devices'] = $this->user->devices;
		$this->render('devices/index.html.twig', $data);
	}
	public function viewAction($id){
		$data=array(
			'headercolor'=>'66cc66',
		);
		$data['average_loss'] = json_decode($this->redis->get('cache.averages'), true);

		$time_limit = -.001;
		$loss7 = array();
		$loss30 = array();
		$loss60 = array();
		$loss90 = array();
		$loss = array();
		$call_counts = array();
		$min_loss = 0.005;
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
		$data['loss7'] = $loss7;
		$data['loss30'] = $loss30;
		$data['loss60'] = $loss60;
		$data['loss90'] = $loss90;
		$data['losses'] = $l;
		ksort($data['loss7']);
		ksort($data['loss30']);
		ksort($data['loss60']);
		ksort($data['loss90']);
		ksort($data['average_loss']);
		ksort($data['losses']);

		$data['device'] = $this->user->devices[$id];
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
		$data = array(
			'headercolor'=>'66ff66'
		);
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
		$this->render('devices/add.html.twig', $data);
	}
	public function deleteAction(){
		$data = array(
			'headercolor'=>'cc6666'
		);
		if(isset($_POST['id'])){
			//delete the device if my company owns it
			$id = $_POST['id'];
			echo "<!-- Deleting $id -->";

			$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id AND company_id = :company AND own = 1");
			$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			$affected = $stmt->rowCount();
			if($affected > 0){
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id");
				$stmt->execute(array(':id'=>$id));
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$id));
				$stmt = $this->writedb->prepare("DELETE FROM devices_history WHERE device_id = :id");
				$stmt->execute(array(':id'=>$id));
			} else {
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id AND company_id = :company");
				$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			}
			header("Location: ".PROTOCOL.ROOT . "/devices/delete");
				
			//redirect to clear post

		}

		$data['devices'] = $this->user->devices;
		$this->render('devices/delete.html.twig', $data);
	}
}