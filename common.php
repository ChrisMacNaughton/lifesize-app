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