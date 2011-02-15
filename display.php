<?php

require_once('video.php');
require_once('camera.php');
require_once('database.php');

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
      $string = "<h2>Most Recent Videos</h2>";

      $string .= "<table class=\"display\">";

      $counter = 0;
      foreach($this->videos as $video) {
         if($counter % 2 == 0) {
            $string .= "<tr>";
            $string .= $this->print_video($video);
         } else {
            $string .= $this->print_video($video);
            $string .= "</tr>";
         }
      }
      if($counter % 2 == 1) {
         $string .= "<td>&nbsp</td>";
         $string .= "</tr>";
      }
      $string .= "</table>";

      if($this->today==1) {
         $string .=  "There has been one video today.";
      } else {
         $string .= "There have been ".$this->today." videos today.";
      }
      return $string;
   }

   private function print_video($video) {
      $cameras = get_cameras();
      $string = "<td>";
      $string .= "<h3>Camera ".$video->camera_id()."</h3>";
      $string .= "<p>".$cameras[$video->camera_id()]->get_description()."</p>";

      $subpic = $video->picture_name();
      $subvid = $video->video_name();

      if($subpic == "" || $subvid == "") {
         $subpic="img/error.gif";
         $subvid="";
      }

      $string .= "<a href=$subvid><img class=\"home-preview\" src=$subpic></img></a>";
      $string .= "</td>";
      return $string;
   }
} // end class home_display

class results_display extends display {
   private $videos;
   private $number_of_videos;
   private $input;
   private $action;
   private $page_num;

   public function __construct($videos, $number_of_videos, $input, $page, $page_num) {
      $this->videos = $videos;
      if(is_numeric($number_of_videos) && $number_of_videos > 0) {
         $this->number_of_videos = $number_of_videos;
      } else {
         $this->number_of_videos = 0;
      }
      $this->input = $input;
      $this->action = "index.php?page=".$page;
      $this->page_num = $page_num;
   }

   public function __toString() {
      $string = "";
      if($this->input) $string .= (string)$this->input;
      if(!empty($this->videos)) {
         $string .= "<table class=\"display\">";
         foreach($this->videos as $video) {
            $string .= "<tr>";
            $string .= $this->print_video($video);
            $string .= "</tr>";
         }
         $string .= "</table>";
      } else {
         if($this->input->get_begin_time() >= $this->input->get_end_time()) {
            $string .= "<p>Invalid starting and ending dates.</p>";
         } else {
            $string .= "<p>No videos found.</p>";
         }
      }

      $string .= $this->print_page_navigation();
      return $string;
   }

   private function print_video($video) {
         $button="Remove Flag";
         if($video->flagged()==0) {
            $button="Flag";
         }

         $string = "<td>";

         $string .= "<a href=\"".$video->video_name()."\"><img class='preview' src=\"".$video->picture_name()."\"></img></a><br />";
         $string .= "<a href=\"".$video->picture_name()."\">Enlarge Picture</a></p>";

         $string .= "</td><td>";
         
         $cameras = get_cameras();
         $string .= "<a href=\"".$video->video_name()."\">".$video->print_time()."</a><br />";
         $string .= "<p>Camera ".$video->camera_id()." (".$cameras[$video->camera_id()]->get_description().")</p>";

         // The page to link to when flagging to keep all the info the same            
         $string .= "<form action=\"".$this->action."\" method=\"post\">";
         $string .= "<input type='hidden' name='vid_id' value=".$video->vid_id().">";
         $string .= "<input type='hidden' name='flagged' value=".$video->flagged().">";
         $string .= "<input type='submit' name='flag' value='$button'>&nbsp;";
         $string .= "<input type='submit' name='remove' value='Remove'>";
         $string .= "</form>";

         $string .= "</td>";
         return $string;
   }

   private function print_page_navigation() {
      if($this->number_of_videos < 2) return "";
      $first_video = (($this->page_num-1)*20+1);
      $last_video  = min((($this->page_num)*20), $this->number_of_videos);
      assert($first_video < $last_video);
      
      $string = "";
      if($last_video == $first_video) { // just one video on this page
         $string .= "Video $last_video";
      } else {
         $string .= "Videos $first_video-$last_video of ".$this->number_of_videos."<br />";
      }
      $max_page = floor($this->number_of_videos/20) + 1;

      $first    = ($this->page_num != 1)      ? "<a href=".$this->action."&page_num=1>first</a>" : "first";
      $previous = (($this->page_num - 1) > 0) ? "<a href=".$this->action."&page_num=".($this->page_num - 1).">previous</a>" : "previous";
      $next     = (($this->page_num) < $max_page) ? "<a href=".$this->action."&page_num=".($this->page_num + 1).">next</a>" : "next";
      $last     = ($this->page_num != $max_page) ? "<a href=".$this->action."&page_num=".$max_page.">last</a>" : "last";
      $string .= "$first | $previous | $next | $last<br />";

      return $string;
   }
} // end class results_display

class stats_display extends display {
   public function __construct() {

   }

   public function __toString() {
      $string  = "<h2>Stats</h2>";
      $string .= "<h3>Total</h3>";

      $string .= "<table class=\"stats\">";
      $string .= "<tr><td>Camera</td><td># of videos</td><tr>";
      $cameras = get_cameras();
      $total = 0;
      foreach($cameras as $camera){
         $sql = "select count(vid_id) from video where camera_id=".$camera->get_id();
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $camcount=$num['count(vid_id)'];
         $string .= "<td>Camera ".$camera->get_id()."</td><td>$camcount</td></tr>";
         $total += $camcount;
      }
      $string .= "<td>Total</td><td>$total</td></tr>";
      $string .= "</table>";

      //get the current date
      $date = getDate();
      $curmonth = $date["mon"];
      $curyear = $date["year"];
      $curmonnum=12*$curyear+$curmonth;

      //get first recorded date
      $sql = "select min(time) from video";
      $result = mysql_query($sql);
      $firdate = mysql_fetch_array($result,MYSQL_ASSOC);

      $first_time = getdate($firdate['min(time)']);
      $firmonth = $first_time['mon'];
      $firyear  = $first_time['year'];
      $firmonnum=12*$firyear+$firmonth;

      // display each month stats
      for($countmon=$curmonnum;$countmon>=$firmonnum;$countmon--){
         $year=floor($countmon/12);
         $month=$countmon%12;
         if($month==0){
            $month=12;
            $year--;
         }
         $monthname=date("F", mktime(0, 0, 0, $month, 1, 2010));
         $string .= "<h2>$monthname - $year</h2>";

         $start_time = mktime(0, 0, 0, $month, 1, $year);
         $end_time   = mktime(0, 0, 0, $month + 1, 1, $year);
         $sql = "select count(vid_id) from video where $start_time <= time and time < $end_time";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $total=$num['count(vid_id)'];

         $string .= "<table class=\"stats\"><tr><td>Camera</td><td># of videos</td><tr>";
         $total = 0;
         foreach($cameras as $camera){
            $sql = "select count(vid_id) from video where camera_id=".$camera->get_id()." and $start_time <= time and time < $end_time";
            $result = mysql_query($sql);
            $num = mysql_fetch_array($result,MYSQL_ASSOC);
            $camcount=$num['count(vid_id)'];
            $string .= "<td>Camera ".$camera->get_id()."</td><td>$camcount</td></tr>";
         }
         $string .= "<td>Total</td><td>$total</td></tr>";
         $string .= "</table>";
      }
      return $string;
   }
} // end class stats_display

class manage_display extends display {
   private $cameras;

   public function __construct() {
   }

   public function __toString() {
      $string = "<form action='index.php?page=manage' method='post'>";
      $cameras = get_cameras();
      foreach($cameras as $camera) {
         $camnum = $camera->get_id();
         $string .= "<h3>Camera $camnum</h3><br />";

         $string .= "<table id='manage'>";
         $string .= "<tr><td><p>Description: </p></td><td><input type='text' name='desc$camnum' value='".$camera->get_description()."' /></td></tr>";
         $string .= "<tr><td><p>Host: </p></td><td><input type='text' name='host$camnum' value='".$camera->get_hostname()."' /></td></tr>";
         $string .= "<tr><td><p>Port: </p></td><td><input type='text' name='port$camnum' value='".$camera->get_port()."' /></td></tr>";
         $string .= "</table>";
      }
      $string .= "<br /><br />";
      $string .= "<input type='submit' name='submit' value='Submit'>";
      $string .= "</form>";
      return $string;
   }
} // end class manage_display
