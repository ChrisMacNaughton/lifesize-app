<?php
include 'config.php';
$ips = array(
	'',
	'4.26.209.14',
);
if($_SERVER['SERVER_ADDR'] != '127.0.0.1' || (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && array_search($_SERVER['HTTP_X_FORWARDED_FOR'], $ips) ) ){
	header("Status: 404");
	?>
	<!-- <?php echo VERSION_ID; ?> -->
	<!-- <?php print_r($_SERVER); ?> -->
	<!-- <?php print_r($ips); ?> -->
	<!-- <?php array_search($_SERVER['HTTP_X_FORWARDED_FOR'], $ips); ?> -->
	<div id="error404" class="content">
		<div class="block">
		<h1><span>Error 404</span></h1>
		<h2><span>Oops!</span></h2>
		<div class="dontWorry">It's looking like you may have taken a wrong turn.
		<br>Don't worry... it happens to the best of us.
		<br /><a href="https://app.control.vc">Back to the app!</a></div>
		          
		</div>
	</div>
	<?php
	die();
}


$dsn = 'mysql:dbname=vcdb;host=vcdb.crwlsevgtlap.us-east-1.rds.amazonaws.com';
try {
    $db = new PDO($dsn, $dbuser, $dbpassword);
} catch (PDOException $e) {
    die("Error connecting to the database: " .  $e->getMessage());
}
$logs = $db->query("SELECT * FROM updater_log ORDER BY `timestamp` DESC LIMIT 10000")->fetchAll(PDO::FETCH_ASSOC);
foreach($logs as $u) {
	$updaters[$u['updater_id']] = substr($u['updater_id'],0,5);
}
echo"<!--";print_r($updaters);echo"-->";
$colors = array(
	'#006633',
	'#6699cc',
	'#663399',
	'#003333',
	'#cc0000',
	'#660099'
	);
?>
<html>
<head>
	<?php /*
<script src="sorter/script.js"></script>
<link href="sorter/style.css" rel="stylesheet">
*/ ?>
<style>
.even {
	background-color: #ccccff;
}
.odd {

}
<?php
$i=0;
foreach ($updaters as $u) {
	if ($i == count($colors)) $i=0;
	echo ".updater-" . $u . "{
	color: " . $colors[$i] . ";
}
";
	$i++;
}
?>
</style>
</head>
<body>
<?php
echo "<h1>Log Analysis</h1>";

#echo "<!--";print_r($logs);echo"-->";
?>
<table id="sortable1" style="width: 95%; text-align: center;">
	<thead>
	<tr>
		<th style="border-bottom: 2px solid #999;">Updater</th>
		<th style="border-bottom: 2px solid #999;">Type</th>
		<th style="border-bottom: 2px solid #999;">Time</th>
		<th style="border-bottom: 2px solid #999;">Action</th>
		<th style="border-bottom: 2px solid #999;">Detail</th>
	</tr>
	</thead>
	<tbody>
<?php $i = 1;
foreach ($logs as $action) { $id = substr($action['updater_id'],0,5); ?>
	<tr><!-- <?php print_r($action); ?>-->
		<td style="border-bottom: 1px solid #ccc;" class="updater-<?php echo $id ?>"><span title="<?php echo $action['updater_id']; ?>"><?php echo $id; ?></span></td>
		<td style="border-bottom: 1px solid #ccc;"><?php echo $action['type']; ?></td>
		<td style="border-bottom: 1px solid #ccc;"><?php echo date('m/d/Y H:i:s',$action['timestamp']/* - 60 * 60 * 7*/); ?></td>
		<td style="border-bottom: 1px solid #ccc;"><?php echo $action['action']; ?></td>
		<td style="border-bottom: 1px solid #ccc;"><?php echo $action['detail']; ?></td>
	</tr>
	<?php $i++;
}
//echo "<pre>";print_r($logs);echo"</pre>";
?>
</tbody>
</table>
<script type="text/javascript">
  var sorter = new TINY.table.sorter("sorter");
	sorter.head = "head";
	sorter.asc = "asc";
	sorter.desc = "desc";
	sorter.currentid = "currentpage";
	sorter.limitid = "pagelimit";
	sorter.init("sortable",0);
  </script>
</body>
</html>