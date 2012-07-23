<?php

class devicesController extends Controller{
	public function beforeAction() {
		parent::beforeAction();
		$stmt = $this->db->prepare("SELECT max FROM companies WHERE id = :company_id");
		$stmt->execute(array(':company_id'=>$this->user->getCompany()));
		$max = $stmt->fetch();
		$this->maxDevices = $max['max'];
	}
	public function indexAction() {
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE company_id = :id");
		$stmt->execute(
			array(
			':id'=>$this->user->getCompany(),
		));
		$devices =  $stmt->fetchAll();
		$data = array(
		'title'=>'Devices',
		'devices'=>$devices
		);
		render ('devices/index.html.twig', $data);
	}
	public function viewAction($id) {
		$stmt = $this->db->prepare("SELECT devices.*, makes.name as Make, models.name AS Model FROM devices LEFT JOIN models ON devices.model_id = models.id LEFT JOIN makes ON devices.make_id = makes.id WHERE devices.id = :id AND devices.company_id = :company_id");
		$stmt->execute(
			array(
			':id'=>$id,
			':company_id'=>$this->user->getCompany(),
		));
		$device = $stmt->fetch();
		$data = array(
			'devices'=>$device,
			'title'=>'Device ( '. $device['name'] . ') Details'
		);
		render('devices/view.html.twig', $data);
	}
}