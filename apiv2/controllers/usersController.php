<?php

class usersController extends defaultController {
	public function putAction() {
		$data = array();
		if(!isset($this->request['put']['company_id'])){
			throw new Exception("User ID is not set", 400);
		}
		$id = $this->request['id'];

		if($this->user['user_id'] != $id && $this->user['level'] < 3){
			throw new Exception("You don't have permission", 403);
		}

		$new_company = $this->request['put']['company_id'];
		$stmt = $this->db->prepare("UPDATE users SET company_id = :comp WHERE id = :id");
		$check = $this->db->prepare("SELECT count(*) AS count FROM companies WHERE id = :comp");
		$check->execute(array(':comp'=>$new_company));
		$res = $check->fetch(PDO::FETCH_ASSOC);
		if ($res['count'] != 0){
			$stmt->execute(array(
				':comp'=>$new_company,
				':id'=>$id
			));

			return array('Status'=>'OK');
		} else {
			throw new Exception("No campany with that ID", 400);
		}
	}
}