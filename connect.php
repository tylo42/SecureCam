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

// To be removed and replaced with securecam_database
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die 
	('Error connectiong to mysql');

$dbname = 'securecam';
mysql_select_db($dbname);



class securecam_database {
   function __construct() {
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

   /**
    * $start_time       Unix timestamp for a date
    * $end_time         Unix timestamp for a date
    * $cameras          Array of cameras in use
    * $page_num         The page to display
    * $flagged          If 1 only display flagged videos 
    */
   public function search_videos($start_time, $end_time, $cameras, $page_num=1, $flagged=0) {
      if(!is_numeric($start_time) || !is_numeric($end_time)) {
         return array();
      }

      if(empty($cameras)) {
         echo "<p>Please specify a camera.</p>";
         return array();
      }

      if($start_time>$end_time) {
         echo "<p>Invalid starting and ending time.</p>";
         return array();
      }

      if(!is_numeric($page_num) || $page_num < 1) {
         $page_num = 1;
      }

      $sql = $this->generate_video_sql($start_time, $end_time, $cameras, "*");
      if($flagged == 1) {
         $sql .= "and flagged=1 ";
      }
      $sql .= "ORDER BY time LIMIT 20 OFFSET ".(($page_num-1)*20);

      return mysql_query($sql);
   }

   public function number_of_videos($start_time, $end_time, $cameras, $flagged=0) {
      if(!is_numeric($start_time) || !is_numeric($end_time) || empty($cameras) || $start_time>$end_time) {
         return 0; // possible throw error at some point
      }

      $sql = $this->generate_video_sql($start_time, $end_time, $cameras, "COUNT(vid_id)");
      if($flagged == 1) {
         $sql .= " AND flagged=1";
      }
      $result = mysql_query($sql);
      $count = mysql_fetch_array($result,MYSQL_ASSOC);
      return $count['COUNT(vid_id)'];
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

   // HELPER FUNCTIONS
   private function generate_video_sql($start_time, $end_time, $cameras, $what) {
      $sql = "SELECT $what FROM video WHERE $start_time < time AND time < $end_time AND (";

      $first = true;
      foreach($cameras as $camera_num) {
         if(is_numeric($camera_num)) {
            if(!$first) $sql .= " OR ";
            $sql .= "camera_id = $camera_num";
            if($first) $first = false;
         } else {
            return array();
         }
      }
      $sql .= ")";
      return $sql;
   }

   // DATA
   private $conn;
}

?>
