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

require_once('database.php');
require_once('camera.php');

class page {
   public function body() { echo $this->display; }

   // DATA
   private $page_name;
   private $display;

   public function __construct($display, $page_name) {
      $this->display   = $display;
      $this->page_name = $page_name;
   }

   public function title() {
      echo "SecureCam - Camera Security System - ".$this->page_name;
   }

   protected function display($begin_time, $end_time, $cameras, $action, $flagged=0) {
      if(isset($_POST['flag'])) {
         $this->database->add_remove_flag($_POST['vid_id'], $_POST['flagged']);
      } else if(isset($_POST['remove'])) {
         $this->database->remove_video($_POST['vid_id']);
      }

      $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
      $result = $this->database->search_videos($begin_time, $end_time, $cameras, $page_num, $flagged);
      if(empty($result)) {
         return;
      }

      echo "<table class=\"display\">";
      while($video = mysql_fetch_array($result,MYSQL_ASSOC)) {
         if(!isset($video['picture_name'])) {
            $video['picture_name'] = "img/nopic.gif"; // FIXME: should have image here
         }

         $video['picture_name'] = $this->get_path($video['picture_name']);
         $video['video_name']   = $this->get_path($video['video_name']);

         $date_time = date("F j, Y - h:i:s A", $video['time']);

         $button="Remove Flag";
         if($video['flagged']==0) {
            $button="Flag";
         }

         echo "<tr><td>";

         echo "<a href=\"".$video['video_name']."\"><img class='preview' src=\"".$video['picture_name']."\"></img></a><br />";
         echo "<a href=\"".$video['picture_name']."\">Enlarge Picture</a></p>";

         echo "</td><td>";
         
         echo "<a href=\"".$video['video_name']."\">".$date_time."</a><br />";
         echo "<p>Camera ".$video['camera_id']." (".$this->get_description($video['camera_id']).")</p>";

         // The page to link to when flagging to keep all the info the same            
         echo "<form action=\"$action\" method=\"post\">";
         echo "<input type='hidden' name='vid_id' value=".$video['vid_id'].">";
         echo "<input type='hidden' name='flagged' value=".$video['flagged'].">";
         echo "<input type='submit' name='flag' value='$button'>&nbsp;";
         echo "<input type='submit' name='remove' value='Remove'>";
         echo "</form>";

         echo "</td></tr>";
      }
      echo "</table>";

      $this->print_page_nums($begin_time, $end_time, $cameras, $action, $flagged);
   }

   protected function first_year() {
      static $first_year = 0;
      if($first_year < 1) {
         $first_year = date("Y",$this->get_time("min"));
      }
      return $first_year;
   }

   protected function last_year() {
      static $last_year = 0;
      if($last_year < 1) {
         $last_year = date("Y",$this->get_time("max"));
      }
      return $last_year;
   }

   protected function put_camera_check_boxes() {
      $this->camera_check();
      
      $cameras = array();
      for($count=1; $count<=$this->number_of_cameras(); $count++){
         $checked = "";
         if($_SESSION['camera'.$count]==1) {
            $checked = "checked";
            $cameras[$count] = $count;
         }
         echo "<input type='checkbox' name='camera$count' value='1' $checked>";
         echo "Camera $count (".$this->get_description($count).")";
         echo "<br />";
      }
      return $cameras;
   }

   // HELPER FUNCTIONS
   private function print_page_nums($begin_time, $end_time, $cameras, $action, $flagged=0) {
      if(!isset($_GET['page_num'])) {
         $_GET['page_num'] = 1;
      }
      if(!is_numeric($_GET['page_num'])) {
         $_GET['page_num'] = 1;
      }

      $count = $this->database->number_of_videos($begin_time, $end_time, $cameras, $flagged);

      for($i=1; $i<($count/20) + 1; $i++) {
         echo "<a href=$action&page_num=$i>$i</a>&nbsp&nbsp&nbsp";
      }
   }

   private function get_time($minmax) {
      $sql = "select $minmax(time) from video";
      $result = mysql_query($sql);
      $time = mysql_fetch_array($result, MYSQL_ASSOC);
      return $time[$minmax.'(time)'];
   }
   
   /// If no cookies no cameras check, else set/keep cameras checked from last submit
   private function camera_check() {
      for($i=1; $i<=$this->number_of_cameras(); $i++) {
         $camera = 'camera'.$i;
         if(isset($_POST['submit'])) {
            $_SESSION[$camera] = (isset($_POST[$camera])) ? $_POST[$camera] : 0;
         } else {
            $_SESSION[$camera] = (isset($_SESSION[$camera])) ? $_SESSION[$camera] : 0;
         }
      }
   }
}
?>
