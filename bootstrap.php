<?php
session_start();
$app = array(
	"start" => microtime(true)
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
    die("Database connection failed");
}
try {
	$writedb = new loggedPDO('mysql:dbname=' . $write_dbname . ';host=' . $write_dbhost, $write_dbuser, $write_dbpass);
} catch (PDOException $e) {
    //$app['errors'][]= $e->getMessage();
    die("Database connection failed");
}
$single_server = array(
    'host'     => $redis_server,
    'port'     => 6379,
);

$redis = new Predis\Client($single_server);
$redis->auth($redis_pass);

require 'system/classes/user.php';
$user = new User($db, $writedb);
define('TIMEZONE',$user->getTimezone());
date_default_timezone_set(TIMEZONE);
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

if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])){
	define('PROTOCOL', 'https://');
	if($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http"){
		header("Location: https://".$_SERVER['HTTP_HOST'] . "/" . $app['controller'] . "/" . $app['action'] . "/" . $app['action']);
		exit(0);
	}
} else
	define('PROTOCOL','http://');

if($redirect){
	if($app['controller'] == 'login')
		$redirect = false;
	if($app['controller'] == 'register')
		$redirect = false;

	if($redirect){
		session_write_close();
		header("Location: ".PROTOCOL.ROOT."/login");
	}
}
if($app['controller'] == 'login' && $user->is_logged_in()){
	header("Location: ".PROTOCOL.ROOT);
}
$app['active'] = $app['controller'];
$req = null; $key = null; $file = null;

if($app['controller'] == "login"){
	$app['controller'] = "users";
	$app['action'] = "login";
}
if($app['controller'] == "register"){
	$app['controller'] = "users";
	$app['action'] = "register";
}
if($app['controller'] == 'logout'){
	$app['controller'] = "users";
	$app['action'] = "logout";
}
if($app['controller'] == 'me'){
	$app['controller'] = "users";
	$app['action'] = "view";
	$app['detail'] = $user->getID();
}
$perms = $db->query("SELECT * FROM permissions")->fetchAll(PDO::FETCH_ASSOC);
foreach($perms as $perm){
	$name = explode(' ',$perm['name']);
	$name = array_reverse($name);
	$name = array(
		strtolower($name[0]),
		strtolower($name[1])
	);
	$name = implode('/',$name);
	$permissions[$name] = (int)$perm['id'];
}
if($user->permissions == 0){
	$user->permissions = array_sum($permissions);
}
if(isset($permissions[$app['controller'].'/'.$app['action']]) AND !($user->permissions & $permissions[$app['controller'].'/'.$app['action']])){
	$_SESSION['errors'][] = "You don't have permission to view that page!";
	session_write_close();
	header("Location: ".PROTOCOL.ROOT."/me");
	exit(0);
}
$app['permissions'] = $permissions;

//$app['debug'] = true;