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

require_once('page.php');
require_once('display.php');
require_once('database.php');
require_once('input.php');


function first_year() {
   static $first_year = 0;
   if($first_year < 1) {
      $first_year = date("Y",securecam_database::singleton()->get_time("min"));
   }
   return $first_year;
}

function last_year() {
   static $last_year = 0;
   if($last_year < 1) {
      $last_year = date("Y",securecam_database::singleton()->get_time("max"));
   }
   return $last_year;
}

function valid_user($username, $password) {
   if($username == "Tylo42" && $password == "lame") {
      return true;
   } else {
      return false;
   }
}

function logged_in() {
   $secret_word = "jt+*=i>b&woq~;TC/:<60917v]B:xmT7)gWljcr->j.r$%nr#/X{BQi{,~xO[yI";
   if(isset($_COOKIE['login'])) {
      list($username,$hash) = explode(',',$_COOKIE['login']);
      if(md5($username.$secret_word) != $hash) { // invalid cookie
         return false;
      }
   } else if(isset($_POST['username']) && valid_user($_POST['username'], $_POST['password'])) { // login attempt
      setcookie('login', $_POST['username'].','.md5($_POST['username'].$secret_word), time()+60*60*4); // set 4 hour cookie
   } else { // first time visitor
      return false;
   }
   return true;
}

function page_factory($page_name, $page_num) {
   $sc_database = securecam_database::singleton();
   
   if(!logged_in()) {
      return new page(new login_display(), "Login");
   }
   
   // reset the session if the previous page is not the same as the current page
   if(isset($_SESSION['previous_page']) && $_SESSION['previous_page'] != $page_name) {
      session_destroy();
   }
   $_SESSION['previous_page'] = $page_name;

   if($page_name == "search") {
      if(isset($_POST['flag'])) {
         if($_POST['flagged'] == 0) {
            $sc_database->add_flag($_POST['vid_id']);
         } else {
            $sc_database->remove_flag($_POST['vid_id']);
         }
      }

      $date = getDate();
      $begin_day = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
      $end_day   = mktime(0, 0, 0, $date['mon'], $date['mday']+1, $date['year']);
      $flagged   = false;
      $selected_cameras = array();
      if(isset($_POST['submit'])) {
         $begin_day = $_SESSION['search_begin'] = mktime($_POST['shour']+$_POST['sampm'], $_POST['smin'], 0, $_POST['smonth'], $_POST['sday'], $_POST['syear']);
         $end_day   = $_SESSION['search_end']   = mktime($_POST['ehour']+$_POST['eampm'], $_POST['emin'], 0, $_POST['emonth'], $_POST['eday'], $_POST['eyear']);
         $flagged   = $_SESSION['flag_check']   = isset($_POST['flag_check']) ? true : false;

         foreach(get_cameras() as $camera) {
            if(isset($_POST['camera'.$camera->get_id()])) { 
               $camera->put_checked(true); 
            } else {
               $camera->put_checked(false);
            }
         }
      } else if(isset($_SESSION['search_begin'])) {
         $begin_day = $_SESSION['search_begin'];
         $end_day   = $_SESSION['search_end'];
         $flagged   = $_SESSION['flag_check'];
      }

      $videos = $sc_database->search_videos($begin_day, $end_day, $page_num, $flagged);
      $number_of_videos = $sc_database->number_of_videos($begin_day, $end_day, $flagged);

      $input = new search_input(get_cameras(), first_year(), last_year(), $begin_day, $end_day, $flagged);
      $search_display = new results_display($videos, $number_of_videos, $input, $page_name, $page_num);

      return new page($search_display, "Search");

   } else if($page_name == "manage") {
      // update camera data if data changed
      if(isset($_POST['submit'])) {
         $cameras = get_cameras();
         foreach($cameras as $camera) {
            $camnum = $camera->get_id();
            $desc = $_POST['desc'.$camnum];
            $host = $_POST['host'.$camnum];
            $port = $_POST['port'.$camnum];
            $sc_database->update_camera($camnum, $desc, $host, $port);
         }
      }

      $max_videos = $sc_database->get_max_videos();
      $manage_display = new manage_display($max_videos);
      return new page($manage_display, "Manage");
      
   } else if($page_name == "stats") {
      $stats = $sc_database->get_stats();
      $stats_display = new stats_display($stats);
      return new page($stats_display, "Stats");
      
   } else if($page_name == "view") {
      return new page(new view_display(), "Live View");
      
   } else {
      $videos = $sc_database->get_max_videos();

      $date = getDate();
      $begin_day = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
      $end_day = mktime(0, 0, 0, $date['mon'], $date['mday']+1, $date['year']);
      $today = $sc_database->number_of_videos($begin_day, $end_day);

      $action = "";

      $home_display = new home_display($videos, $today, $action);

      return new page($home_display, "Home");
   }
}
