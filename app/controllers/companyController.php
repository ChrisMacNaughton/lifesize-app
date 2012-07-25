<?php

class companyController extends Controller {
	public function indexAction () {
	
	}
	public function registerAction() {
		$data = array();
		if (isset($_POST['action']) && $_POST['action'] == 'new') {
			
		}
		$this->render('company/new.html.twig', $data);
	}
}