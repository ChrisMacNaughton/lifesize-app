<?php
$type = "editor";
if(!isset($argc)) $type .= "-web";

require_once('common.php');
define('MAX_editor', 1);
$db->query("DELETE FROM updater_log WHERE type = 'editor'");
$time = (int)time() - 60;
$query = "SELECT count(distinct updater_id) AS count FROM updater_log WHERE (type = 'editor-web' OR type = 'editor') AND `timestamp` > " . $time;
//echo "\n$query\n";
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];
$device_count_check = time();
if ($current_devices == MAX_editor OR $current_devices > MAX_editor){
	die('Already at max updaters of ' . 1 . " ( $current_devices )\n");
}

ulog($updater_log, 'Initialized');

$sqs = new AmazonSQS($options);
$tmp = $db->query("SELECT value FROM settings WHERE setting = 'pre-key'")->fetch(PDO::FETCH_ASSOC);
$pre_key = $tmp['value'];
$key = hash('sha256', $pre_key, true);
$queue_url = "https://sqs.us-east-1.amazonaws.com/626951566381/device_updates";
$updater_log = false;
while(true){
	if($device_count_check < time() - 300) {
		$time = (int)time() - 60;
		$query = "SELECT count(distinct updater_id) AS count FROM updater_log WHERE (type = 'editor-web' OR type = 'editor') AND `timestamp` > " . $time;
		//echo "\n$query\n";
		ulog($updater_log, 'updating count');
		$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
		$current_devices = $res["count"];
		if($current_devices > MAX_editor) die('too many editors\n\n');
		$device_count_check = time();
	}
	
	$queue_size = $sqs->get_queue_size($queue_url);
	ulog($updater_log, 'checking queue', $queue_size);
	if($queue_size > 0){
		
		$messages_raw = $sqs->receive_message($queue_url, array('MaxNumberOfMessages'=>1,'VisibilityTimeout' => 60));
		if($messages_raw->isOK()) {

			$message = $messages_raw->body->ReceiveMessageResult->to_array();
			$message = $message['Message'];
			if(!is_null($message) && ($message != '')){
				$receipt_handle = $message['ReceiptHandle'];

				if(md5($message['Body']) != $message['MD5OfBody']){
					ulog($updater_log, "Message is invalid");
					break;
				}
				$message = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key,base64_decode($message['Body']), MCRYPT_MODE_ECB));


				$message['password']=mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key,base64_decode($message['password']), MCRYPT_MODE_ECB);
				ulog($updater_log, 'working on ' .$message['device_id']);
				$sqs->delete_message($queue_url, $receipt_handle);
				//emulate 2 seconds of processing
				sleep(2);
		} else {
			ulog($updater_log, 'empty queue, sleeping');
			sleep(1);
		}}
	} else {
		ulog($updater_log, 'sleeping...');
		sleep(5);
	}

}