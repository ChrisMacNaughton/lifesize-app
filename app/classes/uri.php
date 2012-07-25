<?php

class URI {
	public $seg = array();
	private $root;
	private $request;
	private $file = 'index.php';
	
	public function __construct() {
		$this->root = str_replace($this->file, ' ', $_SERVER['SCRIPT_NAME']);
		$req = explode('?', $_SERVER['REQUEST_URI']);
		$this->request = $req[0];
		$this->seg = explode('/', trim($this->request, '/'));
		foreach(explode('/',$this->root) as $seg => $val) 
			if(!empty($val) && $val != ' ') array_shift($this->seg);
		
		// Remove the index.php if it's in there...
		if ($this->seg[0] == $this->file){
			array_shift($this->seg);
		}
	}
	public function seg($seg)
	{
		if(isset($this->seg[$seg])) return $this->seg[$seg];
		return false;
	}
}