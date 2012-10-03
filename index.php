<?php
require_once 'bootstrap.php';
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
}

$controller = new $controllerName($app, $db);
$controller->$actionName();