<?php

class userController extends Controller {
	public function newAction() {
		$user = array(
			':user_name'=>'Chris',
			':email'=>'chmacnaughton@gmail.com',
			':phone'=>'7136670763',
			':company_id'=>'SR1'
		);
		echo "<pre>";
		$this->user->register($user);
	}
}