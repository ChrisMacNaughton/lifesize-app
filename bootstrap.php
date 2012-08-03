<?php
/*
*	include required files
*/
error_reporting(E_ALL^E_WARNING^E_NOTICE);
include 'common.php';
define('BASE_PATH', __DIR__);
set_include_path(get_include_path() . PATH_SEPARATOR . 'app/classes/phpseclib' . PATH_SEPARATOR . 'app');
require_once 'vendor/autoload.php';
//require_once 'Net/SSH2.php';
require_once 'app/classes/autoload.php';
/* set amazon config vars */
if (get_cfg_var('aws.access_key') === false)
{
	include 'config.php';
	define('PATH', $path);
} else {
	$options = array(
		'certificate_authority'=>get_cfg_var('aws.param1'),
		'default_cache_config' => '',
		'key' => get_cfg_var('aws.access_key'),
		'secret' => get_cfg_var('aws.secret_key'),
	);
	$dbuser = get_cfg_var('aws.param2');
	$dbpassword = get_cfg_var('aws.param3');
	define('PATH',get_cfg_var('aws.param4'));
	$path = PATH;
	$stripe_key = get_cfg_var('aws.param5');
}
//connect to the mysql db instance of RDS
$dsn = 'mysql:dbname=vcdb;host=vcdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';
try {
    $db = new PDO($dsn, $dbuser, $dbpassword);
} catch (PDOException $e) {
    $app['errors'][]= $e->getMessage();
}
$uri = new URI();
Stripe::setApiKey($stripe_key);

//uncomment the following to switch to using dynamodb to handle sessions

$dynamodb = new AmazonDynamoDB($options);

// Instantiate, configure, and register the session handler
$session_handler = $dynamodb->register_session_handler(array(
	'table_name'       => 'sessions',
	'lifetime'         => 3600,
));

session_start();
$CACHE = array();

require('app/config/locale/'.settings('locale').'.php');