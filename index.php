<?php
$arr = array();
$app['start'] = microtime();
if(function_exists('newrelic_name_transaction')){
	define('NEW_RELIC', true);
} else {
	define('NEW_RELIC', false);
}
define("COMPANY_NAME", 'ControlVC');
include 'bootstrap.php';
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http")
	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$protocol = (DEV_ENV == true) ? 'http' : "https";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'localhost/updater/update.php');
curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
curl_setopt($ch, CURLOPT_RETURN_TRANSFER, true);
$app['updater_path'] = 'localhost/updater/update.php';
curl_exec($ch);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'localhost/updater/maintainer.php');
curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
curl_setopt($ch, CURLOPT_RETURN_TRANSFER, true);
curl_exec($ch);
$app['maintainer_path'] = 'localhost/updater/maintainer.php';

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
		if (NEW_RELIC)
			newrelic_ignore_transaction();
		if(!array_search(l("error_need_to_login"), $_SESSION['flash']) && ($ctrl != 'default' && $actn != 'index'))
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
define('TRANSACTION',$controllerName . '/' . $actionName);
if (NEW_RELIC) 
			newrelic_name_transaction(TRANSACTION);
$db = null;