<?php

class adminController{
public function beforeAction() {}
	public function indexAction() {
		$data = array();
		$data['title'] = 'Admin';
		render('admin/main.html.twig', $data);
	}
	public function companiesAction() {
		$data = array();
		$data['title'] = 'Admin / Companies';
		
		render('admin/companies.html.twig', $data);
	}
	public function devicesAction() {
		$data = array();
		$data['title'] = "Admin / Devices";
		
		render('admin/devices.html.twig', $data);
	}
}