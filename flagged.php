<?php
//	VERSION 1.0.2
//	DATE: 9.22.10

require_once('connect.php');
require_once('functions.php');
require_once('display.php');

// Remove flag
if($_GET['flag']==0)
	deflag($_GET['idvid'], $_GET['idpic']);

echo "<u><h1 align=\"center\">Flagged</h1></u>";

$checkarray= array (1 => $_GET['camera1'], 2 => $_GET['camera2'], 3 => $_GET['camera3'], 4 => $_GET['camera4'], 5 => $_GET['camera5'], 6 => $_GET['camera6'], 7 => $_GET['camera7'], 8 => $_GET['camera8'], 9 => $_GET['camera9']);

//check all for first time
if($_GET['first']==1)
	for($count=1;$count<=numcamera();$count++)
		$checkarray[$count]=1;

echo "<form action=\"flagged.php?second=1\" method=\"get\">";

for($count=1;$count<=numcamera();$count++){
	echo "Camera $count:";
	$checked = ($checkarray[$count]==1) ? "checked" : "";
	echo "<input type='checkbox' name='camera$count' value='1' $checked>";
	echo "<br>";
}

echo "<br>";
echo "<input type='submit' value='Add/Remove Cameras'>";
echo "</form>";

// If no camera is specified
for($count=1;$count<=numcamera();$count++){
	if($checkarray[$count]==1) {
		break;
	}
}



// generate sql 
$sql = "select * from video where flagged=1 and ( ";
$first=0;
for($camnum=1; $camnum<=numcamera(); $camnum++) {
	if($checkarray[$camnum]==1) {
		if($first==0) {
			$first = 1;
			$sql .= "camera_id = $camnum";
		} else {
			$sql .= " or camera_id = $camnum";
		}
	}
}

// if no cameras selected
if($first == 0) {
	echo "<p>Please select a camera</p>";
	$sql = "";
} else {
	$sql .= ") order by time";
}

$action="flagged.php?";
for($x=1;$x<=numcamera()&&$x<9;$x++)
		$action.="&camera$x=$checkarray[$x]";
display($sql,$action);
?>
