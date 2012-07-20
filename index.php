<?php
require_once 'bootstrap.php';

switch($uri->seg[0]) {
	case "":
	case "home":
		require_once "system/controllers/home.php";
		break;
	case "login":
		require_once "system/controllers/user/login.php";
		break;
	case "logout":
		$user->logout();
		header("Location: /");
		break;
}