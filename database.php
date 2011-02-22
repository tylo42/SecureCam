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

require_once('video.php');
require_once('camera.php');


/**
 * @class securecam_database
 *
 * Singleton class
 */
class securecam_database {
   private static $instance;

   // DATA
   private $conn;

   private function __construct() {
      $dbhost = 'localhost';
      $dbuser = 'root';
      $dbpass = 'root';
      $dbname = 'securecam';

      $this->conn = mysql_connect($dbhost, $dbuser, $dbpass) or die
         ('Error connecting to mysql');

      mysql_select_db($dbname);
   }

   function __destruct() {
      mysql_close($this->conn);
   }

   /// singleton method
   public static function singleton() {
      if(!isset(self::$instance)) {
         $c = __CLASS__;
         self::$instance = new $c;
      }
      return self::$instance;
   }

   // Prevent users to clone the instance
   public function __clone() {
      trigger_error('Clone is not allowed.', E_USER_ERROR);
   }

   /**
    * $start_time       Unix timestamp for a date
    * $end_time         Unix timestamp for a date
    * $cameras          Array of cameras in use
    * $page_num         The page to display
    * $flagged          If 1 only display flagged videos 
    */
   public function search_videos($start_time, $end_time, $page_num=1, $flagged=false) {
      if(!is_numeric($start_time) || !is_numeric($end_time)) {
         return array();
      }

      if(!is_numeric($page_num) || $page_num < 1) {
         $page_num = 1;
      }

      $sql = $this->generate_video_sql($start_time, $end_time, "*");
      if($flagged) {
         $sql .= "AND flagged=1 ";
      }
      $sql .= "ORDER BY time LIMIT 20 OFFSET ".(($page_num-1)*20);

      return $this->to_video_collection(mysql_query($sql));
   }

   public function number_of_cameras() {
      static $number_of_cameras = 0;
      if($number_of_cameras < 1) {
         $sql = "SELECT COUNT(camera_id) from camera";
         $result = mysql_query($sql);
         $count = mysql_fetch_array($result, MYSQL_ASSOC);
         $number_of_cameras = $count['COUNT(camera_id)'];
      }
      return $number_of_cameras;
   }

   public function number_of_videos($start_time, $end_time, $flagged=false) {
      if(!is_numeric($start_time) || !is_numeric($end_time) || $start_time>$end_time) {
         return 0; // possible throw error at some point
      }

      if(empty($cameras)) {
         for($i=0; $i<$this->number_of_cameras(); $i++) {
            $cameras[] = $i;
         }
      }

      $sql = $this->generate_video_sql($start_time, $end_time, "COUNT(vid_id)");
      if($flagged) {
         $sql .= " AND flagged=1";
      }
      $result = mysql_query($sql);
      $count = mysql_fetch_array($result,MYSQL_ASSOC);
      return $count['COUNT(vid_id)'];
   }

   public function get_max_videos() {
      $sql  = "SELECT vid.* FROM video vid ";
      $sql .= "INNER JOIN(SELECT camera_id, MAX(vid_id) As MaxVid FROM video ";
      $sql .= "WHERE picture_name != '' GROUP BY camera_id) grouped ";
      $sql .= "ON vid.camera_id = grouped.camera_id AND vid.vid_id = grouped.MaxVid";
      $result = mysql_query($sql);
      return $this->to_video_collection($result);
   }

   public function add_remove_flag($vid_id, $old_flag) {
      if(!is_numeric($vid_id) || !is_numeric($old_flag)) {
         return;
      }
      $flag = ($old_flag==0) ? 1 : 0;
      $sql = "update video set flagged=$flag where vid_id=$vid_id";
      mysql_query($sql);
   }

   public function remove_video($vid_id) {
      if(!is_numeric($vid_id)) {
         return;
      }
      $sql = "DELETE FROM video WHERE vid_id=$vid_id";
      mysql_query($sql);
   }

   public function get_cameras() {
      $sql = "SELECT * FROM camera ORDER BY camera_id";
      $mysql_cameras = mysql_query($sql);
      $cameras = array();
      while($mysql_camera = mysql_fetch_array($mysql_cameras,MYSQL_ASSOC)) {
         $cameras[$mysql_camera['camera_id']] = new camera($mysql_camera['camera_id'], $mysql_camera['hostname'], $mysql_camera['port'], $mysql_camera['description']);
      }
      return $cameras;
   }

   public function update_camera($camera_id, $desc, $host, $port) {
      assert(is_numeric($camera_id));
      $cameras = $this->get_cameras();
      $desc = mysql_real_escape_string($desc);
      $host = mysql_real_escape_string($host);
      $port = mysql_real_escape_string($port);
      if( is_numeric($port) &&
         ( $desc != $cameras[$camera_id]->get_description() ||
           $host != $cameras[$camera_id]->get_hostname()    || 
           $port != $cameras[$camera_id]->get_port() ) ) 
      {
         unset_cameras();
         $sql = "update camera set description=\"$desc\", hostname=\"$host\", port=$port where camera_id=$camera_id";
         $result = mysql_query($sql);
      }
   }
   
   public function get_time($minmax) {
      $minmax = mysql_real_escape_string($minmax);
      $sql = "select $minmax(time) from video";
      $result = mysql_query($sql);
      $time = mysql_fetch_array($result, MYSQL_ASSOC);
      return $time[$minmax.'(time)'];
   }
   
   public function get_stats() {
      $sql = "SELECT camera_id,DATE_FORMAT(FROM_UNIXTIME(time), \"%Y-%m\") AS MONTH,COUNT(vid_id) FROM video GROUP BY camera_id,MONTH DESC";
      $result = mysql_query($sql);
      
      $stats = array();
      while($stat = mysql_fetch_array($result,MYSQL_ASSOC)) {
         $stats[$stat['camera_id']."-".$stat['MONTH']] = $stat['COUNT(vid_id)'];
      }
      
      $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(time), \"%Y-%m\") AS MONTH,COUNT(vid_id) FROM video GROUP BY MONTH DESC";
      $result = mysql_query($sql);
      while($stat = mysql_fetch_array($result,MYSQL_ASSOC)) {
         $stats["total-".$stat['MONTH']] = $stat['COUNT(vid_id)'];
      }
      
      return $stats;
   }

   // HELPER FUNCTIONS
   private function to_video_collection($mysql_result) {
      $videos = array();
      while($video = mysql_fetch_array($mysql_result,MYSQL_ASSOC)) {
         $videos[] = new video($video['vid_id'], $video['time'], $video['video_name'], $video['picture_name'], $video['camera_id'], $video['flagged']);
      }
      return $videos;
   }

   private function generate_video_sql($start_time, $end_time, $what) {
      $sql = "SELECT $what FROM video WHERE $start_time < time AND time < $end_time AND (";

      $first = true;
      foreach(get_cameras() as $camera) {
         if($camera->get_checked()) {
            if(!$first) {
               $sql .= " OR ";
            }
            $sql .= "camera_id = ".$camera->get_id();
            if($first) $first = false;
         }
      }
      // if first is true then no videos were added
      if($first) {
         $sql .= "camera_id = NULL";
      }
      
      $sql .= ")";

      return $sql;
   }
}

?>
