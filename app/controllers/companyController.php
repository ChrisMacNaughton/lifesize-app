<?php

class companyController extends Controller {
	public function indexAction () {
	
	}
	public function registerAction() {
		$data = array(
			'register'=>array(
				'company'=>array(),
				'user'=>array(),
			)
		);
		if (isset($_POST['action']) && $_POST['action'] == 'new') {
			$company[':name'] = $_POST['company_name'];
			$company[':address1'] = $_POST['address_line_1'];
			$company[':address2'] = $_POST['address_line_2'];
			$company[':city'] = $_POST['city'];
			$company[':state'] = $_POST['state'];
			$company[':zip'] = $_POST['zip'];
			$company[':country'] = $_POST['country'];
			foreach ($company as $key=>$val) {
				if ($key != ':address2' && $key != ':state' && $key != ':zip'){
					if(empty($val))
						$errors[$key] = l('error_empty');
				}
			}
			$user[':user_name'] = $_POST['contact_name'];
			$user[':email'] = $_POST['contact_email'];
			$user[':phone'] = $_POST['contact_phone'];
			if (count($errors) == 0) {
				if ($this->register($company)) {
					$user[':company_id'] = $this->db->lastInsertId();
					if ($this->user->register($user)) {
					
					} else {
						$errors[] = $this->db->errorInfo();
					}
				} else {
					$errors[] = $this->db->errorInfo();
				}
			}
			$data['register']['company'] = $company;
			$data['register']['user'] = $user;
			$data['errors'] = $errors;
		}
		$this->render('company/new.html.twig', $data);
	}
	protected function register($company) {
		$name = explode(' ', $company[':name']);
		$slug = '';
		foreach ($name as $word) {
			$slug .= $word[0];
			
		}
		$slug_final = $slug . rand(1, 10);
		$stmt = $this->db->prepare("SELECT * FROM companies WHERE slug = :slug");
		$stmt->execute(array(':slug'=>$slug_final));
		$i=1;
		while ($stmt->rowCount() > 0) {
			$slug_final = $slug . rand($i, $i+10);
			$stmt->execute(array(':slug'=>$slug_final));
			$i++;
		}
		$company[':slug'] = $slug_final;
		
		$fields = array();
		$values = array();
		$value_names = array();
		foreach($company as $field => $value) {
			$fields[] = trim($field, ':');
			$value_names[] = $field;
		}
		$fields = implode(',',$fields);
		$value_names = implode(',', $value_names);
		$query = "INSERT INTO companies ($fields) VALUES ($value_names)";
		$stmt = $this->db->prepare($query);
		return $stmt->execute($company);
	}
}