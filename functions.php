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
require_once('connect.php');

// numcamera finds the number of cameras there are in the system
function numcamera(){

	$sql = "select count(distinct camera_id) from camera;";
	$result = mysql_query($sql);
	$num = mysql_fetch_array($result,MYSQL_ASSOC);
	$numcam=$num['count(distinct camera_id)'];

	return($numcam);
}

// flags the video and pic
function flag($vid_id){
	$sql="update video set flagged=1 where vid_id=$vid_id";
	mysql_query($sql);
}

// removes flages from video and pic
function deflag($vid_id){
	$sql="update video set flagged=0 where vid_id=$vid_id";
	mysql_query($sql);
}

function rmvarwww($input){
	$output =strstr ($input, "snapshots");
	return ($output);
}

function getdescription($cameranumber){
	$sql = "select description from camera where camera_id=$cameranumber";
	$result = mysql_query($sql);
	$info = mysql_fetch_array($result,MYSQL_ASSOC);
	
	return($info['description']);
}

?>
