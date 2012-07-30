<?php

class companyController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		
		if ($this->user->getLevel() < 3) {
			$_SESSION['errors'][] = l('error_no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		$this->company = $this->user->getCompanyDetails();
		$this->stripe = Stripe_Customer::retrieve($this->company['customer_id']);
	}
	public function indexAction () {
		$data = array(
			'title'=>$this->user->getCompany(),
			'company' => $this->company,
			'stripe'=>$this->stripe
		);
		
		$this->render('company/index.html.twig', $data);
	}
	public function addCardAction($id){
		if ($this->user->getLevel() < 3 || $this->company['id'] != $id)
			$json = array('status'=>'error', 'details'=>'No Permission');
		else{
		$token = $_POST['id'];
		$card = $_POST['card'];
		$oldFingerprint = $this->stripe->active_card->fingerprint;
		
		$company = $this->stripe;
		$company->card = $token;
		$response = $company->save();
		
		$card = $response->active_card;
		$options = array(
			':company_id'=>$id,
			':country'=>$card['country'],
			':exp_month'=>$card['exp_month'],
			':exp_year'=>$card['exp_year'],
			':fingerprint'=>$card['fingerprint'],
			':last4'=>$card['last4'],
			':name'=>$card['name'],
			':type'=>$card['type']
		);
		$json = array(
			'Last4'=>$options[':last4'],
			'old'=>$oldFingerprint,
			'new'=>$card['fingerprint']
		);
		$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'change_credit_card',
						':details'=>"User changed the credit card on file",
						':now'=>time()
					));
		}
		echo json_encode($json);
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
			$company[':customer_id'] = "";
			$user[':user_name'] = $_POST['contact_name'];
			$user[':email'] = $_POST['contact_email'];
			$user[':phone'] = $_POST['contact_phone'];
			$user[':level'] = 3;
			if (count($errors) == 0) {
				if ($company = $this->register($company, $user[':email'])) {
					$user[':company_id'] = $company[':id'];
					if ($this->user->register($user)) {
						$_SESSION['flash'][] = l('success_register');
						header("Location: /user/login");
					} else {
						$errors[] = $this->db->errorCode();
					}
				} else {
					$errors[] = $this->db->errorCode();
				}
			}
			$data['register']['company'] = $company;
			$data['register']['user'] = $user;
		}
		$this->render('company/new.html.twig', $data);
	}
	protected function register($company, $email) {
		$name = explode(' ', $company[':name']);
		$slug = '';
		foreach ($name as $word) {
			$slug .= $word[0];
		}
		$slug_final = $slug . rand(1, 10);
		$stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :slug");
		$stmt->execute(array(':slug'=>$slug_final));
		$i=1;
		while ($stmt->rowCount() > 0) {
			$slug_final = $slug . rand($i, $i+10);
			$stmt->execute(array(':slug'=>$slug_final));
			$i++;
		}
		$company[':id'] = $slug_final;
		
		$customer = Stripe_Customer::create(array(
			"description"=>$company[":slug"],
			'email'=>$email,
			'plan'=>'free'
		));
		
		if (isset($customer->id)) {
			$company[':customer_id'] = $customer->id;
		}
		
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
		//echo $query;
		$stmt = $this->db->prepare($query);
		
		if ($stmt->execute($company)) {
			return $company;
		} else {
			return false;
		}
	}
}