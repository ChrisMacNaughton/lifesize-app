<?php
$type = "iface";
if(!isset($argc)) $type .= "-web";

require_once('common.php');
$updater_log = false;

$sqs = new AmazonSQS($options);

$tmp = $db->query("SELECT value FROM settings WHERE setting = 'pre-key'")->fetch(PDO::FETCH_ASSOC);
$pre_key = $tmp['value'];
$key = hash('sha256', $pre_key, true);

$queue_url = "https://sqs.us-east-1.amazonaws.com/626951566381/device_updates";

$devices = $db->query("SELECT * FROM devices LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
foreach($devices as $device){
	$serialized = serialize(array(
		'device_id'=>$device['id'],
		'ip'=>$device['ip'],
		'password'=>base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $device['password'], MCRYPT_MODE_ECB)),
		'request'=>array(
			'some'=>'stuff',
			'more'=>'stuff'
		)
	));
	ulog($updater_log, $serialized);
	$sqs->send_message($queue_url, base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $serialized, MCRYPT_MODE_ECB)));
}