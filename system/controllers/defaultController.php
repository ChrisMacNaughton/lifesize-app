<?php

class defaultController extends Controller{
	public function beforeAction() {
		
	}
	public function indexAction() {
		$data = array(
			'title'=>'Home'
		);
		render('index.html.twig', $data);
	}
}