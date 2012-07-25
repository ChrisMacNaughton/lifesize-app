<?php

class defaultController extends Controller {
	public function indexAction() {
		$data['title'] = "Dashboard";
		$this->render('home.html.twig', $data);
	}
}