<?php
$app['start'] = microtime();
define("COMPANY_NAME", 'VC-Control');
include 'bootstrap.php';

$user = new User();
$ctrl = ($uri->seg(0) == '') ? 'default' : $uri->seg(0);
$actn =  ($uri->seg(1) == '') ? 'index' : $uri->seg(1);
$id = ($uri->seg(2) == '') ? null : $uri->seg(2);

$controllerName = $ctrl . 'Controller';
$actionName = $actn . 'Action';

$smtpSettings = array(
	'Iam User Name'=>'vc-control',
	'server'=>'email-smtp.us-east-1.amazonaws.com',
	'username'=>'AKIAIGRMTSJRNM5OZJGQ',
	'password'=>'AgVymCoIrErF6z7uq/5vwolWI3luYtz6j2nru1Vy7X7S'
);
//echo "<!-- Controller: $controllerName | Action: $actionName-->";
try {
	$controller = new $controllerName($ctrl, $actn, $app, $db);
	if (method_exists($controller, $actionName)) {
		if (is_null($id))
			$controller->$actionName();
		else
			$controller->$actionName($id);
	} else {
		$controller = new errorController();
		$controller->errorAction('action', $actionName);
	}
} catch (Exception $e) {
	$controller = new errorController();
	$controller->errorAction($controllerName, $actionName);
}