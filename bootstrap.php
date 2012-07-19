<?php
error_reporting(E_ALL);
define('START_TIME', microtime());
require_once 'vendor/autoload.php';
require_once 'classes/User.php';
require_once 'classes/Uri.php';

if (get_cfg_var('aws.access_key') === false) {
$options = array(
	'certificate_authority'=>true,
	'default_cache_config' => '',
	'key' => 'AKIAIZCMBC2UFLIFHU2Q',
	'secret' => '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',
);
} else {
$options = array(
	'certificate_authority'=>get_cfg_var('aws.param1'),
	'default_cache_config' => '',
	'key' => get_cfg_var('aws.access_key'),
	'secret' => get_cfg_var('aws.secret_key'),
);
}
$dynamodb = new AmazonDynamoDB($options);

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