<?php
include 'config.php';
include 'loggedPdo.php';
//print_r($argv);
$debug = (isset($argv[1]) && $argv[1] == 'debug') ? true : false;
echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
try {
    $db = new loggedPDO('mysql:dbname=' . $dbname . ';host=' . $dbhost, $dbuser, $dbpassword
	    	//, array(PDO::ATTR_PERSISTENT => true)
	    	);
} catch (PDOException $e) {
    die("Error connecting to the database: " .  $e->getMessage());
}
$data = array();
echo "What would you like to do?\n";
echo "1) Change a user level\n";
$option = fgets(STDIN);
echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
switch($option) {
	case 1:
		echo "Changing a user level\n====================\n";
		echo "Do you know the userid?(y/n): ";
		$know = rtrim(fgets(STDIN));
		echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
		if($know == 'y'){
			echo "Enter the userid: "; $userId = rtrim(fgets(STDIN));
			echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
			$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
			$stmt->execute(array(':id'=>$userId));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if(is_null($res) || $res == '') {
				$data['errors'][] = "Invalid UserID";
				break;
			}
		} else {
			echo "User Email: "; $email = rtrim(fgets(STDIN));
			echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
			echo "CompanyId: "; $company = rtrim(fgets(STDIN));
			echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
			$stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND company_id = :company LIMIT 1");
			$success = $stmt->execute(array(':email'=>$email, ':company'=>$company));
			//print_r($stmt->errorInfo());
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if(is_null($res) || $res == '') {
				$data['errors'][] = "Email / CompanyID pair is invalid";
				break;
			}
		}
		//print_r($res
		$userId = $res['id'];
		$current_level = $res['level'];
		$username = $res['name'];
		echo "User: { ID: $userId | Name: $username } | Current Level: $current_level\n\n";
		echo "New Level: "; $new_level = rtrim(fgets(STDIN));
		echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
		echo "You want user { ID: $userId | Name: $username } to have a level of $new_level? (y/n) "; $verify = rtrim(fgets(STDIN));
		echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
		if ($verify == 'y') {
			$stmt = $db->prepare("UPDATE users SET level = :level WHERE id = :id");
			$success = $stmt->execute(array(
				':id'=>$userId,
				':level'=>$new_level
			));
			if($success)
				$data['info'] = "Successfully updates user $userId";
			else 
				$data['errors'][] = "Failed to update user $userId";
		} else {
			$data['errors'][] = "Cancelled user level change";
			break;
		}
		break;
	default:
		$data['errors'][] = "You must make a choice";
		break;
}
if($debug === true)
	print_r($db->printLog());
print_r($data);
exit(0);