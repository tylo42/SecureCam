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

$monthArray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$dayArray = array (1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25, 26 => 26, 27 => 27, 28 => 28, 29 => 29, 30 => 30, 31 => 31);
$yearArray = array (2010 => 2010, 2011 => 2011, 2012 => 2012, 2013 => 2013, 2014 => 2014);
$hourArray = array (0 => '12', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11');
$minArray = array (0=> '00', 15 => '15', 30 => '30', 45 => '45');
$ampmArray = array (0 => 'am', 12 => 'pm');

function createOptionFromArray($myArray,$selected) {
	if(!is_array($myArray)) {
		return false;
	}
	$returned = $select = '';
	foreach($myArray as $key => $value) {
		if($selected == $key) {
			$select = ' selected';
		}
		$returned .= "<option value=\"$key\"$select>$value</option>";
		$select = '';
	}
	return $returned;
}

function startdate(){
	global $monthArray, $dayArray, $yearArray, $hourArray, $minArray, $ampmArray;
	echo "Starting Date:";

	$selected = (isset($_GET['smonth']) && intval($_GET['smonth']) < 13) ? $_GET['smonth'] : '';
	echo "<select name=\"smonth\">";
	echo createOptionFromArray($monthArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['sday']) && intval($_GET['sday']) < 32) ? $_GET['sday'] : '';
	echo "<select name=\"sday\">";
	echo createOptionFromArray($dayArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['syear']) && intval($_GET['syear']) < 3000) ? $_GET['syear'] : '';
	echo "<select name=\"syear\">";
	echo createOptionFromArray($yearArray,$selected);
	echo "</select>";

	echo "at";

	$selected = (isset($_GET['shour']) && intval($_GET['shour']) < 24) ? $_GET['shour'] : '';
	echo "<select name=\"shour\">";
	echo createOptionFromArray($hourArray,$selected);
	echo "</select>";

	echo ":";

	$selected = (isset($_GET['smin']) && intval($_GET['smin']) < 60) ? $_GET['smin'] : '';
	echo "<select name=\"smin\">";
	echo createOptionFromArray($minArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['sampm']) && intval($_GET['sampm']) < 24) ? $_GET['sampm'] : '';
	echo "<select name=\"sampm\">";
	echo createOptionFromArray($ampmArray,$selected);
	echo "</select>";
}

function enddate(){
	global $monthArray, $dayArray, $yearArray, $hourArray, $minArray, $ampmArray;
	echo "Ending Date:";

	$selected = (isset($_GET['emonth']) && intval($_GET['emonth']) < 13) ? $_GET['emonth'] : '';
	echo "<select name=\"emonth\">";
	echo createOptionFromArray($monthArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['eday']) && intval($_GET['eday']) < 32) ? $_GET['eday'] : '';
	echo "<select name=\"eday\">";
	echo createOptionFromArray($dayArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['eyear']) && intval($_GET['eyear']) < 3000) ? $_GET['eyear'] : '';
	echo "<select name=\"eyear\">";
	echo createOptionFromArray($yearArray,$selected);
	echo "</select>";

	echo "at";

	$selected = (isset($_GET['ehour']) && intval($_GET['ehour']) < 24) ? $_GET['ehour'] : '';
	echo "<select name=\"ehour\">";
	echo createOptionFromArray($hourArray,$selected);
	echo "</select>";

	echo ":";

	$selected = (isset($_GET['emin']) && intval($_GET['emin']) < 60) ? $_GET['emin'] : '';
	echo "<select name=\"emin\">";
	echo createOptionFromArray($minArray,$selected);
	echo "</select>";

	$selected = (isset($_GET['eampm']) && intval($_GET['eampm']) < 24) ? $_GET['eampm'] : '';
	echo "<select name=\"eampm\">";
	echo createOptionFromArray($ampmArray,$selected);
	echo "</select>";
}
?>
