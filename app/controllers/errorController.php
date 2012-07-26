<?php

class errorController extends Controller {
	public function errorAction($controller, $action) {
		switch($problem) {
			case 'controller':
			case 'action':
				header("HTTP/1.0 404 Not Found");
				$this->render('errors/404.html.twig', array('controller'=>$controller, 'action'=>$action));
				break;
		}
	}
}