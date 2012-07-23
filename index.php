<?php

require_once 'bootstrap.php';

$controller = $uri->seg[0];

if ($controller == '' || $controller == 'home') {
	$controller = 'default';
}
$action = $uri->seg[1];

if ($action == '') {
	$action = 'index';
}
$controllerName = strtolower($controller) . "Controller";
$args = array();
$id = null;
if (count($uri->seg) > 2) {
	$id = $uri->seg[2];
	for ($i = 3; count($uri->seg) -3; $i++) {
		$args[] = $uri->seg[$i];
	}
}
if (file_exists('system/controllers/' . $controllerName . '.php')) {
	require_once 'system/controllers/' . $controllerName . '.php';
	$ctrl = new $controllerName($user, $db, $controllerName, $action . 'Action');
	$actionName = $action . 'Action';
	if (method_exists($ctrl, $actionName)) {
		$ctrl->beforeAction();
		$ctrl->$actionName($id, $args);
	} else {
		echo "<br />Invalid Action";
	}
} else {
	echo "<br />Controller not found";
}