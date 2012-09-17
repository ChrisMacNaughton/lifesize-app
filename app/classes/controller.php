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
		$res = $db->query("SELECT app_key FROM apps WHERE id='app-controlVC' AND active=1 LIMIT 1");
		$res = $res->fetch(PDO::FETCH_ASSOC);
		$this->app_key = $res['app_key'];
		$res = $db->query("SELECT access_id, secret FROM api_keys WHERE app_key = '" . $this->app_key . "' LIMIT 1");
		$res = $res->fetch(PDO::FETCH_ASSOC);
		$this->access_id = $res['access_id']; $this->secret = $res['secret'];
		global $user;
		$this->user = $user;
	}
	public function render($file, ARRAY $data = null) {
		$this->app['controller'] = $this->controller;
		$this->app['action'] = $this->action;
		global $app, $path;
		$loader = new Twig_Loader_Filesystem('app/views');
		$twig = new Twig_Environment($loader, $this->app['twig_options']);

		if (!isset($data['flash']))
		$data['flash'] = array();
		if (!isset($data['errors']))
		$data['errors'] = array();
		//echo "<!-- Controller: " . $this->app['controller'] . "  :  Action: " . $this->app['action'] . " -->";
		if($this->app['controller'] == 'user' && $this->app['action'] == 'login') {
			$login = true;
		}
		else {
			$login = false;
		}
		$twig->addExtension(new Twig_Extension_Debug());
		if (!$login) {
			$start = $this->app['start'];
			unset($this->app['start']);
			$this->app['system']['path'] = PATH;
			$this->app['system']['load_time'] = microtime_diff($start);
			$data['app'] = $this->app;
			$data['app']['system']['protocol'] = (DEV_ENV == true) ? 'http' : "https";
			$data['app']['user'] = $this->user->getUser();
			$data['app']['company'] = $this->company;
			$data['app']['app_key'] = $this->app_key;
			$data['app']['accessId'] = $this->access_id;
			$data['app']['quick_expire'] =time() + 15;
			$string = $this->access_id . chr(0x0D) . $data['app']['quick_expire'];
			$data['app']['quick_sig'] = urlencode(base64_encode(hash_hmac('sha1', $string, $this->secret, true)));
			$data['app']['long_expire'] =time() + 300;
			$string = $this->access_id . chr(0x0D) . $data['app']['long_expire'];
			$data['app']['long_sig'] = urlencode(base64_encode(hash_hmac('sha1', $string, $this->secret, true)));
			
		} 
		foreach ($_SESSION['errors'] as $err) 
			$data['errors'][] = $err;
		unset($_SESSION['errors']);
		foreach ($_SESSION['flash'] as $flash)
			$data['flash'][] = $flash;
		unset($_SESSION['flash']);
		$data['app']['session'] = $_SESSION;
		if($this->user->getLevel() == 4){
			$data['app']['new_relic'] = (NEW_RELIC)?"enabled":"disabled";
			if(NEW_RELIC){

			}

			$data['app']['db_data'] = $this->db->printLog();
		}
		ksort($data['app']);ksort($data);

		echo $twig->render($file, $data);
	}
}