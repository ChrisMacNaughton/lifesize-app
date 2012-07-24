<?php

function render($file, $data = null) {
	global $user, $uri;
	$loader = new Twig_Loader_Filesystem('system/views');
	$twig = new Twig_Environment($loader, array(
		'cache'=>false,
		'debug'=>true
	));
	$data['page'] = $uri->seg[0];
	if (isset($_SESSION['flash'])) {
		$data['flash'] = $_SESSION['flash'];
		unset($_SESSION['flash']);
	}
	if (isset($_SESSION['errors'])) {
		$data['errors'] = $_SESSION['errors'];
		unset($_SESSION['errors']);
	}
	$data['user'] = $user->userinfo();
	$twig->addExtension(new Twig_Extension_Debug());
	
	echo $twig->render($file, $data);
}
function l($string) {
	global $lang;
	
	// Check if the string exists
	if(!isset($lang[$string])) return '['.$string.']';
	
	// Get the locale string
	$string = $lang[$string];
	
	// Check if the $vars is an array or use the function args.
	if(!is_array($vars)) $vars = array_slice(func_get_args(),1);
	
	// Loop through the vars and replace the the {x} stuff
	foreach($vars as $var)
	{
		if(!isset($v)) $v = 0;
		++$v;
		$string = str_replace('{'.$v.'}',$var,$string);
	}
	
	return $string;
}
function ping($host) {
    exec(sprintf('ping -n 1 -w 500 %s', escapeshellarg($host)), $res, $rval);
	
    return $rval === 0;
}
function lifesizeSplit($string) {
	$string = explode(chr(0x0a), $string);
	return $string;
}
function time_to_seconds($start) {
	$time = explode(':', $start);
	$hours = $time[0];
	$minutes = $time[1];
	$seconds = $time[2] + ($minutes * 60) + ( $hours * 60 * 60);
	return $seconds;
}
function settings($setting)
{
	global $CACHE, $db;
	
	// Check if the setting has already been fetched
	// and return it if it has.
	if(isset($CACHE['settings'][$setting])) return $CACHE['settings'][$setting];
	
	// Looks like the setting isn't in the cache,
	// lets fetch it now...
	$stmt = $db->prepare("SELECT setting, value FROM settings WHERE setting= :setting");
	$stmt->execute(array(
		':setting'=>$setting
	));
	$result = $stmt->fetch();
	$CACHE['settings'][$setting] = $result['value'];
	
	return $CACHE['settings'][$setting];
}