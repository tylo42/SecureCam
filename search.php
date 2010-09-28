<?php
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

//	Small Bug: If you check boxes and then click the today buttons without first clicking search the check boxes will be removed
//	NOTES: at some point add in a count to tell if there is no videos in specified time

require_once('connect.php');
require_once('functions.php');
require_once('searchfunc.php');
require_once('display.php');

// Add remove flag
if($_GET['flag']==1)
	flag($_GET['idvid'], $_GET['idpic']);
if($_GET['flag']==0)
	deflag($_GET['idvid'], $_GET['idpic']);

$smonth=$_GET['smonth'];
$sday=$_GET['sday'];
$syear=$_GET['syear'];
$shour=$_GET['shour'];
$smin=$_GET['smin'];
$sampm=$_GET['sampm'];

$emonth=$_GET['emonth'];
$eday=$_GET['eday'];
$eyear=$_GET['eyear'];
$ehour=$_GET['ehour'];
$emin=$_GET['emin'];
$eampm=$_GET['eampm'];

$checkarray= array (1 => $_GET['camera1'], 2 => $_GET['camera2'], 3 => $_GET['camera3'], 4 => $_GET['camera4'], 5 => $_GET['camera5'], 6 => $_GET['camera6'], 7 => $_GET['camera7'], 8 => $_GET['camera8'], 9 => $_GET['camera9']);

for($x=1;$x<=numcamera();$x++) {
	$checkboxes.="&camera$x=$checkarray[$x]";
}

$date = getDate();
$curday = $date["mday"];
$curmonth = $date["mon"];
$curyear = $date["year"];

echo "<u><h1 align=\"center\">Search</h1></u>";

echo "<table><tr><td>";

//Today Button start
echo "<form action=\"search.php?smonth=$curmonth&sday=$curday&syear=$curyear&shour=$shour&emonth=$emonth&eday=$eday&eyear=$eyear&ehour=$ehour$checkboxes";
for($x=1;$x<=numcamera();$x++)
	echo "&camera$x=$checkarray[$x]";
echo "#$idvid\" method=\"post\">";
echo "<input type='submit' value='Set Starting Date To Today'>";
echo "</form>";

echo "</td><td>";

//Today Button end
echo "<form action=\"search.php?smonth=$smonth&sday=$sday&syear=$syear&shour=$shour&emonth=$curmonth&eday=$curday&eyear=$curyear&ehour=$ehour$checkboxes";
for($x=1;$x<=numcamera();$x++) {
	echo "&camera$x=$checkarray[$x]";
}
echo "#$idvid\" method=\"post\">";
echo "<input type='submit' value='Set Ending Date To Today'>";
echo "</form>";

echo "</td></tr><table>";

echo "<form action=\"search.php\" method=\"get\">";
startdate();
echo "<br>";
enddate();
echo "<br>";

// ------- Check boxes for cameras ------------	
for($count=1;$count<=numcamera();$count++){
	if($count==5)
		echo "<br>";
		echo "Camera $count:";
		$checked = ($checkarray[$count]==1) ? "checked" : "";
		echo "<input type='checkbox' name='camera$count' value='1' $checked>";
}
echo "<br>";
echo "<input type='submit' value='Search'>";
echo "</form><br>";

if(isset($_GET['shour'])){  // checks for first search
	// check for a specified camera
	for($count=1;$count<=numcamera();$count++){
		if($checkarray[$count]==1) {
			break;
		}
	}

	$begin_time = mktime($shour + $sampm, $smin, 0, $smonth, $sday, $syear);
	$end_time   = mktime($ehour + $eampm, $emin, 0, $emonth, $eday, $eyear);
	if($begin_time>=$end_time){
		echo "Invaid starting and ending time.<br />";
		break;
	}
	
	// generate sql 
	$sql = "select * from video where $begin_time <= time and time < $end_time and (";
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

	$action="search.php?flag=$nflag&idvid=$idvid&idpic=$idpic&smonth=$smonth&sday=$sday&syear=$syear&shour=$shour&smin=$smin&sampm=$sampm&emonth=$emonth&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&eampm=$eampm";
	for($x=1;$x<=numcamera()&&$x<9;$x++) {
		$action.="&camera$x=$checkarray[$x]";
	}
			
	display($sql,$action);
} 
?>
