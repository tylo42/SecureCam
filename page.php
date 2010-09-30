<?php

require_once('connect.php');

abstract class page {
   abstract protected function page_name();
   abstract public function body();

   public function title() {
      echo "SecureCam - Camera Security System - ";
      $this->page_name();
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
}
?>
