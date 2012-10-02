<?php

class Controller{
	public function __construct($app){
		$this->app = $app;
	}
	protected function render($file, $data = array()){
		$loader = new Twig_Loader_Filesystem('/var/www/' . BASE_DIR . '/app/views');
		$options = array();
		if(DEV_ENV){
			$options['debug'] = true;
		} else {
			$options['cache'] = 'tmp';
		}
		$twig = new Twig_Environment($loader,$options);
		if(DEV_ENV)
			$twig->addExtension(new Twig_Extension_Debug());
		array_merge($data, $this->app);

		echo $twig->render($file, $data);
	}
}