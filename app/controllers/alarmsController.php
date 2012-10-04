<?php

class alarmsController extends Controller {
	public function indexAction(){
		$data = array(
			'headercolor'=>'FF9933',
		);
		$stmt = $this->db->prepare("SELECT id, name, description FROM alarms");
		$stmt->execute(array(':id'=>$this->user->getID()));
		$alarms = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = $this->db->prepare("SELECT * FROM users_alarms WHERE user_id = :id");
		$stmt->execute(array(':id'=>$this->user->getID()));
		$data['users_alarms'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$devices = $this->user->devices;
		foreach($devices as $device){
			$final[$device['id']] = $alarms;
			foreach($alarms as $alarm){
				/*
					TODO: Add alarm search to build array of alarms, enabled or not, added or not
				*/
			}
		}
		$data['devices'] = $devices;
		//$data['alarms'] = $res;
		$this->render('alarms/index.html.twig', $data);
	}
	public function deviceAction($devic_id){

	}
}