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
 
 abstract class input {
   abstract function __toString();
 } // end class input
 
class search_input {
   private $first_year;
   private $last_year;
   private $begin_time;
   private $end_time;
   
   public function __construct($first_year, $last_year, $begin_time=NULL, $end_time=NULL) {
      $this->first_year = $first_year;
      $this->last_year  = $last_year;
      if(isset($begin_time) && isset($end_time)) {
         $this->begin_time = $begin_time;
         $this->end_time = $end_time;
      } else {
         $this->begin_time = mktime(0, 0, 0);
         $this->end_time   = mktime(0, 0, 0) + 60*60*24;
      }
      assert($this->first_year < $this->last_year);
      assert($this->begin_time < $this->end_time);
   }
    
   public function __toString() {
      echo "<form action=\"index.php?page=search\" method=\"post\">";
      echo "<table>";
      $this->search_date("Starting",$this->begin_time, "s");
      $this->search_date("Ending",  $this->end_time,   "e");
      echo "</table>";

      $cameras = $this->put_camera_check_boxes();
      echo "<input type='submit' value='Search' name='submit'>";
      echo "</form><br />";
   }
   
   private function search_date($name, $unix_time, $prefix) {
      $monthArray = array();
      for($i=1; $i<=12; $i++) {
         $monthArray[$i] = date("F", mktime(0, 0, 0, $i, 1, 2010));
      }

      $dayArray = array();
      for($i=1; $i<=31; $i++) {
         $dayArray[$i] = $i;
      }

      $yearArray = array();
      for($i=$this->first_year; $i<=$this->last_year; $i++) {
         $yearArray[$i] = $i;
      }

      $hourArray = array();
      $hourArray[0] = 12;
      for($i=1; $i<12; $i++) {
         $hourArray[$i] = $i;
      }

      $minArray = array(0 => '00', 15 => '15', 30 => '30', 45 => '45');
      $ampmArray = array(0 => 'am', 12 => 'pm');

      echo "<tr><td>";
      echo $name." Date:";
      echo "</td><td>";

      $month = date("n", $unix_time);
      echo "<select name=\"".$prefix."month\">";
      echo $this->createOptionFromArray($monthArray, $month);
      echo "</select>";

      $day   = date("j", $unix_time);
      echo "<select name=\"".$prefix."day\">";
      echo $this->createOptionFromArray($dayArray, $day);
      echo "</select>";

      $year  = date("Y", $unix_time);
      echo "<select name=\"".$prefix."year\">";
      echo $this->createOptionFromArray($yearArray, $year);
      echo "</select>";

      echo "</td><td>at</td><td>";

      $hour  = date("g", $unix_time);
      echo "<select name=\"".$prefix."hour\">";
      echo $this->createOptionFromArray($hourArray, $hour);
      echo "</select>";

      echo ":";

      $min   = date("i", $unix_time);
      echo "<select name=\"".$prefix."min\">";
      echo $this->createOptionFromArray($minArray, $min);
      echo "</select>";

      $ampm  = date("A", $unix_time) == "AM" ? 0 : 1;
      echo "<select name=\"".$prefix."ampm\">";
      echo $this->createOptionFromArray($ampmArray,$ampm);
      echo "</select>";

      echo "</td></tr>";
   }
   
   private function createOptionFromArray($myArray,$selected) {
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
} // end class search_input
?>
