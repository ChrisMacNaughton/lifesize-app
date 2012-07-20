<?php
if (!isset($_GET['id'])) {
	$result = $db->query("SELECT * FROM devices WHERE online = 1");
	$devices = $result->fetchAll();
	foreach ($devices as $device) {
		$ch = curl_init();
		$url = 'http://' . PATH. '/update?id=' . $device['id'];
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

		curl_exec($ch);
		curl_close($ch);
	}
} else {
	$stmt = $db->prepare("SELECT ip, id, password FROM devices WHERE id = :id");
	$stmt->execute(array(':id'=>$_GET['id']));
	$device = $stmt->fetch();
	$ls = new Lifesize($device['ip'], $device['password']);
	$system = array();
	$query = "UPDATE devices SET name = :name, calling = :calling, license = :license, version = :version, make_id = :make, model_id = :model, updated = :updated WHERE id = :device_id";
	$stmt = $db->prepare($query);
	if ($ls->connected()) {
		$lifesize = $ls->update();
		$system = array(
			':device_id'=>$device['id'],
			':name'=>$lifesize['name'],
			':calling'=>$lifesize['calling'],
			':license'=>$lifesize['license'],
			':version'=>$lifesize['version'],
			':make'=>$lifesize['make'],
			':model'=>$lifesize['model'],
			':updated'=>time()
		);
		$result = $db->query("SELECT id FROM makes WHERE name = '" . $system[':make'] . "'");
		$result =  $result->fetch();
		$system[':make'] = $result['id'];
		$result = $db->query("SELECT id FROM models WHERE name = '" . $system[':model'] . "'");
		$result =  $result->fetch();
		$system[':model'] = $result['id'];
		//echo "<pre>";print_r($system); echo "</pre>";
		if ($stmt->execute($system)) {
		 echo "Success!";
		} else {
			echo "Failure";
		}
	}
	
	//echo "$query";
	//echo "<pre>"; print_r($system); echo "</pre>";
}