<?php

class adminController extends Controller{
	public function beforeAction() {
	
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