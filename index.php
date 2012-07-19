<?php
require_once 'bootstrap.php';

switch($uri->seg[0]) {
	case "login":
		require_once "system/controllers/user/login.php";
		break;
	case "logout":
		$user->logout();
		break;
}