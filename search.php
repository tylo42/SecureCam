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

// NOTES: at some point add in a count to tell if there is no videos in specified time

require_once('connect.php');
require_once('page.php');

class search_page extends page {
   protected function page_name() {
      echo "Search";
   }

   public function body() {
      $start_time = array();
      $end_time = array();
      if(isset($_POST['submit'])) {
         $start_time['month'] = $_POST['smonth'];
         $start_time['day']   = $_POST['sday'];
         $start_time['year']  = $_POST['syear'];
         $start_time['hour']  = $_POST['shour'];
         $start_time['min']   = $_POST['smin'];
         $start_time['ampm']  = $_POST['sampm'];

         $end_time['month']   = $_POST['emonth'];
         $end_time['day']     = $_POST['eday'];
         $end_time['year']    = $_POST['eyear'];
         $end_time['hour']    = $_POST['ehour'];
         $end_time['min']     = $_POST['emin'];
         $end_time['ampm']    = $_POST['eampm'];

      } else {
         $date = getDate();
         $start_time['month'] = $date['mon'];
         $start_time['day']   = $date['mday'];
         $start_time['year']  = $date['year'];
         $start_time['hour']  = 0;
         $start_time['min']   = 0;
         $start_time['ampm']  = 0;

         $date = getDate(time() + 60*60*24);
         $end_time['month']   = $date['mon'];
         $end_time['day']     = $date['mday'];
         $end_time['year']    = $date['year'];
         $end_time['hour']    = 0;
         $end_time['min']     = 0;
         $end_time['ampm']    = 0;
      }
      $date = getDate();
      $curday = $date["mday"];
      $curmonth = $date["mon"];
      $curyear = $date["year"];

      echo "<h2>Search</h2>";

      echo "<form action=\"index.php?page=search\" method=\"post\">";
      echo "<table>";
      $this->search_date("Starting",$start_time, "s");
      $this->search_date("Ending",  $end_time,   "e");
      echo "</table>";

      $cameras = $this->put_camera_check_boxes();
      echo "<input type='submit' value='Search' name='submit'>";
      echo "</form><br />";


      $begin_time = mktime($start_time['hour'] + $start_time['ampm'], $start_time['min'], 0, $start_time['month'], $start_time['day'], $start_time['year']);
      $end_time   = mktime($end_time['hour']   + $end_time['ampm'],   $end_time['min'],   0, $end_time['month'],   $end_time['day'],   $end_time['year']);

      $action = "index.php?page=search";
      $this->display($begin_time, $end_time, $cameras, $action);
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

   private function search_date($name, $time, $prefix) {
      $monthArray = array();
      for($i=1; $i<=12; $i++) {
         $monthArray[$i] = date("F", mktime(0, 0, 0, $i, 1, 2010));
      }

      $dayArray = array();
      for($i=1; $i<=31; $i++) {
         $dayArray[$i] = $i;
      }

      $yearArray = array();
      for($i=$this->first_year(); $i<=$this->last_year(); $i++) {
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

      $selected = (isset($time['month']) && intval($time['month']) < 13) ? $time['month'] : '';
      echo "<select name=\"".$prefix."month\">";
      echo $this->createOptionFromArray($monthArray,$selected);
      echo "</select>";

      $selected = (isset($time['day']) && intval($time['day']) < 32) ? $time['day'] : '';
      echo "<select name=\"".$prefix."day\">";
      echo $this->createOptionFromArray($dayArray,$selected);
      echo "</select>";

      $selected = (isset($time['year']) && intval($time['year']) < 3000) ? $time['year'] : '';
      echo "<select name=\"".$prefix."year\">";
      echo $this->createOptionFromArray($yearArray,$selected);
      echo "</select>";

      echo "</td><td>at</td><td>";

      $selected = (isset($time['hour']) && intval($time['hour']) < 24) ? $time['hour'] : '';
      echo "<select name=\"".$prefix."hour\">";
      echo $this->createOptionFromArray($hourArray,$selected);
      echo "</select>";

      echo ":";

      $selected = (isset($time['min']) && intval($time['min']) < 60) ? $time['min'] : '';
      echo "<select name=\"".$prefix."min\">";
      echo $this->createOptionFromArray($minArray,$selected);
      echo "</select>";

      $selected = (isset($time['ampm']) && intval($time['ampm']) < 24) ? $time['ampm'] : '';
      echo "<select name=\"".$prefix."ampm\">";
      echo $this->createOptionFromArray($ampmArray,$selected);
      echo "</select>";

      echo "</td></tr>";
   }
}
?>
