<?php

class adminController extends Controller {
	public function indexAction(){
		$data = array(
			'headercolor'=>'663399',
      'adminPage'=>'overview'
		);
		$this->render('admin/index.html.twig', $data);
	}
  public function companiesAction($id = null){
    if($id == null){
      $data = array(
        'headercolor'=>'663399',
        'adminPage'=>'company'
      );
      $data['companies'] = $this->db->query("SELECT C.name as name, C.id as id, S.name AS planName FROM companies AS C INNER JOIN subscriptions AS S ON C.plan_id = S.id")->fetchAll(PDO::FETCH_ASSOC);
      $this->render('admin/companies.html.twig', $data);
    } else {
      $data = array(
        'headercolor'=>'663399',
        'adminPage'=>'company'
      );
      $company_stmt = $this->db->prepare("SELECT companies.*, S.name AS planName FROM companies INNER JOIN subscriptions AS S ON companies.plan_id = S.id WHERE companies.id=:id");
      $company_stmt->execute(array(":id"=>$id));
      $data['company'] = $company_stmt->fetch(PDO::FETCH_ASSOC);
      if(isset($_POST['action'])){
        $stmt = $this->db->prepare("SELECT count(*) AS count FROM companies_devices WHERE company_id = :id");
        switch ($_POST['action']) {
          case 'changePlan':
            if($_POST['plan'] == 'plan-sdfb834rdfg'){
              $plan = '0_-_1';
            } else if ($_POST['plan'] == 'plan-sdioybs0'){
              $plan = 'free';
            } else if ($_POST['plan'] == 'plan-osr7y078'){
              //pro
              $stmt->execute(array(':id'=>$id));
              $count = $stmt->fetch(PDO::FETCH_ASSOC);
              $count = $count['count'];
              $plan = "pro-$count-15";
            } else if ($_POST['plan'] == 'plan-soenri7'){
              //basic
              $stmt->execute(array(':id'=>$id));
              $count = $stmt->fetch(PDO::FETCH_ASSOC);
              $count = $count['count'];
              $plan = "basic-$count-10";
            }
            $cu = Stripe_Customer::retrieve($data['company']['customer_id']);
            $stmt = $this->db->prepare("UPDATE companies SET plan_id = :plan_id WHERE id=:id");
            $stmt->execute(array(':plan_id'=>$_POST['plan'], ':id'=>$id));
            $cu->updateSubscription(array("prorate" => true, "plan" => $plan));
            header("Loation: ".PROTOCOL.ROOT."/admin/company/$id");
            break;
          case 'toggleActive':
            $stmt = $this->db->prepare("UPDATE companies SET active = :active WHERE id = :id");
            $active = ($_POST['active'] == 1)?0:1;
            $stmt->execute(array(':active'=>$active, ':id'=>$id));
          default:
            # code...
            break;
        }
      }
      $company_stmt->execute(array(":id"=>$id));
      $data['company'] = $company_stmt->fetch(PDO::FETCH_ASSOC);
      $data['plans'] = $this->db->query("SELECT * FROM subscriptions")->fetchAll(PDO::FETCH_ASSOC);

      $this->render('admin/company.html.twig', $data);
    }
  }
}