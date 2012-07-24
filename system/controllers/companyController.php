<?php

class companyController extends Controller {
	public function indexAction() {
		$data = array();
		render('company/index.html.twig', $data);
	}
}