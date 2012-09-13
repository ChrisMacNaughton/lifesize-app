<?php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http")
	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
error_reporting(E_ALL^E_NOTICE^E_WARNING);
require_once 'bootstrap.php';

if (isset($_GET['debug']) && ($_GET['debug'] == true) && ($user['level'] == 4)) {
	$data['debug']['database']['queries'] = $db::printLog();
	$data['debug']['total_time'] = round((microtime(true) - $start) * 1000, 3);
	$data['debug']['database']['db_time'] = round($db_time, 3);
	$data['debug']['user'] = $user;

	ksort($data['debug']);
	//$data['debug']['server'] = $_SERVER;
}

$view->render($data);