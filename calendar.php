<?
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

require_once('connect.php');

function calendar($date){
   //If no parameter is passed use the current date.
   if($date == null) $date = getDate();

   $day = $date["mday"];
   $month = $date["mon"];

   $pmonth = $month-1;
   $year = $date["year"];

   $month_name=date("F", mktime(0, 0, 0, $date["mon"], 1, 2000)); // FIX ME: This does not work if a link is clicked in the calendar

   if($month==12){
      $nmonth=1;
      $nyear=$year+1;
   } else {
      $nmonth = $month+1;
      $nyear=$year;
   }

   if($month==1){
      $pmonth=12;
      $pyear=$year-1;
   } else {
      $pmonth = $month-1;
      $pyear=$year;
   }

   $this_month = getDate(mktime(0, 0, 0, $month, 1, $year));
   $next_month = getDate(mktime(0, 0, 0, $month + 1, 1, $year));

   //Find out when this month starts and ends.
   $first_week_day = $this_month["wday"];
   $days_in_this_month = round(($next_month[0] - $this_month[0]) / (60 * 60 * 24));

   $calendar_html = "<table id=\"cal\">";

   $calendar_html .= "<tr class=\"cal_title\"><td colspan=\"7\"><a href=index.php?page=browse&mday=1&mon=$pmonth&year=$pyear>&larr;</a>&nbsp;&nbsp;";
   $calendar_html .= "$month_name $year";
   $calendar_html .= "&nbsp;&nbsp;<a href=index.php?page=browse&mday=1&mon=$nmonth&year=$nyear>&rarr;</a></td></tr>";

   $calendar_html .= "<tr>";
   $calendar_html .= "<td class=\"cal_top\">S</td>";
   $calendar_html .= "<td class=\"cal_top\">M</td>";
   $calendar_html .= "<td class=\"cal_top\">T</td>";
   $calendar_html .= "<td class=\"cal_top\">W</td>";
   $calendar_html .= "<td class=\"cal_top\">T</td>";
   $calendar_html .= "<td class=\"cal_top\">F</td>";
   $calendar_html .= "<td class=\"cal_top\">S</td>";
   $calendar_html .= "</tr><tr>";

   //Fill the first week of the month with the appropriate number of blanks.
   for($week_day = 0; $week_day < $first_week_day; $week_day++) {
      $calendar_html .= "<td class=\"cal_empty\"> </td>";
   }

   $week_day = $first_week_day;
   for($day_counter = 1; $day_counter <= $days_in_this_month; $day_counter++) {
      $week_day %= 7;

      if($week_day == 0)
         $calendar_html .= "</tr><tr>";

      //sql search to see if there is any info for that day
      $begin_day = mktime(0, 0, 0, $month, $day_counter, $year);
      $end_day = mktime(0, 0, 0, $month, $day_counter+1, $year);

      $sql = "select * from video where $begin_day <= time and time < $end_day"; // THIS IS BAD
      $result = mysql_query($sql);
      $vid = mysql_fetch_array($result,MYSQL_ASSOC);

      //Do something different for the current day.
      $class = "cal_day";
      if($day == $day_counter) {
         $class = "cal_selected_day";
      }

      if($vid['vid_id']==null) {
         $calendar_html .= "<td class=\"$class\">$day_counter</td>";
      } else {
         $calendar_html .= "<td class=\"$class\"><a href=index.php?page=browse&mday=$day_counter&mon=$month&year=$year>$day_counter</a></td>";
      }

      $week_day++;
   }

   $calendar_html .= "</tr>";
   $calendar_html .= "</table>";

   return($calendar_html);
}
?>
