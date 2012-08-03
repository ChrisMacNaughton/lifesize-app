<?php

function load($classname) {
	$path = BASE_PATH . '/app/classes/'. strtolower($classname) .'.php';
	if (file_exists($path)) {
		include_once $path;
		return;
		}
	$path = BASE_PATH . '/app/controllers/' . $classname . '.php';
	if (file_exists($path)){
		include_once $path;
		return;
	}
}

spl_autoload_register('load');