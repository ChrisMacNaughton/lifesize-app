<?php
error_reporting(E_ALL);
session_start();
define('START_TIME', microtime());
require_once 'vendor/autoload.php';
require_once 'classes/PasswordHash.php';
require_once 'classes/User.php';
require_once 'classes/Uri.php';
require_once 'common.php';

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

$uri = new Uri();

$user = new User();

if (!$user->isAuthenticated()){
	if ($uri->seg[0] != 'login') {
		$_SESSION['flash'][] = "You must login first";
		header("Location: login");
	}
}