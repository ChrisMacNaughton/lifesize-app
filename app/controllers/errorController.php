<?php

class errorController extends Controller {
	public function errorAction($problem) {
		switch($problem) {
			case 'controller':
			case 'action':
				$this->render('errors/404.html.twig', array('missing'=>$problem));
				break;
		}
	}
}