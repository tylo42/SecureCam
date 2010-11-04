<?php

require_once('video.php');

abstract class display {
   abstract function __toString();
}

class home_display extends display {
   private $videos;
   private $today;
   private $action;

   public function __construct($videos, $today, $action) {
      $this->videos = $videos;

      if(!is_numeric($today) || $today < 0) {
         $this->today = 0;
      } else {
         $this->today = $today;
      }
      $this->action = $action;
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

class results_display extends display {
   private $videos;
   private $number_of_videos;

   public function __construct($videos, $number_of_videos) {
      $this->videos = $videos;
      if(is_numeric($number_of_videos) && $number_of_videos > 0) {
         $this->number_of_videos = $number_of_videos;
      } else {
         $this->number_of_videos = 0;
      }
   }

   public function __toString() {
      echo "<table class=\"display\">";
      foreach($this->videos as $video) {
         echo "<tr>";
         $this->print_video($video);
         echo "</tr>"
      }
      echo "</table>";

      $this->print_page_nums($begin_time, $end_time, $cameras);
   }

   private function print_video($video) {
         $button="Remove Flag";
         if($video->flagged()==0) {
            $button="Flag";
         }

         echo "<td>";

         echo "<a href=\"".$video->video_name()."\"><img class='preview' src=\"".$video->picture_name()."\"></img></a><br />";
         echo "<a href=\"".$video->picture_name()."\">Enlarge Picture</a></p>";

         echo "</td><td>";
         
         echo "<a href=\"".$video->video_name()."\">".$date_time."</a><br />";
         echo "<p>Camera ".$video->camera_id()." (".$this->get_description($video['camera_id']).")</p>";

         // The page to link to when flagging to keep all the info the same            
         echo "<form action=\"".$this->action."\" method=\"post\">";
         echo "<input type='hidden' name='vid_id' value=".$video->vid_id().">";
         echo "<input type='hidden' name='flagged' value=".$video->flagged().">";
         echo "<input type='submit' name='flag' value='$button'>&nbsp;";
         echo "<input type='submit' name='remove' value='Remove'>";
         echo "</form>";

         echo "</td>";
   }

   private function print_page_nums($begin_time, $end_time, $cameras) {
      if(!isset($_GET['page_num'])) {
         $_GET['page_num'] = 1;
      }
      if(!is_numeric($_GET['page_num'])) {
         $_GET['page_num'] = 1;
      }

      for($i=1; $i<($this->number_of_videos/20) + 1; $i++) {
         if($_GET['page_num'] == $i) {
            echo "$i&nbsp&nbsp&nbsp";
         } else {
            echo "<a href=".$this->action."&page_num=$i>$i</a>&nbsp&nbsp&nbsp";
         }
      }
   }
}
