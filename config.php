<?php
$options = array('certificate_authority'=>true,
				'default_cache_config' => '',);
if (get_cfg_var('aws.access_key') === false) {

	define(DEV_ENV, true);
	$options['key'] = 'AKIAIZCMBC2UFLIFHU2Q';
	$options['secret'] = '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';

	$path = 'ls.dev';
	define('PATH', $path);
	
	
} else {
	$options['key'] = get_cfg_var('aws.access_key');
	$options['secret'] = get_cfg_var('aws.secret_key');
	
	define(DEV_ENV, false);
	define('PATH',get_cfg_var('aws.param1'));
	$path = PATH;
}
//dev key
//$stripe_key = "RErWJasvTnxUahbxUsW6wbjTVALVk3KL";
//live key
$stripe_key = "gXvEMFzbneZV0BxeD4rBobo3zRfh7Zvu";
$dbuser = 'vcawsuser';
$dbpassword = 'Mplz_D8ZJoxwXPug';