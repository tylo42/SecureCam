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

// connect to database
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die 
   ('Error connectiong to mysql');

$dbname = 'securecam';
mysql_select_db($dbname);

error_reporting(E_ALL);
ini_set("display_errors", 1);


$filetype=$_SERVER['argv'][1]; // %n

if($filetype==8){
   $sql="insert into video(time, video_name, event, camera_id) values(";
   $sql.="'".$_SERVER['argv'][2]."', ";         // %s
   $sql.="'".path($_SERVER['argv'][3])."', ";   // %f
   $sql.="'".$_SERVER['argv'][4]."', ";         // %v
   $sql.=$_SERVER['argv'][5].")";               // camera_id
   mysql_query($sql);
} else if($filetype==1) {
   // Since each camera is in its own thread and a picture will only
   // be made after a video has been create, we can assume that the
   // latest video with this camera should be associated with this picture
   $sql="select max(vid_id) from video where camera_id=".$_SERVER['argv'][3];
   $result=mysql_query($sql);
   // DEBUG: system("echo \"$sql\" >> log");
   $max=mysql_fetch_array($result,MYSQL_ASSOC);
   $sql="update video set picture_name='".path($_SERVER['argv'][2])."' where vid_id=".$max['max(vid_id)'];
   echo $sql;
   mysql_query($sql);
}

function path($input) {
   $pos=strpos($input,"/images");
   if($pos===false) {
      return $input;
   } else {
      return substr($input,$pos);
   }
}
?>
