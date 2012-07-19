<?php

function render($file, $data) {
	$loader = new Twig_Loader_Filesystem('system/views');
	$twig = new Twig_Environment($loader, array(
		'cache'=>'false',
		'debug'=>true
	));
	if (isset($_SESSION['flash'])) {
		$data['flash'] = $_SESSION['flash'];
		unset($_SESSION['flash']);
	}
	echo $twig->render($file, $data);
}