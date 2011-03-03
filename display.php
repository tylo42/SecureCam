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

require_once('video.php');
require_once('camera.php');

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
      $string  = "<h2>Most Recent Videos</h2>";
      $string .= "<hr />";

      $string .= "<table class=\"display\"><tr>";

      $counter = 1;
      foreach($this->videos as $video) {
         if($counter % 2 == 0) {
            $string .= $this->print_video($video);
            $string .= "</tr></tr>";
         } else {
            $string .= $this->print_video($video);
         }
         $counter++;
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
      $string .= "<h3>".$cameras[$video->camera_id()]->get_description()."</h3>";

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
      
      $string = "<p class='navigation'>";
      if($last_video == $first_video) { // just one video on this page
         $string .= "Video $last_video";
      } else {
         $string .= "Videos $first_video-$last_video of ".$this->number_of_videos."<br />";
      }
      $max_page = ceil($this->number_of_videos/20);

      $first    = ($this->page_num != 1)      ? "<a href=".$this->action."&page_num=1>first</a>" : "first";
      $previous = (($this->page_num - 1) > 0) ? "<a href=".$this->action."&page_num=".($this->page_num - 1).">previous</a>" : "previous";
      $next     = ($this->page_num < $max_page) ? "<a href=".$this->action."&page_num=".($this->page_num + 1).">next</a>" : "next";
      $last     = ($this->page_num != $max_page) ? "<a href=".$this->action."&page_num=".$max_page.">last</a>" : "last";
      $string .= "$first | $previous | $next | $last";
      
      $string .= "</p>";

      return $string;
   }
} // end class results_display

class stats_display extends display {
   private $stats;
   
   public function __construct($stats) {
      $this->stats = $stats;
   }

   public function __toString() {
      $string  = "<h2>Statistics</h2>";
      $string .= "<hr />";

      //get the current date
      $date = getDate();
      $curmonth = $date["mon"];
      $curyear = $date["year"];
      $curmonnum=12*$curyear+$curmonth;
      
      $string .= "<table id='stats'>";
      $string .= "<tr><th></th>";
      $count = 0;
      $countmon=$curmonnum;
      while($count < 12) {
         $year=floor($countmon/12);
         $month=$countmon%12;
         if($month==0){
            $month=12;
            $year--;
         }
         $monthname=date("M", mktime(0, 0, 0, $month, 1, $year));
         $string .= "<th>$monthname</th>";
         $count++;
         $countmon--;
      }
      $string .= "</tr>";
      
      foreach(get_cameras() as $camera) {
         $string .= $this->print_row($curmonnum, $camera->get_id(), $camera->get_description());
      }
      $string .= $this->print_row($curmonnum, "total", "Total");
      $string .= "</table>";
      
      return $string;
   }
   
   private function print_row($curmonnum, $name, $description) {
      $string  = "<tr>";
      $string .= "<td>$description</td>";
      $count = 0;
      $countmon=$curmonnum;
      while($count < 12) {
         $year=floor($countmon/12);
         $month=$countmon%12;
         if($month==0){
            $month=12;
            $year--;
         }
         
         $key = "$name-".date("Y-m", mktime(0, 0, 0, $month, 1, $year));
         if(isset($this->stats[$key])) {
            $string .= "<td>".$this->stats[$key]."</td>";
         } else {
            $string .= "<td>0</td>";
         }
         $count++;
         $countmon--;
      }
      $string .= "</tr>";
      return $string;
   }
} // end class stats_display

class manage_display extends display {
   private $max_videos;

   public function __construct($max_videos) {
      $this->max_videos = $max_videos;
   }

   public function __toString() {
      $string  = "<h2>Update camera data</h2>";
      $string .= "<hr />";
      $string .= "<form action='index.php?page=manage' method='post'>";
      foreach(get_cameras() as $camera) {
         $camnum = $camera->get_id();
         $string .= "<h3>Camera $camnum</h3><br />";

         $string .= "<table class='manage'><tr><td>";
         foreach($this->max_videos as $video) {
            if($video->camera_id() == $camera->get_id()) {
               $string .= "<img class='manage-preview' src=".$video->picture_name()." />";
               break;
            }
         }
         $string .= "</td><td>";

         $string .= "<table class='manage-input'>";
         $string .= "<tr><td><p>Description: </p></td><td><input type='text' name='desc$camnum' value='".$camera->get_description()."' /></td></tr>";
         $string .= "<tr><td><p>Host: </p></td><td><input type='text' name='host$camnum' value='".$camera->get_hostname()."' /></td></tr>";
         $string .= "<tr><td><p>Port: </p></td><td><input type='text' name='port$camnum' value='".$camera->get_port()."' /></td></tr>";

         $string .= "</table>";
         $string .= "</td></tr></table>";
      }
      $string .= "<br /><br />";
      $string .= "<input type='submit' name='submit' value='Submit'>";
      $string .= "</form>";
      return $string;
   }
} // end class manage_display

class view_display extends display {
   public function __construct() {
   }
   
   public function __toString() {
      $string  = "<h2>View live cameras</h2>";
      $string .= "<hr />";
      $counter = 1;
      foreach(get_cameras() as $camera) {
         $string .= "<iframe class='view' src=\"http://".$camera->get_hostname().":".$camera->get_port()."\"></iframe>\n";
         if(($counter % 2) == 0) $string .= "<br />";
         $counter++;
      }
      return $string;
   }
} // end class view_display

class login_display extends display {
   
   public function __construct() {
   }
   
   public function __toString() {
      $string  = "<form method='POST' action='index.php'>";
      $string .= "<table>";
      $string .= "<tr><td>Username:</td><td><input type='text' name='username'</td></tr>";
      $string .= "<tr><td>Password:</td><td><input type='password' name='password'</td></tr>";
      $string .= "</table>";
      $string .= "<input type='submit' value='Log In'>";
      $string .= "</form>";
      return $string;
   }
} // end class login
