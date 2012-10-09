<?php

class webhookController Extends Controller{
	public function indexAction(){
		$stmt = $this->db->prepare("INSERT INTO webhooks (id, hook) VALUES (:id, :hook)");
		$id = 'webhook-' . substr(sha1(rand(1,1000).microtime(true)),0,10);

		$body = @file_get_contents('php://input');
		$event_json = json_decode($body, true);

		$hook = json_encode(array("Post"=>$_POST, "Get"=>$_GET, "Request"=>$_REQUEST, "Body"=>$event_json));

		$stmt->execute(array(':id'=>$id,':hook'=>$hook));

		header("HTTP/1.0 200 OK");
	}	
}