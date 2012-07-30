<?php

class eventsController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		
		if ($this->user->getLevel() < 3) {
			$_SESSION['errors'][] = l('error_no_permission');
			header("Location: /user/view/" . $this->user->getID());
		}
		$this->company = $this->user->getCompanyDetails();
		
	}
	public function indexAction() {
		$data = array(
			'title'=>'Events'
		);
		$stmt = $this->db->prepare("SELECT * FROM `events` WHERE company_id = :id ORDER BY start_time DESC");
		$options = array(
			':id'=>$this->company['id']
			);
		$stmt->execute($options);
		$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$working = array();
		$stmt = $this->db->prepare("SELECT * FROM codes");
		$stmt->execute();
		$codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($codes as $code){
			$tmp[$code['code']] = $code;
		}
		$codes = $tmp;
		$call = $this->db->prepare("SELECT devices.ip AS ip, devices.name AS caller, calls.in_call FROM calls LEFT JOIN devices ON caller = devices.id WHERE event_id = :id");
$update = $this->db->prepare("
SELECT devices.name, old.description AS old_desc, new.description AS new_desc
FROM updates
LEFT JOIN devices ON updates.device_id = devices.id
LEFT JOIN software_versions AS old ON updates.current_version = old.id
LEFT JOIN software_versions AS new ON updates.new_version = new.id
WHERE event_id = :id
");
		foreach ($events as $event) {
			$call->closeCursor();
			$update->closeCursor();
			switch($event['type']) {
				case 100:
					$call->execute(array(':id'=>$event['id']));
					$acall = $call->fetch(PDO::FETCH_ASSOC);
					$working[$event['type']][] =array(
						'id'=>$event['id'],
						'type_code' => $event['type'],
						'type'=>array(
							'name'=>$codes[$event['type']]['name'],
							'code'=>$codes[$event['type']]['code'],
							'description'=>$codes[$event['type']]['description']
						),
						'start_time'=>$event['start_time'],
						'scheduled'=>_ago($event['start_time']),
						'call'=>array(
							'caller'=>$acall['caller'],
							'in_call'=>explode(',', $acall['in_call']),
							'caller_ip'=>$acall['ip'],
						),
						'notes'=>$event['notes']
					);
					break;
				case 101:
					$update->execute(array(
						':id'=>$event['id']
					));
					$aupdate = $update->fetch(PDO::FETCH_ASSOC);
					$working[$event['type']][] =array(
						'id'=>$event['id'],
						'type_code' => $event['type'],
						'type'=>array(
							'name'=>$codes[$event['type']]['name'],
							'code'=>$codes[$event['type']]['code'],
							'description'=>$codes[$event['type']]['description']
						),
						'start_time'=>$event['start_time'],
						'scheduled'=>_ago($event['start_time']),
						'update'=>$aupdate,
						'notes'=>$event['notes']
					);
					break;
			}
		}
		$data['events'] = $working;
		$data['now'] = time();
		$this->render('events/index.html.twig' , $data);
	}
	public function newAction() {
		$data = array(
		'title'=>'New Event'
		);
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE company_id = :id AND active = 1");
		$stmt->execute(array(
			':id'=>$this->company['id'],
		));
		$data['devices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = $this->db->prepare("SELECT * FROM software_versions");
		$stmt->execute();
		$data['updates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->render('events/new.html.twig', $data);
	}
}