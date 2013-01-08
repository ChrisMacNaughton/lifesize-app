<?php
$options = array('certificate_authority'=>true,
        'default_cache_config' => '',);
if (get_cfg_var('aws.access_key') === false) {

  define('DEV_ENV', true);
  $options['key'] = '';//AWS access key
  $options['secret'] = '';//AWS secret key

  $path = '';//This is the path that the app is accessible at during development **MUST BE A ROOT PATH**
  define('ROOT', $path);
  $app['twig_options'] = array(
      'cache'=>false,
      'debug'=>true
    );

} else {
  $options['key'] = get_cfg_var('aws.access_key');
  $options['secret'] = get_cfg_var('aws.secret_key');

  define('DEV_ENV', false);
  define('PATH',get_cfg_var('aws.param1'));
  $path = PATH;

  $app['twig_options'] = array(
      'cache'=>'cache',
      'debug'=>false
    );
}
/*
* Stripe keys, fill out both live and dev keys if desired
*/
//dev key
if(DEV_ENV)
$stripe_key = "";
//live key
if(!DEV_ENV)
$stripe_key = "";

/*
* Read Database login information
*/
$dbname = '';
$dbhost = '';
$dbuser = '';
$dbpass = '';

/*
* Write Database login information
*/
$write_dbname = '';
$write_dbhost = '';
$write_dbuser = '';
$write_dbpass = '';

/*
*   Redis server information
*/
$redis_server = '';
$redis_pass = '';