<?php
//	VERSION 1.0
//	DATE: 8.20.08
//	NOTES: 

function display($sql,$action){
	echo "<table align='center' border=\"1\">";

	$result = mysql_query($sql);

	// populate the array
	$counter = 1;
	while($video = mysql_fetch_array($result,MYSQL_ASSOC)) {
		if(!isset($video['picture_name'])) {
			$video['picture_name'] = "img/nopic.gif";
		}
		
		$video['picture_name'] = rmvarwww($video['picture_name']);
		$video['video_name'] = rmvarwww($video['video_name']);
		
		$date_time = date("F j, Y - h:i:s A",$video['time']);

		$nflag=0;
		$button="Remove Flag";
		if($video['flagged']==0){
			$nflag=1;
			$button="Flag";
		}

		if($counter==1) {
			$table.="<tr>";
		} else if(!(is_int($counter/2))) {
			echo "</tr><tr>";
		}
		echo "<td width=\"320px\"><font size=\"4\"><p id=\"".$video['vid_id']."\" align=\"center\"><u>".$date_time."</u></p></font>";

		// The page to link to when flagging to keep all the info the same				
		echo "<form action=\"$action&flag=$nflag&idvid=".$video['vid_id']."#".$video['vid_id']."\" method=\"post\">";
		echo "<input type='submit' value='$button'>";
		echo "</form>";

		echo "<a href=\"".$video['video_name']."\"><img width='320' height='240' src=\"".$video['picture_name']."\"></img></a><br>";
		echo "<a href=\"".$video['picture_name']."\">Enlarge Picture</a><br><br></td>";
		$counter++;
	}
	if(is_int($counter/2))
		echo "<td width=\"320px\">&nbsp</td>";
	echo "</tr></table>";
}
?>
