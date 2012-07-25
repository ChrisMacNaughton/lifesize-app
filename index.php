<?php
$app['start'] = microtime();
include 'bootstrap.php';

$app['user'] = new User();
$ctrl = ($uri->seg(0) == '') ? 'default' : $uri->seg(0);
$actn =  ($uri->seg(1) == '') ? 'index' : $uri->seg(1);
$id = ($uri->seg(2) == '') ? null : $uri->seg(2);

$controllerName = $ctrl . 'Controller';
$actionName = $actn . 'Action';
try {
	$controller = new $controllerName($ctrl, $actn, $app, $db);
	if (method_exists($controller, $actionName)) {
		if (is_null($id))
			$controller->$actionName();
		else
			$controller->$actionName($id);
	} else {
		$controller = new errorController();
		$controller->errorAction('action');
	}
} catch (Exception $e) {
	$controller = new errorController();
	$controller->errorAction('controller');
}