<?php
/* Copyright 2008, 2009, 2010, 2011 Tyler Hyndman
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

require_once('layout_utils.php');

abstract class input {
   abstract function __toString();

   private $cameras;

   public function __construct($cameras) {
      $this->cameras = $cameras;
   }

   protected function put_camera_check_boxes() {
      $string = "<p class='search-heading'>Select cameras</p>";
      $tds = array();
      foreach($this->cameras as $camera) {
         $checked = $camera->get_checked() ? "checked" : "";
         $td  = "<p>";
         $td .= "<input type='checkbox' name='camera".$camera->get_id()."' value='1' $checked />";
         $td .= "&nbsp;".$camera->get_description();
         $td .= "</p>";
         $tds[] = $td;
      }
      $string .= html_table(3, $tds, "search_cameras");
      return $string;
   }
} // end class input

class search_input extends input {
   private $begin_time;
   private $end_time;
   private $flagged;

   public function __construct($cameras, $begin_time, $end_time, $flagged) {
      parent::__construct($cameras);

      $this->begin_time = $begin_time;
      $this->end_time = $end_time;
      $this->flagged = $flagged;
   }

   public function get_begin_time() { return $this->begin_time; }
      public function get_end_time()   { return $this->end_time; }

      public function __toString() {
         $string  = "<h2>Search for videos&hellip;</h2>";
         $string .= "<hr />";

         $string .= "<form action=\"index.php?page=search\" method=\"post\">";

         $string .= "<table id='search'><tr>";

         $string .= "<td>\n\n";
         $string .= "<table class='search-time'>\n";
         $string .= $this->search_date("Starting",$this->begin_time, "s");
         $string .= $this->search_date("Ending",  $this->end_time,   "e");
         $string .= "</table>\n\n";
         $string .= "</td>";

         $string .= "<td>";
         $string .= $this->put_camera_check_boxes();
         $string .= "</td>";

         $string .= "<td>";
         $checked = $this->flagged ? "checked" : "";
         $string .= "<p><input type='checkbox' name='flag_check' value='1' $checked/>";
         $string .= "&nbsp;Flagged";
         $string .= "</td>";

         $string .= "</tr></table>";

         $string .= "<input type='submit' value='Search' name='submit'>";

         $string .= "</form>";
         return $string;
      }

   private function search_date($name, $unix_time, $prefix) {
      $string .= "<tr><th>$name Date</th>";
      $string .= "<td></td>";
      $string .= "<th>$name Time</th></tr>\n";

      $string .= "<tr><td>";

      $date = date("n/j/Y", $unix_time);
      $string .= "<input name='".$prefix."date' type='text' value='$date' maxlength='10' size='10' />"; // "DD/MM/YYYY"

      $string .= "</td><td>";
      $string .= "at";
      $string .= "</td><td>";

      $time = date("g:i A", $unix_time);
      $string .= "<input name='".$prefix."time' type='text' value='$time' maxlength='8' size='10' />"; // "HH:MM AA"

      $string .= "</td></tr>\n";
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
