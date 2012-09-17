<?php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http")
	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if(extension_loaded('newrelic')){
	define("NEW_RELIC", true);
	newrelic_disable_autorum();
} else {
	define("NEW_RELIC", false);
}
error_reporting(E_ALL^E_NOTICE^E_WARNING);
require_once 'bootstrap.php';

if (isset($_GET['debug']) && ($_GET['debug'] == true) && ($user['level'] == 4)) {
	$data['debug']['database']['queries'] = $db::printLog();
	$data['debug']['total_time'] = round((microtime(true) - $start) * 1000, 3);
	$data['debug']['database']['db_time'] = round($db_time, 3);
	$data['debug']['user'] = $user;
	$data['debug']['server'] = $_SERVER;
	ksort($data['debug']);
}
if (NEW_RELIC) 
	newrelic_name_transaction($request['controller'] . '-' . $request['id'] . '-' . $request['misc'] . '-' . $request['method'] );
$view->render($data);