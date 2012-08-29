<?php

class defaultController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		$this->company = $this->user->getCompanyDetails();
	}
	public function indexAction() {
		$data['title'] = "Dashboard";
		$stmt = $this->db->prepare("SELECT count(*) AS count FROM devices_alarms INNER JOIN users WHERE company_id = :company AND active = 1");
		$stmt->execute(array(':company'=>$this->company['id']));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['active_alarms'] = $res['count'];
		$stmt = $this->db->prepare("SELECT count(*) AS count FROM devices WHERE company_id = :id");
		$stmt->execute(array(':id'=>$this->company['id']));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['device_count'] = $res['count'];
		$this->render('home.html.twig', $data);
	}
}