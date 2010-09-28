<?php
//	VERSION 1.0.2
//	DATE: 9.22.10

require_once('connect.php');
require_once('functions.php');


if(isset($_GET['camera'])){
	$camera=$_GET['camera'];
	$desc=$_POST['desc'];
	$sql = "update camera set description=\"$desc\" where camera_id=$camera";
	$result = mysql_query($sql);
}

for($camnum=1;$camnum<=numcamera();$camnum++){
	$sql = "select description from camera where camera_id=$camnum";
	$result = mysql_query($sql);
	$info = mysql_fetch_array($result,MYSQL_ASSOC);

	echo "<p><font size=\"6\"><u>Camera $camnum</u></font><br>";

	echo "<form action='manage.php?camera=$camnum' method='post'>";
	echo "Description: ";
	echo "<textarea name='desc' rows='1'>";
	echo $info['description'];
	echo "</textarea><br><br>";
	echo "<input type='submit' value='Edit'>";
	echo "</form>";
	echo "<br><br>";
}
?>
