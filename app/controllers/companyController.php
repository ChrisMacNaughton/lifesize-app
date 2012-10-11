<?php

class companyController extends Controller {
	
	public function indexAction(){
		$data = array('headercolor'=>'00ffff');
		$data['company'] = $this->user->getCompanyDetails();
		$stmt = $this->db->prepare("SELECT * FROM subscriptions WHERE id = :id");
		$stmt->execute(array(':id'=>$data['company']['plan_id']));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['company']['plan'] = $res['name'];
		$this->render('company/index.html.twig', $data);
	}
	public function editAction($name){
		switch($name){
			case "plan":
				echo "Editing subscription";
				break;
			default:
				//edit company details
			break;
		}
	}
}