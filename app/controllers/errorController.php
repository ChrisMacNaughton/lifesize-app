<?php

class errorController extends Controller {
	public function errorAction($controller, $action) {
		
		$this->render('errors/404.html.twig', null, '404');
	}
}