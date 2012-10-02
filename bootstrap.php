<?php
session_start();
$app = array(
	"Start" => microtime(true)
);
/*
*	Incliude config / class for db
*	Initialize DB
*/
require 'system/config.php';
require 'system/classes/loggedPDO.php';
try {
	$db = new loggedPDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpass);
} catch (PDOException $e) {
    //$app['errors'][]= $e->getMessage();
    throw new Exception('Service is unavailable', 513);
}

require 'system/classes/user.php';
$user = new User($db);
$redirect = ($user->is_logged_in())?false:true;
require 'vendor/autoload.php';
/*
*	building URI array
*/
$file = explode('/', __file__);
$key = array_search('bootstrap.php', $file) - 1;
define('BASE_DIR', $file[$key]);

$req = explode('/', $_SERVER['REQUEST_URI']);
unset($req[0]);
if($req[1] == BASE_DIR){
	unset($req[1]);
}
$req = array_values($req);
if($req[0] == 'index.php')
	unset($req[0]);
$req = array_filter($req);

/*
*	Clearing variables used in building URI
*/

$uri = array_values($req);
$app['controller'] = isset($uri[0])?$uri[0]:"dashboard";
$app['action'] = isset($uri[1])?$uri[1]:"index";
$app['detail'] = isset($uri[2])?$uri[2]:"";

if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http"){
	header("Location: https://".$_SERVER['HTTP_HOST'] . "/" . $app['controller'] . "/" . $app['action'] . "/" . $app['action']);
}

if($redirect){
	if($app['controller'] == 'login')
		$redirect = false;
	if($app['controller'] == 'register')
		$redirect = false;

	if($redirect){
		session_write_close();
		header("Location: /".BASE_DIR."/login");
	}
}
$req = null; $key = null; $file = null;

if($app['controller'] == "login"){
	$app['controller'] = "user";
	$app['action'] = "login";
}
if($app['controller'] == "register"){
	$app['controller'] = "company";
	$app['action'] = "register";
}