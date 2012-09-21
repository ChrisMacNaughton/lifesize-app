<?php
include 'app/classes/passwordhash.php';
$random_pass = substr(sha1(microtime(true) . rand(1,1000)), 2, 10);
header("Content-Type: text/plain");
echo "pass used for test: $random_pass\n\n";
for ($i=1;$i<18;$i++){
	$hasher = new PasswordHash($i,false);
	$start = microtime(true);
	$hasher->HashPAssword($random_pass);
	$final = microtime(true) - $start;
	echo "For $i iterations: " . $final . "ms\n";
	ob_flush();
	if($final/1000 > 10) break;
}

echo "It took more than 10 seconds for the last iteration";