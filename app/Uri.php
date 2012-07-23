<?php

class URI
{
	public $seg = array();
	public $style = 1;
	private $root;
	private $request;
	private $file = 'index.php';
	
	// Construct function.
	public function __construct()
	{
		// Get the root
		$this->root = str_replace($this->file,'',$_SERVER['SCRIPT_NAME']);
		
		// Get the request without the query string
		$req = explode('?',$_SERVER['REQUEST_URI']);
		$this->request = $req['0'];
		
		// Explode the segments
		$this->seg = explode('/',trim($this->request,'/'));
		
		// Remove the crap
		foreach(explode('/',$this->root) as $seg => $val)
			if(!empty($val)) array_shift($this->seg);
		
		// Remove the index.php if it's in there...
		if(@$this->seg['0'] == 'index.php') array_shift($this->seg);
	}
	
	/**
	 * Get URI
	 * Used to get the current URI
	 */
	public function geturi()
	{
		return $this->anchor($this->seg);
	}
	
	public function seg($seg)
	{
		if(isset($this->seg[$seg])) return $this->seg[$seg];
		return false;
	}
	
	public function anchorfile() { return $this->file; }
	
	/**
	 * Anchor
	 * Used to create URI's
	 */
	public function anchor($segments = array())
	{
		if(!is_array($segments))
			$segments = func_get_args();
		
		$path = ($this->style == 1 ? str_replace($this->file,'',$_SERVER['SCRIPT_NAME']) : $_SERVER['SCRIPT_NAME'].'/');
		return $path.$this->array_to_uri($segments);
	}
	
	// Used to convert the array passed to it into a URI
	private function array_to_uri($segments = array())
	{
		if(count($segments) < 1 or !is_array($segments)) return;
		
		foreach($segments as $key => $val)
			$segs[] = $val;
			
		return implode('/',$segs);
	}
}
?>