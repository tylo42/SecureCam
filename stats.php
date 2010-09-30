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
require_once('calendar.php');
require_once('page.php');

class stats_page extends page {
   protected function page_name() {
      echo "Stats";
   }

   public function body() {

      echo "<u><h1 align=\"center\">Stats</h1></u>";

      $sql = "select count(vid_id) from video";
      $result = mysql_query($sql);
      $num = mysql_fetch_array($result,MYSQL_ASSOC);
      $total=$num['count(vid_id)'];

      echo "<br><u><h2>All Time</h2></u>";

      echo "<table border=\"1\"><tr><th>Camera</th><th># of videos</td></th><tr>";
      for($count=1;$count<=$this->number_of_cameras();$count++){
         $sql = "select count(vid_id) from video where camera_id=$count";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $camcount=$num['count(vid_id)'];
         echo "<td>Camera $count</td><td>$camcount</td></tr>";
      }
      echo "<td>Total</td><td>$total</td></tr>";
      echo "</table>";

      //get the current date
      $date = getDate();
      $curmonth = $date["mon"];
      $curyear = $date["year"];
      $curmonnum=12*$curyear+$curmonth;

      //get first recorded date
      $sql = "select time from video where vid_id=1";
      $result = mysql_query($sql);
      $firdate = mysql_fetch_array($result,MYSQL_ASSOC);

      $first_time = getdate($firdate['time']);
      $firmonth = $first_time['mon'];
      $firyear  = $first_time['year'];
      $firmonnum=12*$firyear+$firmonth;

      //set a lower limit to prevent infinite loop
      $limmonnum=12*2008+6;

      // display each month stats
      for($countmon=$curmonnum;$countmon>=$firmonnum&&$countmon>$limmonnum;$countmon--){
         $year=floor($countmon/12);
         $month=$countmon%12;
         if($month==0){
            $month=12;
            $year--;
         }
         $monthname=mon_name($month);
         echo "<br><u><h2>$monthname - $year</h2></u>";

         $start_time = mktime(0, 0, 0, $month, 1, $year);
         $end_time   = mktime(0, 0, 0, $month + 1, 1, $year);
         $sql = "select count(vid_id) from video where $start_time <= time and time < $end_time";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $total=$num['count(vid_id)'];

         echo "<table border=\"1\"><tr><th>Camera</th><th># of videos</td></th><tr>";
         for($count=1;$count<=numcamera()&&$count<9;$count++){
            $sql = "select count(vid_id) from video where camera_id=$count and $start_time <= time and time < $end_time";
            $result = mysql_query($sql);
            $num = mysql_fetch_array($result,MYSQL_ASSOC);
            $camcount=$num['count(vid_id)'];
            echo "<td>Camera $count</td><td>$camcount</td></tr>";
         }
         echo "<td>Total</td><td>$total</td></tr>";
         echo "</table>";
      }
   }
}
?>
