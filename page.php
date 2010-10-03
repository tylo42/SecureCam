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

abstract class page {
   abstract protected function page_name();
   abstract public function body();

   public function title() {
      echo "SecureCam - Camera Security System - ";
      $this->page_name();
   }

   public function display($sql,$action){
      echo "<table class=\"display\">";

      $result = mysql_query($sql);

      // populate the array
      $counter = 1;
      while($video = mysql_fetch_array($result,MYSQL_ASSOC)) {
         if(!isset($video['picture_name'])) {
            $video['picture_name'] = "img/nopic.gif";
         }

         $video['picture_name'] = $this->get_path($video['picture_name']);
         $video['video_name'] = $this->get_path($video['video_name']);

         $date_time = date("F j, Y - h:i:s A",$video['time']);

         $button="Remove Flag";
         if($video['flagged']==0){
            $button="Flag";
         }

         if($counter==1) {
            echo "<tr>";
         } else if(!(is_int($counter/2))) {
            echo "</tr><tr>";
         }
         echo "<td><p id=\"".$video['vid_id']."\">".$date_time."<br />";
         echo "<a href=\"".$video['picture_name']."\">Enlarge Picture</a></p>";
         echo "<a href=\"".$video['video_name']."\"><img class='preview' src=\"".$video['picture_name']."\"></img></a>";

         // The page to link to when flagging to keep all the info the same            
         echo "<form action=\"$action#".$video['vid_id']."\" method=\"post\">";
         echo "<input type='submit' name='".$video['vid_id']."' value='$button'>&nbsp;";
         echo "<input type='submit' value='Remove'>";
         echo "</form>";

         echo "</td>";

         $counter++;
      }
      if(is_int($counter/2))
         echo "<td width=\"320px\">&nbsp</td>";
      echo "</tr></table>";
   }

   /// return the number of cameras
   public function number_of_cameras() {
      static $cameras = 0;
      if($cameras < 1) {
         $sql = "select count(distinct camera_id) from camera";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $cameras = $num['count(distinct camera_id)'];
      }
      return $cameras;
   }

   /// If no cookies no cameras check, else set/keep cameras checked from last submit
   public function camera_check() {
      for($i=1; $i<=$this->number_of_cameras(); $i++) {
         $camera = 'camera'.$i;
         if(isset($_POST['submit'])) {
            $_SESSION[$camera] = (isset($_POST[$camera])) ? $_POST[$camera] : 0;
         } else {
            $_SESSION[$camera] = (isset($_SESSION[$camera])) ? $_SESSION[$camera] : 0;
         }
      }
   }

   public function add_flag($vid_id) {
      $this->flag_sql($vid_id, 1);
   }

   public function remove_flag($vid_id) {
      $this->flag_sql($vid_id, 0);
   }

   public function get_path($input) {
      return strstr($input, "snapshots");
   }

   public function get_description($camera_id) {
      $sql = "select description from camera where camera_id=$camera_id";
      $result = mysql_query($sql);
      $info = mysql_fetch_array($result, MYSQL_ASSOC);

      return $info['description'];
   }

   // HELPER FUNCTIONS
   private function flag_sql($vid_id, $flag) {
      $sql = "update video set flagged=$flag where vid_id=$vid_id";
      mysql_query($sql);
   }
}
?>
