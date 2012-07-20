<?php
if (!isset($_GET['id'])) {
	$result = $db->query("SELECT * FROM devices");
	$devices = $result->fetchAll();
	
	foreach ($devices as $device) {
		$ch = curl_init();
		$url = 'http://' . PATH. '/ping?id=' . $device['id'];
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

		curl_exec($ch);
		curl_close($ch);
	}
} else {
	$stmt = $db->prepare("SELECT * FROM devices WHERE id = :id");
	$stmt->execute(array(':id'=>$_GET['id']));
	$device = $stmt->fetch();
	
	$stmt = $db->prepare("UPDATE devices SET online = :online, online_updated = :updated WHERE id = :id");
	$online = (ping($device['ip']) == 1) ? 1 : 0;
		$stmt->execute(array(
			':online'=>$online,
			':updated'=>time(),
			':id'=>$device['id']
		));
}