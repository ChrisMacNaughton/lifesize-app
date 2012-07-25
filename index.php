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
$redirect = true;
if ($user->isAuthenticated())
	$redirect = false;
	
if ($uri->seg[0] == 'users' && $uri->seg[1] == 'login')
	$redirect = false;
	
if ($uri->seg[0] == 'company' && $uri->seg[1] == 'new')
	$redirect = false;

if ($redirect === true) {
	header("Location: /users/login");
}
$stmt = $db->prepare("SELECT customer_id FROM companies WHERE id = :id");
$stmt->execute(array(
	':id'=>$user->getCompany(),
));
$company = $stmt->fetch();

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