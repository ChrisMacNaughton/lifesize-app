<?php

class userController extends Controller{
	public function loginAction(){
		$this->render("user/login.html.twig");
	}
}