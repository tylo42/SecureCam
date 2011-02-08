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



function page_factory($page_name, $page_num) {
   $sc_database = securecam_database::singleton();

   if($page_name == "search") {
      $date = getDate();
      $begin_day = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
      $end_day = mktime(0, 0, 0, $date['mon'], $date['mday']+1, $date['year']);

      $videos = $sc_database->search_videos($begin_day, $end_day, array(1, 2), $page_num);
      $number_of_videos = $sc_database->number_of_videos($begin_day, $end_day);

      $input = new search_input(first_year(), last_year());
      $search_display = new results_display($videos, $number_of_videos, $input, $page_name, $page_num);

      return new page($search_display, "Search");
      
   } else if($page_name == "browse") {
      $date = getDate();
      $begin_day = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
      $end_day = mktime(0, 0, 0, $date['mon'], $date['mday']+1, $date['year']);

      $videos = $sc_database->search_videos($begin_day, $end_day, array(1, 2), $page_num);
      $number_of_videos = $sc_database->number_of_videos($begin_day, $end_day);

      $browse_display = new results_display($videos, $number_of_videos, NULL, $page_name, $page_num);

      return new page($browse_display, "Browse");
      
   } else if($page_name == "flagged") {
      $date = getDate();
      $begin_day = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
      $end_day = mktime(0, 0, 0, $date['mon'], $date['mday']+1, $date['year']);

      $videos = $sc_database->search_videos($begin_day, $end_day, array(1, 2), $page_num, true);
      $number_of_videos = $sc_database->number_of_videos($begin_day, $end_day, true);

      $browse_display = new results_display($videos, $number_of_videos, NULL, $page_name, $page_num);

      return new page($browse_display, "Flagged");
      
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

      return new page(new manage_display(), "Manage");
      
   } else if($page_name == "stats") {
      return new page(new stats_display(), "Stats");
      
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
