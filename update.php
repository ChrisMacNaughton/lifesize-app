<?php

$devices = array(
	'dev-0897f2ff3d'=>'HH654204AEB4,HK654304AEB47',
	'dev-346bac3a5e'=>'HH654204AF58,HK654404AF5870',
	'dev-426ab2bdeb'=>'HH654204AF10,HK654404AF105',
	'dev-a054c946fd'=>'GP652203C3D0,GQ671003C3D05',
	'dev-xedgw45ydr'=>'HH654204AD48,HK654404AD484',
	'new'=>''
);

foreach($devices as $id=>$dev){
	print("$id => " . sha1($dev) . "\n");
}