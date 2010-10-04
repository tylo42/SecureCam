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

function search_date($name, $time, $prefix){
   global $monthArray, $dayArray, $yearArray, $hourArray, $minArray, $ampmArray;
   echo "<tr><td>";
   echo $name." Date:";
   echo "</td><td>";

   $selected = (isset($time['month']) && intval($time['month']) < 13) ? $time['month'] : '';
   echo "<select name=\"".$prefix."month\">";
   echo createOptionFromArray($monthArray,$selected);
   echo "</select>";

   $selected = (isset($time['day']) && intval($time['day']) < 32) ? $time['day'] : '';
   echo "<select name=\"".$prefix."day\">";
   echo createOptionFromArray($dayArray,$selected);
   echo "</select>";

   $selected = (isset($time['year']) && intval($time['year']) < 3000) ? $time['year'] : '';
   echo "<select name=\"".$prefix."year\">";
   echo createOptionFromArray($yearArray,$selected);
   echo "</select>";

   echo "</td><td>at</td><td>";

   $selected = (isset($time['hour']) && intval($time['hour']) < 24) ? $time['hour'] : '';
   echo "<select name=\"".$prefix."hour\">";
   echo createOptionFromArray($hourArray,$selected);
   echo "</select>";

   echo ":";

   $selected = (isset($time['min']) && intval($time['min']) < 60) ? $time['min'] : '';
   echo "<select name=\"".$prefix."min\">";
   echo createOptionFromArray($minArray,$selected);
   echo "</select>";

   $selected = (isset($time['ampm']) && intval($time['ampm']) < 24) ? $time['ampm'] : '';
   echo "<select name=\"".$prefix."ampm\">";
   echo createOptionFromArray($ampmArray,$selected);
   echo "</select>";

   echo "</td></tr>";
}
