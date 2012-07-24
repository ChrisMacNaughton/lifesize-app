<?php
define('GUEST_LEVEL', 0);
define('USER_LEVEL', 1);
define('OPERATOR_LEVEL', 2);
define('ADMIN_LEVEL', 3);
define('SUPER_ADMIN_LEVEL', 4);
require_once 'vendor/autoload.php';
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
define('PATH',get_cfg_var('aws.param4'));
}
/*
$dynamodb = new AmazonDynamoDB($options);

// Instantiate, configure, and register the session handler
$session_handler = $dynamodb->register_session_handler(array(
	'table_name'       => 'sessions',
	'lifetime'         => 3600,
));
*/
session_start();
$CACHE = array();
/*
if (!isset($_SESSION['flash']))
	$_SESSION['flash'] = array();
if (!isset($_SESSION['errors']))
	$_SESSION['errors'] = array();
*/

set_include_path(get_include_path() . PATH_SEPARATOR . 'app/phpseclib' . PATH_SEPARATOR . 'app');
require_once ('Net/SSH2.php');
require_once 'PasswordHash.php';
require_once 'Uri.php';
require_once 'User.php';
require_once 'common.php';
require_once 'system/lib/Controller.php';
/*
 * setup configs for local / AWS beanstalk
*/

$dsn = 'mysql:dbname=vcdb;host=vcawsdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';


try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    $_SESSION['errors'][] = $e->getMessage();
}

require('system/locale/'.settings('locale').'.php');
$user = new User($db);

$uri = new Uri();