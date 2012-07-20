<?php
$company_id =  $user->getCompany();
$data = array();
$stmt = $db->prepare("SELECT max FROM companies WHERE id = :company_id");
$stmt->execute(array(':company_id'=>$company_id));
$max = $stmt->fetch();
$max_devices = $max['max'];

$stmt = $db->prepare("SELECT * FROM devices WHERE company_id = :company_id");
$stmt->execute(array(
	':company_id'=>$company_id
));
$devices = $stmt->fetchAll();
if (count($devices) < $max_devices) {
	$data['new_device'] = true;
}
$data['devices'] = $devices;
if(empty($uri->seg[1])) {
	
	render('device/index.html.twig', $data);
} else if ($uri->seg[1] == 'add') {
	if (!$data['new_device']) {
		$_SESSION['errors'][] = l('error_max_devices');
		header("Location: devices");
	} else {
		if (isset($_POST['action']) && $_POST['action'] == 'add') {
			$stmt = $db->prepare("SELECT * FROM devices WHERE ip = :ip AND company_id = :id");
			$stmt->execute(array(
				':ip'=>$_POST['ip'],
				':id'=>$company_id
			));
			$devs = $stmt->fetchAll();
			if (count($devs) > 0) {
				$_SESSION['errors'][] = l('error_device_already_exists');
			} else {
				$stmt = $db->prepare("INSERT INTO devices (`ip`, `company_id`, `added`, `password`) VALUES (:ip, :id, :added, :password)");
				$result = $stmt->execute(array(
					':ip'=>$_POST['ip'],
					':id'=>$company_id,
					':password'=>$_POST['password'],
					':added'=>time()
				));
				if ($result) {
					$_SESSION['flash'][] = l("success_adding_device");
					$ch = curl_init();
					$url = 'http://' . PATH. '/ping?id=' . $db->lastInsertId();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
					curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

					curl_exec($ch);
					curl_close($ch);
				} else {
					$_SESSION['errors'][] = l("error_adding_device");
				}
			}
		}
		render('device/add.html.twig', $data);
	}
}