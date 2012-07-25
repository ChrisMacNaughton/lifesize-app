<?php

class companyController extends Controller {
	public function indexAction() {
		$data = array();
		$stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
		$stmt->execute(array(
			':id'=>$this->user->getCompany(),
		));
		$data['company'] = $stmt->fetch();
		if (is_null($data['company']['customer_id']))
			$_SESSION['errors'][] = l('error_inactive_customer');
		render('company/index.html.twig', $data);
	}
	public function newAction() {
		if ($this->user->isAuthenticated()) {
			header('Location: /users');
		}
		$data = array();
		if (isset($_POST['action']) && $_POST['action'] =='new') {
			var_dump($_POST);
			$stmt = $this->db->prepare("SELECT * FROM companies WHERE name = :company");
			$stmt->execute(array(':company'=>$_POST['company_name']));
			$result = $stmt->fetch();
			$stmt->closeCursor();
			$errors = array();
			if ($result) {
				$errors['company'] = l('error_company_exists');
			}
			$stmt = $this->db->prepare("SELECT * FROM companies WHERE slug = :company");
			$stmt->execute(array(':company'=>$_POST['slug']));
			$result = $stmt->fetch();
			$stmt->closeCursor();
			if ($result) {
				$errors['company'] = l('error_slug_exists');
			}
			
			if ($_POST['password'] != $_POST['password2']) {
				$errors['password'] = l('error_password_nomatch');
			}
			if (!validEmail($_POST['email'])) {
				$errors['email'] = l('error_invalid_email');
			}
			
			
			if (count($errors) == 0) {
				
			
				try {
				$stripe_customer = Stripe_Customer::create(array(
					"description"=>$_POST['company_name'],
					"email"=>$_POST['email'],
				));
				
					$customer = array(
						':name'=>$_POST['company_name'],
						':slug'=>$_POST['slug'],
						':customer_id'=>$stripe_customer->id,
						':address'=>$_POST['address1'],
						':address2'=>$_POST['address2'],
						':city'=>$_POST['city'],
						':state'=>$_POST['State'],
						':zip'=>$_POST['zip'],
						':country'=>$_POST['Country'],
						':created'=>time(),
						':rate'=>settings('current_rate')
					);
					$stmt = $this->db->prepare("INSERT INTO companies (name, slug, created, rate, customer_id, address, address2, city, state, zip, country) VALUES (:name, :slug, :created, :rate, :customer_id, :address, :address2, :city, :state, :zip, :country)");
					if ($stmt->execute($customer)) {
					$company_id = $this->db->lastInsertId();
					$user=array(
						'name'=>$_POST['name'],
						'email'=>$_POST['email'],
						'password'=>$_POST['password'],
						'password2'=>$_POST['password2'],
						'company_id'=>$company_id,
						'active'=>1,
						'level'=>3
					);
					echo "User:<pre>";var_dump($user); echo"</pre>";
						if (!$this->user->register($user)) {
							$errors[] = $this->user->getErrors();
						}
					}
					echo "<pre>";
					var_dump($customer);
					echo "</pre>";
				} catch(Exception $e) {
					echo "<pre>";
					var_dump($e);
					echo "</pre>";
				}
			
			}
			echo "<pre>";	var_dump($errors);echo "</pre>";
		}
		render('company/new.html.twig');
	}
	public function payment_detailsAction() {
		$data = array();
		if (isset($_POST['action']) && $_POST['action'] == 'add') {
		var_dump($_POST);
			
			  $error = '';
			  $success = '';
			  $stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
					$stmt->execute(array(
						':id'=>$this->user->getCompany(),
					));
					$company= $stmt->fetch();
					//var_dump($company);
			  try {
				if (!isset($_POST['stripeToken']))
				  throw new Exception("The Stripe Token was not generated correctly");
				  
				  
				Stripe_Customer::create(array(
					"description" => $company['name'],
					"name" =>$this->user->getName(),
					"card" => $_POST['stripeToken']));
				$success = 'Your payment was successful.';
			  }
			  catch (Exception $e) {
				$error = $e->getMessage();
			  }
			  $data['error'] = $error;
			  $data['success'] = $success;
		}
		render('company/payment.html.twig', $data);
	}
}