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
   
   private $cameras;
   
   public function __construct($cameras) {
      $this->cameras = $cameras;
   }
   
   protected function put_camera_check_boxes() {
      $string = "";
      foreach($this->cameras as $camera) {
         $checked = $camera->get_checked() ? "checked" : "";
         $string .= "<input type='checkbox' name='camera".$camera->get_id()."' value='1' $checked>";
         $string .= "Camera ".$camera->get_id()." (".$camera->get_description().")";
         $string .= "<br />";
      }
      return $string;
   }
 } // end class input
 
class search_input extends input {
   private $first_year;
   private $last_year;
   private $begin_time;
   private $end_time;
   
   public function __construct($cameras, $first_year, $last_year, $begin_time, $end_time) {
      parent::__construct($cameras);
      
      $this->first_year = $first_year;
      $this->last_year  = $last_year;
      $this->begin_time = $begin_time;
      $this->end_time = $end_time;
      
      assert($this->first_year < $this->last_year);
   }
   
   public function get_begin_time() { return $this->begin_time; }
   public function get_end_time()   { return $this->end_time; }
    
   public function __toString() {
      $string = "";
      $string .= "<form action=\"index.php?page=search\" method=\"post\">";
      $string .= "<table>";
      $string .= $this->search_date("Starting",$this->begin_time, "s");
      $string .= $this->search_date("Ending",  $this->end_time,   "e");
      $string .= "</table>";

      $string .= $this->put_camera_check_boxes();
      $string .= "<input type='submit' value='Search' name='submit'>";
      $string .= "</form><br />";
      return $string;
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

      $string = "";
      $string .= "<tr><td>";
      $string .= $name." Date:";
      $string .= "</td><td>";

      $month = date("n", $unix_time);
      $string .= "<select name=\"".$prefix."month\">";
      $string .= $this->createOptionFromArray($monthArray, $month);
      $string .= "</select>";

      $day   = date("j", $unix_time);
      $string .= "<select name=\"".$prefix."day\">";
      $string .= $this->createOptionFromArray($dayArray, $day);
      $string .= "</select>";

      $year  = date("Y", $unix_time);
      $string .= "<select name=\"".$prefix."year\">";
      $string .= $this->createOptionFromArray($yearArray, $year);
      $string .= "</select>";

      $string .= "</td><td>at</td><td>";

      $hour  = date("g", $unix_time);
      $string .= "<select name=\"".$prefix."hour\">";
      $string .= $this->createOptionFromArray($hourArray, $hour);
      $string .= "</select>";

      $string .= ":";

      $min   = date("i", $unix_time);
      $string .= "<select name=\"".$prefix."min\">";
      $string .= $this->createOptionFromArray($minArray, $min);
      $string .= "</select>";

      $ampm  = date("A", $unix_time) == "AM" ? 0 : 12;
      $string .= "<select name=\"".$prefix."ampm\">";
      $string .= $this->createOptionFromArray($ampmArray,$ampm);
      $string .= "</select>";

      $string .= "</td></tr>";
      return $string;
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