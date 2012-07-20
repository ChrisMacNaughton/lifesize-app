<?php
error_reporting(E_ALL);
session_start();
define('START_TIME', microtime());
set_include_path(get_include_path() . PATH_SEPARATOR . 'vendor/phpseclib');
$_SESSION['errors'] = array();
$_SESSION['flash'] = array();
require_once 'vendor/autoload.php';
require_once 'classes/PasswordHash.php';
require_once 'classes/User.php';
require_once 'classes/Uri.php';
require_once 'common.php';
require_once('Net/SSH2.php');
require_once 'classes/Lifesize.php';
$dsn = 'mysql:dbname=vcdb;host=vcawsdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';
if (get_cfg_var('aws.access_key') === false) {
include 'config.php';
define('PATH', $path);
} else {
$options = array(
	'certificate_authority'=>get_cfg_var('aws.param1'),
	'default_cache_config' => '',
	'key' => get_cfg_var('aws.access_key'),
	'secret' => get_cfg_var('aws.secret_key'),
);
$user = get_cfg_var('aws.param2');
$password = get_cfg_var('aws.param3');
define('PATH', cfr_get_var('aws.param4'));
}
//$dynamodb = new AmazonDynamoDB($options);

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    $_SESSION['errors'][] = $e->getMessage();
}

$uri = new Uri();

$user = new User($db);
if (!$user->isAuthenticated()){
	if ($uri->seg[0] != 'login') {
		$_SESSION['flash'][] = "You must login first";
		header("Location: login");
	}
}