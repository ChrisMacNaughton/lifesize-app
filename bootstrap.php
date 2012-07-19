<?php
define('START_TIME', microtime());
require_once 'vendor/autoload.php';
require_once 'classes/User.php';
require_once 'classes/Uri.php';
$dynamodb = new AmazonDynamoDB();

// Instantiate, configure, and register the session handler
$session_handler = $dynamodb->register_session_handler(array(
	'table_name'       => 'sessions',
	'lifetime'         => 3600,
));
$uri = new Uri();
// Open the session
session_start();
if (isset($_SESSION['user'])) {
	$user = new User($_SESSION['user']);
} else {
	$user = new User();
}