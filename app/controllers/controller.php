<?php

class Controller{
	public function __construct($app, $db){
		global $user;
		$this->user = $user;
		$this->app = $app;
		$this->db = $db;
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
		
		$data['user'] = $this->user;
		$data['active'] = $this->app['active'];
		$data['root'] = PROTOCOL.ROOT."/";

		$errors = (isset($_SESSION['errors'])) ? $_SESSION['errors'] : null;

		foreach($errors as $err){
			$data['errors'][] = $err;
		}
		$data['db_info'] = $this->db->printlog();
		$this->app['run-time'] = round((microtime(true) - $this->app['start']) * 1000, 3);
		unset($this->app['start']);

		$data['app'] = $this->app;
		echo $twig->render($file, $data);
	}
}