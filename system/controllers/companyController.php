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
	public function payment_detailsAction() {
		$data = array();
		if (isset($_POST['action']) && $_POST['action'] == 'add') {
		var_dump($_POST);
			Stripe::setApiKey("RErWJasvTnxUahbxUsW6wbjTVALVk3KL");
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