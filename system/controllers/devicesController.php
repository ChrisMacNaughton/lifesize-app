<?php

class devicesController extends Controller{
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
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE id = :id AND company_id = :company_id");
		$stmt->execute(
			array(
			':id'=>$id,
			':company_id'=>$this->user->getCompany(),
		));
		$device = $stmt->fetch();
		$data = array(
			'device'=>$device,
			'title'=>'Device ( '. $device['name'] . ') Details'
		);
		render('devices/view.html.twig', $data);
	}
}