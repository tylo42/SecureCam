<?php
//	VERSION 1.0
//	DATE: 8.20.08
//	Description: This includes misc. functions
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
