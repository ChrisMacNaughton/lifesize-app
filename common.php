<?php

function render($file, $data = null) {
	global $user, $uri;
	$loader = new Twig_Loader_Filesystem('system/views');
	$twig = new Twig_Environment($loader, array(
		'cache'=>'false',
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
	return $string;
}
function ping($host) {
    exec(sprintf('ping -n 1 -w 1000 %s', escapeshellarg($host)), $res, $rval);
	
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