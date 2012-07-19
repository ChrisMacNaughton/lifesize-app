<?php

function render($file, $data) {
	$loader = new Twig_Loader_Filesystem('system/views');
	$twig = new Twig_Environment($loader, array(
		'cache'=>'false',
		'debug'=>true
	));
	
	echo $twig->render($file, $data);
}