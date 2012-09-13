<?php

//echo hash('sha256', rand(1,1000000) . 'app_key' . microtime(true));
error_reporting(E_ALL^E_NOTICE);
$ch = curl_init();

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("App-Key: "));



//
//curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
$url = "http://localhost/apiv2";
//$url = "https://api.control.vc";
$url .= "/devices?debug=true&accessId=user-hbfuey3&expires=1356769278&signature=dR0c4trnai7gKmPWGV%2FPm2cvcGM%3D";

$POST = array(
	'id'=>"dev-0897f2ff3d",
	'name'=>'Sunbelt Houston',
);
foreach($POST as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
curl_setopt($ch,CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
$res = curl_exec($ch);

$info = curl_getinfo($ch);
$status = $info['http_code'];

//echo $res;

$devices = json_decode($res, true);

header("Content-Type: text/plain");
print_r($devices);