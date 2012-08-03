<?php
/*
require_once 'bootstrap.php';

$ec2 = new AmazonEC2($options);
$ami_id = "ami-976dc3fe";

$instances = $ec2->describe_instances(array(
'Filter'=> array(
	array('Name' => 'image-id', 'Value'=>$ami_id),
	array('Name' => 'instance-state-name', 'Value'=>"running")
)
));
$instances = $instances->body->reservationSet->to_array();
$instances = $instances['item'];
echo "<pre>";
print_r($instances['instancesSet']);
echo "</pre>";

echo "Count: " . count($instances['instancesSet']);