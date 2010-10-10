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
    */
   public function search_videos($start_time, $end_time, $cameras, $page_num=1) {
      if(!is_numeric($start_time) && !is_numeric($end_time) && empty($cameras) && !is_numeric($page_num) && $page_num < 1) {
         return array();
      }

      $sql = "SELECT * FROM video WHERE $start_time < time AND time < $end_time";

      foreach($cameras as $camera_num) {
         if(is_numeric($camera_num)) {
            $sql .= "AND camera_id = $camera_num";
         } else {
            return array();
         }
      }

      $sql .= "LIMIT 20 OFFSET ".(($page_num-1)*20);

      return $this->query_array($sql);
   }

   // HELPER FUNCTIONS
   private function query_array($sql) {
      $result = mysql_query($sql);
      return mysql_fetch_array($result);
   }

   // DATA
   private $conn;
}

?>
