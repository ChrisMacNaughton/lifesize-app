<?php
$app['start'] = microtime();
define("COMPANY_NAME", 'VC-Control');
include 'bootstrap.php';
$user = new User();
$ctrl = ($uri->seg(0) == '' || $uri->seg[0] == 'home') ? 'default' : $uri->seg(0);
$actn =  ($uri->seg(1) == '') ? 'index' : $uri->seg(1);
$id = ($uri->seg(2) == '') ? null : $uri->seg(2);

$controllerName = $ctrl . 'Controller';
$actionName = $actn . 'Action';
if (!$user->isAuthenticatedFully()) {
$redir = true;
	if ($controllerName == 'userController' && $actionName == 'loginAction') 
		$redir = false;
	if ($controllerName == 'userController' && $actionName == 'resetAction') 
		$redir = false;
	if ($controllerName == 'companyController' && $actionName == 'registerAction')
		$redir = false;
	
	if ($redir)
	{
		if ($controllerName != "defaultController" && $actionName != "indexAction")
			$_SESSION['flash'][] = l("error_need_to_login");
		session_write_close();
		header("Location: /user/login");
	}
}
$app['page'] = $ctrl;

if (class_exists($controllerName))
	$controller = new $controllerName($ctrl, $actn, $app, $db);
else
	$controller = new errorController();
	
if (method_exists($controller, $actionName)) {
	$controller->beforeAction();
	if (is_null($id))
		$controller->$actionName();
	else
		$controller->$actionName($id);
} else {
	$controller = new errorController();
	$controller->errorAction($ctrl, $actn);
}
