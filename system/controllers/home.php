<?php
$devices = $db->query("SELECT devices.*, makes.name as Make, models.name as Model
FROM devices
LEFT JOIN makes
ON devices.make_id = makes.id
LEFT JOIN models
ON devices.model_id = models.id WHERE company_id = '" . $user->getCompany() . "'");
$data['devices'] = $devices->fetchAll();
render('home.html.twig', $data);