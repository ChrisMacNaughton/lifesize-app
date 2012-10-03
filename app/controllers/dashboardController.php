<?php

class dashboardController extends Controller {
	public function indexAction(){
		$data = array('user'=>$this->user);

		$this->render("dashboard.html.twig", $data);
	}

}