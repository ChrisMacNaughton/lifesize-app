<?php

class Controller {
	protected $controller, $action, $app, $db, $user;
	public function beforeAction() {
		$table = null;
		switch ($this->controller)  {
			case 'events':
				$table = 'events';
				break;
		}
		if (!is_null($table)){
			$options = array(
				':id'=>$this->user->getCompanyId()
			);
			$stmt = $this->db->prepare("SELECT * FROM $table WHERE company_id = :id");
			$stmt->execute($options);
			$this->$table = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	public function __construct($controller, $action, $app, $db) {
		$this->controller = $controller;
		$this->action = $action;
		
		$this->app = $app;
		$this->db = $db;
		global $user;
		$this->user = $user;
	}
	public function render($file, ARRAY $data = null) {
		global $app, $path;
		$loader = new Twig_Loader_Filesystem('app/views');
		$twig = new Twig_Environment($loader, array(
			'cache'=>false,
			'debug'=>true
		));
		if (!isset($data['flash']))
		$data['flash'] = array();
		$twig->addExtension(new Twig_Extension_Debug());
		$start = $this->app['start'];
		unset($this->app['start']);
		$this->app['system']['path'] = PATH;
		$this->app['system']['load_time'] = microtime_diff($start);
		$data['app'] = $this->app;
		$data['app']['system']['protocol'] = "http";
		$data['app']['user'] = $this->user->getUser();
		foreach ($_SESSION['errors'] as $err) 
		$data['errors'][] = $err;
		unset($_SESSION['errors']);
		foreach ($_SESSION['flash'] as $flash)
			$data['flash'][] = $flash;
		unset($_SESSION['flash']);
		$data['app']['session'] = $_SESSION;
		echo $twig->render($file, $data);
	}
}