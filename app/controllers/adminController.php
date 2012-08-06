<?php

class adminController extends Controller{
	public function beforeAction() {
		parent::beforeAction();
		global $options;
		$ec2 = new AmazonEC2($options);
		$ami_id = "ami-976dc3fe";

		$instances = $ec2->describe_instances(array(
		'Filter'=> array(
			array('Name' => 'image-id', 'Value'=>$ami_id),
			array('Name' => 'instance-state-name', 'Value'=>"running")
		)
		));
		$instances = $instances->body->reservationSet->to_array();
		$instances = $instances['item'];
		if (is_null($instances['instancesSet'])) { //multiple servers
			$count =  count($instances);
		} else { //single server
			$count = count($instances['instancesSet']);
		}
		$this->app['serverCount'] = $count;
	}
	
	public function indexAction() {
		$this->app['admin'] = 'cp';
		$data = array('title'=>'AdminCP');	
		$this->render('admin/index.html.twig', $data);
	}
	public function devicesAction() {
		$this->app['admin'] = 'devices';
		$data = array('title'=>'Devices - AdminCP');	
		$this->render('admin/devices.html.twig', $data);
	}
	public function companiesAction() {
		$this->app['admin'] = 'companies';
		$data = array('title'=>'Companies - AdminCP');	
		$this->render('admin/companies.html.twig', $data);
	}
}