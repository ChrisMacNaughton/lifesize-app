<?php

class errorController extends Controller{
	public function notFoundAction(){
		header("HTTP/1.0 404 Not Found");
		echo "Page Not Found";
	}
}