<?php
error_reporting(E_ALL);
$api_key = "25fc643b758288a934190ed3e1e7f3b2260bdccff7fab45";
$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 'x-api-key');
curl_setopt($ch, CURLOPT_HTTPHEADER, array("x-api-key: $api_key"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

/*
	Application summary
*/


curl_setopt($ch, CURLOPT_URL, "https://rpm.newrelic.com/accounts/179828/applications/847442/threshold_values.xml");
$res = curl_exec($ch);
$data = simplexml_load_string($res);

$final = array();
foreach($data->threshold_value as $a) {
	$a = $a->attributes();
	//print_r($a);echo $a->name . ' - ' . $a->metric_value;
	$name = (string)$a->name;
	$final[$name] = (double)$a->metric_value;
}
//print_r($final);
header("Content-Type: application/json");
echo json_encode($final);

/*
	Aplpication Dashboard
*/
/*
curl_setopt($ch, CURLOPT_URL, "https://api.newrelic.com/application_dashboard?application_id=847442");
$res = curl_exec($ch);
echo $res;
*/