<?php

abstract class video {
   private $time;
   private $video_name;
   private $picture_name;
   private $camera_id;
   private $flagged;

   public function __construct($vid_id, $time, $video_name, $picture_name, $camera_id, $flagged) {
      $this->vid_id       = $vid_id;
      $this->time         = $time;
      $this->video_name   = $video_name;
      $this->picture_name = $picture_name;
      $this->camera_id    = $camera_id;
      $this->flagged      = $flagged;
   }

   public function vid_id() {
      return $this->vid_id;
   }

   public function print_time() {
      return date("F j, Y - h:i:s A", $time);
   }

   public function video_name() {
      return $this->video_name;
   }

   public function picture_name() {
      return $this->picture_name;
   }

   public function camera_id() {
      return $this->camera_id;
   }

   public function flagged() {
      return $this->flagged;
   }
}

?>