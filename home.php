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
require_once('page.php');

class home_page extends page {
   protected function page_name() {
      // intentionally left blank
   }

   public function body() {


      echo "<h2>Most Recent Videos</h2>";

      echo "<table id=\"recent-videos\">";

      // this can be done smarter with just one query!!!
      for($camnum=1;$camnum<=$this->number_of_cameras();$camnum++){

         if(!(is_int($camnum/2))){
            echo "<tr><td><h3>Camera $camnum</h3>";
            echo "<p>".getdescription($camnum)."</p>";
            echo "</td>";
            $ncamnum=$camnum+1;
            if($camnum<$this->number_of_cameras()){
               echo "<td><h3>Camera $ncamnum</h3>";
               echo "<p>".getdescription($ncamnum)."</p>";
               echo "</td>";
            }else
               echo "<td>&nbsp;</td>";
            echo "</tr><tr>";
         }

         echo "<td>";

         // find the max vid_id for the camera
         $sql = "select max(vid_id) from video where camera_id=$camnum and picture_name!=''";
         $result = mysql_query($sql);
         $vid = mysql_fetch_array($result,MYSQL_ASSOC);
         $max=$vid['max(vid_id)'];

         $sql = "select * from video where vid_id=$max";
         $result = mysql_query($sql);
         $vid = mysql_fetch_array($result,MYSQL_ASSOC);

         $subpic=rmvarwww($vid['picture_name']);
         $subvid=rmvarwww($vid['video_name']);

         if($subpic == "" || $subvid == "") {
            $subpic="img/error.gif";
            $subvid="";
         }

         echo "<a href=$subvid><img class=\"preview\" src=$subpic></img></a>  ";
         echo "</td>";
      }
      echo "</tr></table>";

      //  ----- Print how many videos have been taken today ---------
      $date = getDate();

      $day = $date["mday"];
      $month = $date["mon"];
      $year = $date["year"];

      $begin_day = mktime(0, 0, 0, $month, $day, $year);
      $end_day = mktime(0, 0, 0, $month, $day+1, $year);

      $sql = "select count(vid_id) from video where $begin_day <= time and time < $end_day";
      $result = mysql_query($sql);
      $num = mysql_fetch_array($result,MYSQL_ASSOC);

      $number=$num['count(vid_id)'];

      if(!isset($number)) {
         $number = 0;
      }

      if($number==1) {
         echo "There has been one video today.";
      } else {
         echo "There have been $number videos today.";
      }
   }
}
?>
