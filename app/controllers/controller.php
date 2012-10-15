<?php

class Controller{
	public function __construct($app, $db, $writedb, $redis){
		global $user, $options;
		$this->user = $user;
		$this->redis = $redis;
		$this->writedb = $writedb;
		$this->app = $app;
		$this->db = $db;
		$this->sqs = new AmazonSQS($options);
	}
	protected function render($file, $data = array()){
		$maxage = time() + (365 * 24 * 60 * 60);
		header("Strict-Transport-Security:max-age=$maxage; includeSubDomains");
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
		$data['perms'] = $this->user->permissions;
		$data['active'] = $this->app['active'];
		$data['root'] = PROTOCOL.ROOT."/";

		$errors = (isset($_SESSION['errors'])) ? $_SESSION['errors'] : array();
		unset($_SESSION['errors']);
		foreach($errors as $err){
			$data['errors'][] = $err;
		}

		$flash = (isset($_SESSION['flash'])) ? $_SESSION['flash'] : array();
		unset($_SESSION['flash']);
		foreach($flash as $fl){
			$data['flash'][] = $fl;
		}
		$data['db_data'] = $this->db->printlog();
		$this->app['run-time'] = round((microtime(true) - $this->app['start']) * 1000, 3);
		unset($this->app['start']);
		$twig->getExtension('core')->setTimezone(TIMEZONE);
		//$data['app']['db_data'] = $this->db->printog();
		$data['app'] = $this->app;
		echo $twig->render($file, $data);
	}
}