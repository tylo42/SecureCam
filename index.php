<!-- Copyright 2008, 2009, 2010 Tyler Hyndman
   
   This file is part of SecureCam.
 
   SecureCam is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   SecureCam is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with SecureCam.  If not, see <http://www.gnu.org/licenses/>.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link type="text/css" rel="stylesheet" media="all" href="style.css">
<title>SecureCam - Camera Security System</title></head>
<body>
<table style="width: 1075px; height: 743px; text-align: left; margin-left: auto; margin-right: auto;" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td style="width: 50px; height: 106px; background-color: blue; vertical-align: top;"><img style="width: 50px; height: 100px;" alt="" src="img/topleft.gif"><br><br></td>
<td style="width: 300px; height: 106px; background-color: blue; vertical-align: top;"><a href="index.php"><img style="width: 300px; height: 100px;" border="0" alt="SecureCam" src="img/Logo.gif"></a><br><br></td>
<td style="width: 700px; height: 106px; vertical-align: top; background-color: blue; text-align: center;"><img style="border: 0px solid ; width: 300px; height: 100px;" alt="Camera Securty System" src="img/bartextv1.gif" hspace="0" vspace="0"><img style="border: 0px solid ; width: 400px; height: 100px;" alt="" src="img/bar.gif" hspace="0" vspace="0"><br>

<?php
require_once('connect.php');
require_once('functions.php');

// ------- Camera links at top bar ----------

$numcam=numcamera();

for($count=1;$count<=$numcam;$count++){
	echo "<a href=\"index.php?page=camera&num=$count\"><img style=\"width: 150px; height: 50px;\" border=\"0\" alt=\"Camera $count\" src=\"img/Camera$count.gif\"></a>";
	if($count%4==0) {
		echo "<br>";
	}
}
?>

<img style="border: 0px solid ; width: 700px; height: 5px;" alt="" src="img/blueshim.gif" hspace="0" vspace="0"></td>
<td style="width: 0px; height: 106px; background-color: blue; vertical-align: top;"><img style="width: 50px; height: 100px;" alt="" src="img/topright.gif"><br><br></td>
</tr>
<tr>
<td style="width: 50px; background-color: blue; height: 406px;"></td>
<td style="width: 300px; height: 406px; background-color: blue; vertical-align: top;">
<a href="index.php"><img style="width: 225px; height: 75px;" border="0" alt="home" src="img/home.gif"></a><br>
<a href="index.php?page=search"><img style="width: 225px; height: 75px;" border="0" alt="Search" src="img/search.gif"></a><br>
<a href="index.php?page=browse"><img style="width: 225px; height: 75px;" border="0" alt="Browse" src="img/browse.gif"></a><br>
<a href="index.php?page=flagged"><img style="width: 225px; height: 75px;" border="0" alt="Flagged" src="img/flagged.gif"></a><br>
<a href="index.php?page=manage"><img style="width: 225px; height: 75px;" border="0" alt="Manage" src="img/manage.gif"></a><br>
<a href="index.php?page=stats"><img style="width: 225px; height: 75px;" border="0" alt="Stats" src="img/stats.gif"></a><br><br></td>
<td style="height: 406px;">



<! ------- Start php code --------->
<iframe height="500" width="700" frameborder="0"
<?php

for($count=1;$count<=numcamera();$count++){
	$sql="select hostname from camera where camera_id=$count";
	$result = mysql_query($sql);
	$host = mysql_fetch_array($result,MYSQL_ASSOC);
	$hostname[$count]=$host['hostname'];
}

if($_GET['page']=="search") {
	echo "src=\"search.php\"";
} else if($_GET['page']=="browse") {
	echo "src=\"browse.php?first=1\"";
} else if($_GET['page']=="flagged") {
	echo "src=\"flagged.php?first=1\"";
} else if($_GET['page']=="manage") {
	echo "src=\"manage.php\"";
} else if($_GET['page']=="stats") {
	echo "src=\"stats.php\"";
} else if($_GET['page']=="camera"&&isset($_GET['num'])) {
	$num=$_GET['num'];
	echo "src=\"http://$hostname[$num]:5070$num\"";
} else {
	echo "src=\"home.php\"";
}
?>
</iframe>

<! ------- End php code --------->

</td>
<td style="background-color: blue; width: 0px; height: 406px;"></td>
</tr>
<tr>
<td style="width: 50px; height: 100px;"><img style="width: 50px; height: 100px;" alt="" src="img/bottomleft.gif" align="top"></td>
<td style="width: 300px; background-color: blue; height: 100px;"></td>
<td style="background-color: blue; height: 100px;"></td>
<td style="width: 0px; height: 100px;"><img style="width: 50px; height: 100px;" alt="" src="img/bottomright.gif" align="top"></td>
</tr>
</tbody>
</table><div style="text-align: center;">
<br>
</div></body></html>
