<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib' . PATH_SEPARATOR . __DIR__ . '/');
require_once 'Net/SSH2.php';
class mySSH extends NET_SSH2{
	public function updateLicense($licensekey){
		$this->setTimeout(2);
		$res = $this->read();
		$this->write("set system licensekey -i << EOF\n");
		$this->write($licensekey . "\n");

		$this->write("EOF\n");
		$res.=$this->read();
		return $res;
	}
}