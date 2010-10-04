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

         $date = getDate(time() + 60*60*24);
         $end_time['month']   = $date['mon'];
         $end_time['day']     = $date['mday'];
         $end_time['year']    = $date['year'];
      }
      $date = getDate();
      $curday = $date["mday"];
      $curmonth = $date["mon"];
      $curyear = $date["year"];

      echo "<h2>Search</h2>";

      echo "<form action=\"index.php?page=search\" method=\"post\">";
      echo "<table>";
      search_date("Starting",$start_time, "s");
      search_date("Ending",  $end_time,   "e");
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

         $begin_time = mktime($start_time['hour'] + $start_time['ampm'], $start_time['min'], 0, $start_time['month'], $start_time['day'], $start_time['year']);
         $end_time   = mktime($end_time['hour']   + $end_time['ampm'],   $end_time['min'],   0, $end_time['month'],   $end_time['day'],   $end_time['year']);
         if($begin_time>=$end_time){
            echo "<p>Invaid starting and ending time.</p>";
         } else {

            // generate sql 
            $sql = "select * from video where $begin_time <= time and time < $end_time and (";
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
               $action = "index.php?page=search";
               $this->display($sql, $action);
            }
         }
      } 
   }
}
?>
