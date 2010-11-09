<?php

require_once('video.php');
require_once('camera_collection.php');
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
      $string .= "<p>".$cameras->get_description($video->camera_id())."</p>";

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
         echo "</tr>";
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

class stats_display extends display {
   public function __construct() {

   }

   public function __toString() {
      echo "<h2>Stats</h2>";

      echo "<h3>Total</h3>";

      echo "<table class=\"stats\">";
      echo "<tr><td>Camera</td><td># of videos</td><tr>";
      for($count=1;$count<=$this->number_of_cameras();$count++){
         $sql = "select count(vid_id) from video where camera_id=$count";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $camcount=$num['count(vid_id)'];
         echo "<td>Camera $count</td><td>$camcount</td></tr>";
      }
      echo "<td>Total</td><td>$total</td></tr>";
      echo "</table>";

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
         echo "<br><u><h2>$monthname - $year</h2></u>";

         $start_time = mktime(0, 0, 0, $month, 1, $year);
         $end_time   = mktime(0, 0, 0, $month + 1, 1, $year);
         $sql = "select count(vid_id) from video where $start_time <= time and time < $end_time";
         $result = mysql_query($sql);
         $num = mysql_fetch_array($result,MYSQL_ASSOC);
         $total=$num['count(vid_id)'];

         echo "<table class=\"stats\"><tr><td>Camera</td><td># of videos</td><tr>";
         for($count=1;$count<=$this->number_of_cameras();$count++){
            $sql = "select count(vid_id) from video where camera_id=$count and $start_time <= time and time < $end_time";
            $result = mysql_query($sql);
            $num = mysql_fetch_array($result,MYSQL_ASSOC);
            $camcount=$num['count(vid_id)'];
            echo "<td>Camera $count</td><td>$camcount</td></tr>";
         }
         echo "<td>Total</td><td>$total</td></tr>";
         echo "</table>";
      }

   }
}

class manage_display extends display {
   public function __construct() {

   }

   public function __toString() {
      if(isset($_POST['submit'])) {
         for($camnum=1; $camnum<=$this->number_of_cameras(); $camnum++) {
            $desc = mysql_real_escape_string($_POST['desc'.$camnum]);
            $host = mysql_real_escape_string($_POST['host'.$camnum]);
            $port = mysql_real_escape_string($_POST['port'.$camnum]);
            if( is_numeric($port) &&
                ( $desc != $this->get_description($camnum) ||
                  $host != $this->get_hostname($camnum)    || 
                  $port != $this->get_port($camnum)) ) {
               $sql = "update camera set description=\"$desc\", hostname=\"$host\", port=$port where camera_id=$camnum";
               $result = mysql_query($sql);
               unset($_SESSION['description1']); // Session variables need to be reset from database
            }
         }
      }

      echo "<form action='index.php?page=manage' method='post'>";
      for($camnum=1;$camnum<=$this->number_of_cameras();$camnum++){
         echo "<h3>Camera $camnum</h3><br />";

         echo "<table id='manage'>";
         echo "<tr><td><p>Description: </p></td><td><input type='text' name='desc$camnum' value='".$this->get_description($camnum)."' /></td></tr>";
         echo "<tr><td><p>Host: </p></td><td><input type='text' name='host$camnum' value='".$this->get_hostname($camnum)."' /></td></tr>";
         echo "<tr><td><p>Port: </p></td><td><input type='text' name='port$camnum' value='".$this->get_port($camnum)."' /></td></tr>";
         echo "</table>";
      }
      echo "<br /><br />";
      echo "<input type='submit' name='submit' value='Submit'>";
      echo "</form>";
   }
}
