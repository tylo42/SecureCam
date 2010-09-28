<?php
session_start();
/* Copyright 2008, 2009, 2010 Tyler Hyndman
 * 
 * This file is part of SecureCam.
 *
 * SecureCam is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * SecureCam is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with SecureCam.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('connect.php');
require_once('calendar.php');
require_once('functions.php');
require_once('display.php');

// Add/remove flag
if($_GET['flag']==1) {
	flag($_GET['idvid']);
}

if($_GET['flag']==0) {
	deflag($_GET['idvid']);
}

// set up check boxes
$checkarray= array (1 => $_GET['camera1'], 2 => $_GET['camera2'], 3 => $_GET['camera3'], 4 => $_GET['camera4'], 5 => $_GET['camera5'], 6 => $_GET['camera6'], 7 => $_GET['camera7'], 8 => $_GET['camera8'], 9 => $_GET['camera9']);

//check all for first time
if($_GET['first']==1){
	for($count=1;$count<=numcamera();$count++)
		$checkarray[$count]=1;
	$date=getDate();
	$_SESSION['mday']= $date["mday"];
	$_SESSION['mon'] = $date["mon"];
	$_SESSION['year'] = $date["year"];
}
else if(isset($_GET["mday"])){
	$_SESSION['mday']= $_GET["mday"];
	$_SESSION['mon'] = $_GET["mon"];
	$_SESSION['year'] = $_GET["year"];
}

for($x=1;$x<=numcamera();$x++) {
	$checkboxes.="&camera$x=$checkarray[$x]";
}

echo "<link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"style.css\" />\n"; // FIXME: THIS SHOULD NOT GO HERE - ???

//title
echo "<h1>Browse</h1>";

echo "<table border=\"0\" width=\"100%\"><tr><td>";
echo "<form action=\"browse.php\" method=\"get\">";

for($count=1;$count<=numcamera();$count++){
	echo "Camera $count:";
	$checked = ($checkarray[$count]==1) ? "checked" : "";
	echo "<input type='checkbox' name='camera$count' value='1' $checked>";
	echo "<br>";
}
echo "<br>";
echo "<input type='submit' value='Add/Remove Cameras'>";
echo "</form>";

echo "</td><td align=\"right\">";

// Display the calandar
$date["mday"]=$_SESSION['mday'];
$date["mon"]=$_SESSION['mon'];
$date["year"]=$_SESSION['year'];

echo calendar($date,$checkboxes);
echo "</td></table>";

//Display the videos
for($count=1;$count<=numcamera();$count++){
	if($checkarray[$count]==1)
		break;
	if($count==numcamera())
		echo "Please specify a camera <br>";
}

$day = $_SESSION["mday"];
$month = $_SESSION["mon"];
$year = $_SESSION["year"];	

$begin_day = mktime(0, 0, 0, $month, $day, $year);
$end_day = mktime(0, 0, 0, $month, $day+1, $year);

// generate sql 
$sql = "select * from video where $begin_day <= time and time < $end_day and (";
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

$action="browse.php?mday=$day&mon=$month&year=$year";
for($x=1;$x<=numcamera();$x++) {
	$action.="&camera$x=$checkarray[$x]";
}
display($sql,$action);
?>
