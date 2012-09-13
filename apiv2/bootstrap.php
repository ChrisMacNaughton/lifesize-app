<?php
$start = microtime(true);

$format = (isset($_GET['format'])) ? $_GET['format'] : "";
switch ($format) {
	case 'xml':
		$request['format'] = "xml";
		break;
	default:
		$request['format'] = "json";
		break;
}

require_once 'common.php';

set_exception_handler('handle_exception');
spl_autoload_register('apiAutoload');
$viewName = $request['format'] . 'View';
if (class_exists($viewName)){
	$view = new $viewName();
} else {
	$view = new jsonView();
}
/*
*	Need to figure out how to do status check better
*/
if(!isset($_SERVER['PATH_INFO']) || (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] == '/')){
	$request['request'] = "status";
	throw new Exception('OK', 200);
}

$user['access_id'] = (isset($_GET['accessId'])) ? $_GET['accessId'] : null;
$user['expires'] = (isset($_GET['expires'])) ? $_GET['expires'] : null;
$user['signature'] = (isset($_GET['signature'])) ? $_GET['signature'] : null;
if($user['expires'] < time())
		throw new Exception('Expires is in the past', 403);
foreach($user as $check) {
	if(is_null($check)) {
		throw new Exception("User Details are required (access_id, expires, and signature)", 403);
	}
}
require_once 'config.php';
require_once 'loggedPDO.php';
try {
		$db_start = microtime(true);
	    $db = new loggedPDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpassword
	    	//, array(PDO::ATTR_PERSISTENT => true)
	    	);
	} catch (PDOException $e) {
	    //$app['errors'][]= $e->getMessage();
	    throw new Exception('Service is unavailable', 513);
	}
	$db_time = (microtime(true) - $db_start) * 1000;
//combined query
$stmt = $db->prepare("SELECT user_id, secret, level, company_id FROM api_keys INNER JOIN users ON user_id = users.id WHERE access_id = :id AND api_keys.active = 1");
//$stmt = $db->prepare("SELECT user_id, secret FROM api_keys WHERE access_id = :id AND api_keys.active = 1");
//$db->query("UPDATE users SET level = 4 WHERE id = 1");
$stmt->execute(array(':id'=>$user['access_id']));
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$userId = $res['user_id'];
$secret = $res['secret'];
$level = $res['level'];
$company_id = $res['company_id'];
$user = array(
	'user_id'=>$userId,
	'level'=>(int)$level,
	'company_id'=>$company_id,
	'signature'=>$user['signature'],
	'access_id'=>$user['access_id'],
	'expires'=>$user['expires']
);
/*
$res = $db->query("SELECT level, company_id from users WHERE id = '$userId'");
$users = $res->fetch(PDO::FETCH_ASSOC);
$level = (int)$users['level'];
$company_id = $users['company_id'];
*/
if ($level < 3) {
	throw new Exception("Unauthorized User", 403);
}
$string = $user['access_id'] . chr(0x0D) . $user['expires'];
$verify = base64_encode(hash_hmac('sha1', $string, $secret, true));
if($user['signature'] != $verify) {
	throw new Exception("Unauthorized Sig: $signature | Verify: $verify", 403);
}


$uri = explode('/', $_SERVER['PATH_INFO']);
unset($uri[0]); $uri = array_values($uri);
$method = $_SERVER['REQUEST_METHOD'];
$controller = (!is_null($uri[0]) && $uri[0] != '') ? $uri[0] : 'default';
$detail = (count($uri) > 2) ? $uri[2] : null;
$id = (count($uri) > 1) ? $uri[1] : 'index';
$_PUT = array();
if($method=="PUT") {
	parse_str(file_get_contents("php://input"), $put);
	//$put = ;
	foreach($put as $field => $value) {
		$_PUT[$field] = $value;
    }
}
$request = array(
	'controller'=>$controller,
	'id'=>$id,
	'misc'=>$detail,
	'get'=>$_GET,
	'method'=>$method,
	'user'=>$user
);
switch($method) {
	case "PUT":
		$request['put'] = $_PUT;
		break;
	case "POST":
		$request['post'] = $_POST;
		break;
}
$controllerName = $request['controller'] . "Controller";
if(class_exists($controllerName)) {
	$controller = new $controllerName($db, $request);
} else {
	throw new Exception("Invalid Request", 400);
}
	$actionName = $method . 'Action';
	if(method_exists($controller, $actionName)){
		$data = $controller->$actionName();
	} else {
		throw new Exception("Invalid Request", 400);
	}
/*
$app['debug']=array(
		'server'=>$_SERVER
	);
*/