<?php

class webhookController Extends Controller{
	public function indexAction(){
		$stmt = $this->db->prepare("INSERT INTO webhooks (id, hook) VALUES (:id, :hook)");
		$id = 'webhook-' . substr(sha1(rand(1,1000).microtime(true)),0,10);
		$hook = json_encode($_POST);

		$stmt->execute(array(':id'=>$id,':hook'=>$hook));

		header("HTTP/1.0 200 OK");
	}	
}