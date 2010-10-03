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

// Small Bug: If you check boxes and then click the today buttons without first clicking search the check boxes will be removed
// NOTES: at some point add in a count to tell if there is no videos in specified time

require_once('connect.php');
require_once('searchfunc.php');
require_once('page.php');

class search_page extends page {
   protected function page_name() {
      echo "Search";
   }

   public function body() {

      $this->camera_check();

      if(isset($_POST['submit'])) {
         $smonth = $_POST['smonth'];
         $sday   = $_POST['sday'];
         $syear  = $_POST['syear'];
         $shour  = $_POST['shour'];
         $smin   = $_POST['smin'];
         $sampm  = $_POST['sampm'];

         $emonth = $_POST['emonth'];
         $eday   = $_POST['eday'];
         $eyear  = $_POST['eyear'];
         $ehour  = $_POST['ehour'];
         $emin   = $_POST['emin'];
         $eampm  = $_POST['eampm'];

      }
      $date = getDate();
      $curday = $date["mday"];
      $curmonth = $date["mon"];
      $curyear = $date["year"];

      echo "<h2>Search</h2>";

      echo "<form action=\"index.php?page=search\" method=\"post\">";
      echo "<table>";
      search_date("Starting", "s");
      search_date("Ending", "e");
      echo "</table>";

      // ------- Check boxes for cameras ------------ 
      for($count=1;$count<=$this->number_of_cameras();$count++){
         if($count==5) {
            echo "<br />";
         }
         echo "Camera $count:";
         $checked = (isset($_SESSION['camera'.$count]) && $_SESSION['camera'.$count]==1) ? "checked" : "";
         echo "<input type='checkbox' name='camera$count' value='1' $checked>";
      }
      echo "<br />";
      echo "<input type='submit' value='Search' name='submit'>";
      echo "</form><br />";

      if(isset($_POST['submit'])) {  // checks for first search
         // check for a specified camera

         $begin_time = mktime($shour + $sampm, $smin, 0, $smonth, $sday, $syear);
         $end_time   = mktime($ehour + $eampm, $emin, 0, $emonth, $eday, $eyear);
         if($begin_time>=$end_time){
            echo "<p>Invaid starting and ending time.</p>";
         } else {

            // generate sql 
            $sql = "select * from video where $begin_time <= time and time < $end_time and (";
            $first=0;
            for($camnum=1; $camnum<$this->number_of_cameras(); $camnum++) {
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
               $action = "index.php?page=search";
               $this->display($sql, $action);
            }
         }
      } 
   }
}
?>
