<?php
include 'app/classes/passwordhash.php';
set_time_limit(0);
$random_pass = substr(sha1(microtime(true) . rand(1,1000)), 2, 10);
header("Content-Type: text/plain");
echo "pass used for test: $random_pass\n\n";
for ($i=1;$i<=18;$i++){
	$hasher = new PasswordHash($i,false);
	$start = microtime(true);
	$hasher->HashPassword($random_pass);
	$final = microtime(true) - $start;
	echo "For ".pow(2,$i)." iterations($i): " . round($final, 5) . "s\n";
	flush();$hasher = null;
	if($final > 30){
		echo "It took more than 30 seconds for the last iteration";
		break;
	}
}

