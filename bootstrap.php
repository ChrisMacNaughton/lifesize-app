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

include 'config.php';

Stripe::setApiKey($stripe_key);
//connect to the mysql db instance of RDS
$dsn = 'mysql:dbname=vcdb;host=vcdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';
try {
    $db = new PDO($dsn, $dbuser, $dbpassword);
} catch (PDOException $e) {
    $app['errors'][]= $e->getMessage();
    die($e->getMessage());
}
$uri = new URI();


//uncomment the following to switch to using dynamodb to handle sessions

$dynamodb = new AmazonDynamoDB($options);
/*
// Instantiate, configure, and register the session handler
$session_handler = $dynamodb->register_session_handler(array(
	'table_name'       => 'sessions',
	'lifetime'         => 3600,
));
*/
session_start();
$CACHE = array();

require('app/config/locale/'.settings('locale').'.php');