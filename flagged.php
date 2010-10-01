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

require_once('connect.php');
require_once('functions.php');
require_once('display.php');
require_once('page.php');

class flagged_page extends page {
   protected function page_name() {
      echo "Flagged";
   }

   public function body() {

      echo "<h2>Flagged</h2>";

      $this->camera_check();

      echo "<form action=\"index.php?page=flagged\" method=\"post\">";

      for($i=1; $i<=$this->number_of_cameras(); $i++){
         echo "Camera $i:";
         $checked = ($_SESSION['camera'.$i] == 1) ? "checked" : "";
         echo "<input type='checkbox' name='camera$i' value='1' $checked>";
         echo "<br />";
      }

      echo "<br />";
      echo "<input type='submit' name='submit' value='Add/Remove Cameras'>";
      echo "</form>";

      // generate sql 
      $sql = "select * from video where flagged=1 and ( ";
      $first=0;
      for($camnum=1; $camnum<=$this->number_of_cameras(); $camnum++) {
         if($_SESSION['camera'.$camnum]==1) {
            if($first==0) {
               $first = 1;
               $sql .= "camera_id = $camnum";
            } else {
               $sql .= " or camera_id = $camnum";
            }
         }
      }

      $sql .= ") order by time";

      // if no cameras selected
      if($first == 0) {
         echo "<p>Please select a camera</p>";
      } else {
         $action = "flagged.php";
         display($sql, $action);
      }
   }
}
?>
