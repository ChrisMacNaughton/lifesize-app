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

if(empty($uri->seg[1])) {
	$data['devices'] = $devices;
	render('device/index.html.twig', $data);
} else if ($uri->seg[1] == 'add') {
	if (!$data['new_device']) {
		$_SESSION['errors'][] = l('error_max_devices');
		header("Location: devices");
	} else {
		if (isset($_POST['action']) && $_PSOT['action'] == 'add') {
			
		}
		render('device/add.html.twig', $data);
	}
}