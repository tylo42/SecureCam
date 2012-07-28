<?php
//	VERSION 1.0
//	DATE: 8.20.08
$dbhost = '';
$dbuser = '';
$dbpass = '';

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die 
	('Error connectiong to mysql');

$dbname = 'securecam';
mysql_select_db($dbname);
?>
