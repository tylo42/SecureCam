<?php
/* Copyright 2008, 2009, 2010, 2011 Tyler Hyndman
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

class video {
   private $vid_id;
   private $time;
   private $video_name;
   private $picture_name;
   private $camera_id;
   private $flagged;

   public function __construct($vid_id, $time, $video_name, $picture_name, $camera_id, $flagged) {
      $this->vid_id       = $vid_id;
      $this->time         = $time;
      $this->video_name   = $this->strip_path($video_name);
      $this->picture_name = $this->strip_path($picture_name);
      $this->camera_id    = $camera_id;
      $this->flagged      = $flagged;
   }

   public function vid_id() {
      return $this->vid_id;
   }

   public function print_time() {
      return date("F j, Y - h:i:s A", $this->time);
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

   // HELPER FUNCTIONS
   private function strip_path($input) {
      return strstr($input, "snapshots");
   }
}

?>
