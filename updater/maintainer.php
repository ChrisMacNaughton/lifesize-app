<?php

if(function_exists(newrelic_disable_autorun)){
	newrelic_disable_autorun();
	newrelic_ignore_apdex();
}
$type = "Maintainer-web";
require_once("common.php");

$time = (int)time() - (30 * 60);
$query = "SELECT count(distinct updater_id) AS count FROM updater_log WHERE (type = 'Maintainer-web' OR type='Maintainer') AND `timestamp` > " . $time;
//echo "\n$query\n";urrent_devices
$res = $db->query($query)->fetch(PDO::FETCH_ASSOC);
$current_devices = $res["count"];
if($current_devices >= 1) {
	die();
}
ulog($updater_log, 'Initialized');

Stripe::setApiKey($stripe_key);
$log_cleaner = $db->prepare("DELETE * FROM updater_log WHERE timestamp < :time");
$company_list_stmt = $db->prepare("SELECT id, customer_id, subscription_id FROM companies WHERE synced < :time");
$update_company = $db->prepare("UPDATE companies SET last4 = :last4, active=:active, subscription_id = :plan_id, `interval` = :interval, synced = :time");
while(true) {
	$time = time();
	ulog($updater_log, "cleaning up", "updater's log");
	$log_cleaner->execute(array( ':time'=>$time - 24 * 60 * 60));

	ulog($updater_log, "Initiating company sync");
	$company_list_stmt->execute(array(':time'=>$time - 24*60*60));
	$companies = $company_list_stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($companies as $c) {
		$company = Stripe_customer::retrieve($c['customer_id']);
		//print_r($company);
		if($company['active_card'])
			$last4 = $company['active_card']['last4'];
		else
			$last4 = null;
		$subscription = $company['subscription'];
		$plan_id = $subscription['plan']['id'];
		$active = ($subscription['status'] == 'active') ? 1 : 0;
		//echo "Last 4: $last4\nPlan: $plan";//print_r($plan);
		$plan = explode('_', $plan_id);
		$rate = $plan[0]; $max = $plan[1]; $interval = $plan[2];
		$update_company->execute(array(
			':last4'=>$last4,
			':plan_id'=>$plan_id,
			':interval'=>$interval,
			':time'=>$time,
			':active'=>$active
			));
		$errors = $update_company->errorInfo();
		if($errors[0] != '0000')
			print_r($errors);
	}
	sleep(1 * 30 * 60);
}