#!/usr/bin/php -q
<?php

require_once('connect.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

// DEBUG
echo 'Number of arguments passed:' , $_SERVER['argc'] , "\n";
$count = 0;
foreach ($_SERVER['argv'] as $arg){
	echo "argv[".$count++."]:$arg\n";
}


$filetype=$_SERVER['argv'][1]; // %n

if($filetype==8){
	$sql="insert into video(time, video_name, event, camera_id) values(";
	$sql.="'".$_SERVER['argv'][2]."', "; 	    // %s
	$sql.="'".path($_SERVER['argv'][3])."', "; 	// %f
	$sql.="'".$_SERVER['argv'][4]."', "; 	    // %v
	$sql.=$_SERVER['argv'][5].")"; 	            // camera_id
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

// DEBUG:  system("echo \"$sql\" >> log");

function path($input) {
	$pos=strpos($input,"/images");
	if($pos===false) {
		return $input;
	} else {
		return substr($input,$pos);
	}
}
?>
