<?php

require_once('video.php');

abstract class display {
   abstract function __toString();
}

class home_display extends display {
   private $videos;
   private $today;

   public function __construct($videos, $today) {
      $this->videos = $videos;

      if(!is_numeric($today) || $today < 0) {
         $this->today = 0;
      } else {
         $this->today = $today;
      }
   }

   public function __toString() {
      echo "<h2>Most Recent Videos</h2>";

      echo "<table class=\"display\">";

      $counter = 0;
      foreach($this->videos as $video) {
         if($counter % 2 == 0) {
            echo "<tr>";
            $this->print_video($video);
         } else {
            $this->print_video($video);
            echo "</tr>";
         }
      }
      if($counter % 2 == 1) {
         echo "<td>&nbsp</td>";
         echo "</tr>";
      }
      echo "</table>";

      if($this->today==1) {
         echo "There has been one video today.";
      } else {
         echo "There have been ".$this->today." videos today.";
      }
   }

   private function print_video($video) {
      echo "<td>";
      echo "<h3>Camera $video->vid_id()</h3>";
      echo "<p>".$cameras->get_description($camnum)."</p>";

      $subpic = $this->get_path($video->get_picture_name());
      $subvid = $this->get_path($video->get_video_name());

      if($subpic == "" || $subvid == "") {
         $subpic="img/error.gif";
         $subvid="";
      }

      echo "<a href=$subvid><img class=\"home-preview\" src=$subpic></img></a>";
      echo "</td>";
   }
}
