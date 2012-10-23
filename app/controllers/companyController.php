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
	public function addCardAction($id){
		
		$token = $_POST['id'];
		$card = $_POST['card'];
		$company = $this->user->getCompanyDetails();

		//$oldFingerprint = $this->stripe->active_card->fingerprint;
		if($company['customer_id'] == null){
			$response = Stripe_Customer::create(array(
				'email'=>$company['created_by'],
				"description" => $company['id'],
				"card" => $token // obtained with Stripe.js
			));
			$oldFingerprint = '';
			$stmt = $this->db->prepare("UPDATE companies SET customer_id = :customer_id WHERE id = :id");
			$stmt->execute(array(
				':customer_id'=>$response->id,
				':id'=>$company['id']//cus_0b1N38ef1sdvfb
			));
		} else {
			$company = Stripe_Customer::retrieve($this->company['customer_id']);
			$oldFingerprint = $company->active_card->fingerprint;
			$company->card = $token;
			$response = $company->save();
		}
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
		$stmt = $this->db->prepare("UPDATE companies SET last4 = :last4 WHERE id = :id");
		$stmt->execute(array(
			':last4'=>$options[':last4'],
			':id'=>$thid->company['id']
		));
		$json['errors'] = $stmt->errorInfo();
		echo json_encode($json);
		exit();
	}
	public function editAction($name){
		switch($name){
			case "plan":
				$data = array('headercolor'=>'00cccc');
				$data['company'] = $this->user->getCompanyDetails();
				if(isset($_POST['plan'])){
					$cu = Stripe_Customer::retrieve($data['company']['customer_id']);
					$stmt = $this->db->prepare("UPDATE companies SET plan_id = :plan WHERE id = :company_id");
					$opts = array('company_id'=>$data['company']['id']);
					switch($_POST['plan']){
						case "Pro":
							$opts['plan']="plan-osr7y078";
							$cu->updateSubscription(array("prorate" => true, "plan" => "pro-".$this->user->deviceCount()));
							break;
						case "Basic":
							$opts['plan']="plan-soenri7";
							$cu->updateSubscription(array("prorate" => true, "plan" => "basic-".$this->user->deviceCount()));
							break;
						case "Free":
							if($this->user->deviceCount() > 1){
								$_SESSION['errors'][] = "Invalid Plan, you have too many devices";
								session_write_close();
								header("Location: ".PROTOCOL.ROOT."/company/edit/plan");
								exit();
							}
							$opts['plan']="plan-sdioybs0";
							$cu->updateSubscription(array("prorate" => true, "plan" => "free"));
							break;
						default:
							$_SESSION['errors'][] = "Invalid Plan";
							session_write_close();
							header("Location: ".PROTOCOL.ROOT."/company/edit/plan");
							exit();
							break;
					}
					$stmt->execute($opts);
					session_write_close();
					header("Location: ".PROTOCOL.ROOT."/company/edit/plan");
					exit();
				}
				//$data['plan'] = $company['planName'];
				$stmt = $this->db->prepare("SELECT * FROM subscriptions WHERE id = :id");
				$stmt->execute(array(':id'=>$data['company']['plan_id']));
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$data['company']['plan'] = $res['name'];
				$data['devices_count'] = count($this->user->devices);
				$data['plans'] = array(
					'basic'=>array(
						'price'=>10,
					),
					'pro'=>array(
						'price'=>15,
					),
				);
				$this->render("company/subscription.html.twig", $data);
				break;
			default:
				//edit company details
			break;
		}
	}
}