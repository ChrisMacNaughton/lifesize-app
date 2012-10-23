<?php

error_reporting(E_ALL^E_NOTICE);

require_once 'bootstrap.php';
if(DEV_ENV && isset($_GET['debug']) && $_GET['debug'] == 'true'){
	require_once 'system/classes/phpErrors.php';
	\php_error\reportErrors();
}
require_once 'app/controllers/controller.php';
$controllerName = "dashboardController";
$actionName = "indexAction";
if(file_exists('app/controllers/' . strtolower($app['controller']) . 'Controller.php')){
	include 'app/controllers/' . strtolower($app['controller']) . 'Controller.php';
	$controllerName = strtolower($app['controller']) . 'Controller';
	if(method_exists($controllerName, strtolower($app['action']) . "Action")){
		$actionName = strtolower($app['action'] . 'Action');
	} else {
		include 'app/controllers/error.php';
		$controllerName = "errorController";
		$actionName = "notFoundAction";
	}
} else {
	include 'app/controllers/error.php';
	$controllerName = "errorController";
	$actionName = "NotFoundAction";
}

$controller = new $controllerName($app, $db, $writedb, $redis);
$controller->$actionName($app['detail']);

