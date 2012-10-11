<?php

class adminController extends Controller {
	public function indexAction(){
		$data = array(
			'headercolor'=>'663399'
		);
		$this->render('admin/index.html.twig', $data);
	}
}