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
require_once('page.php');

class browse_page extends page {
   protected function page_name() {
      echo "Browse";
   }

   public function body() {
      $this->camera_check();

      //check all for first time
      if(isset($_GET['mday'])) {
         $_SESSION['mday'] = $_GET['mday'];
         $_SESSION['mon']  = $_GET['mon'];
         $_SESSION['year'] = $_GET['year'];
      } else {
         $date=getDate();
         $_SESSION['mday'] = $date["mday"];
         $_SESSION['mon']  = $date["mon"];
         $_SESSION['year'] = $date["year"];
      }

      $action = "index.php?page=browse&mday=".$_SESSION['mday']."&mon=".$_SESSION['mon']."&year=".$_SESSION['year'];
      
      //title
      echo "<h2>Browse</h2>";

      echo "<table border=\"0\" width=\"100%\"><tr><td>";
      echo "<form action=\"$action\" method=\"post\">";

      $cameras = array();
      for($count=1; $count<=$this->number_of_cameras(); $count++){
         echo "Camera $count:";
         $checked = "";
         if($_SESSION['camera'.$count]==1) {
            $checked = "checked";
            $cameras[$count] = $count;
         }
         echo "<input type='checkbox' name='camera$count' value='1' $checked>";
         echo "<br />";
      }
      echo "<br />";
      echo "<input action='index?page=browse' name='submit' type='submit' value='Add/Remove Cameras'>";
      echo "</form>";

      echo "</td><td align=\"right\">";

      // Display the calandar
      $date["mday"] = $_SESSION['mday'];
      $date["mon"]  = $_SESSION['mon'];
      $date["year"] = $_SESSION['year'];

      echo calendar($date);
      echo "</td></table>";

      //Display the videos
      $day   = $_SESSION["mday"];
      $month = $_SESSION["mon"];
      $year  = $_SESSION["year"];	

      $begin_day = mktime(0, 0, 0, $month, $day, $year);
      $end_day = mktime(0, 0, 0, $month, $day+1, $year);

      $this->display($begin_day, $end_day, $cameras, $action);
   }
}
?>
