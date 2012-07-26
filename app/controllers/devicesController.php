<?php

class devicesController extends Controller {
	public function indexAction() {
		$data = array(
			'title'=>'Devices'
		);
		$this->render('devices/index.html.twig', $data);
	}
}