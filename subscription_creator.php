<?php
require_once 'system/config.php';
//require 'system/classes/loggedPDO.php';
require_once 'vendor/autoload.php';
Stripe::setApiKey($stripe_key);

$plans = array(
	"basic",
	"pro"
);
$rates = array(
	"basic"=>10,
	"pro"=>15
);

for($i=1; $i<=100; $i++){
	foreach($plans as $plan){
		$rate = $rates[$plan];
		$amount = $i * $rate * 100;

		$name = $plan . '-' . $i . '-' . $rate;
		try{
			$tmp = Stripe_Plan::retrieve($name);
			$tmp->delete();
			} catch(Exception $e){
				print("No Plan!\n");
			}	
		$options = array(
			"amount" => $amount,
			"interval" => "month",
			"name" => ucfirst($plan) .' ' . $i . ' Devices',
			"currency" => "usd",
			"id" => $name);
		print_r($options);
		Stripe_Plan::create($options);

	}
}
/*
Stripe_Plan::create(array(
  "amount" => $i * $rate,
  "interval" => "month",
  "name" => "Amazing Gold Plan",
  "currency" => "usd",
  "id" => "gold")
);
*/
