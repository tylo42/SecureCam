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
<?php
require_once('database.php');
require_once('page_factory.php');

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'On');


if(!isset($_GET['page'])) $_GET['page'] = "";
if(!isset($_GET['page_num']) || !is_numeric($_GET['page_num'])) $_GET['page_num'] = 1;
$page = page_factory($_GET['page'], $_GET['page_num']);

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link type="text/css" rel="stylesheet" media="all" href="style.css">
<title><?php $page->title(); ?></title></head>

<body>
<div id="page_bg">
<div id="heading">
<h1>SecureCam</h1>
<p id="subtitle">Camera Security System</p>
</div>

<?php


// ------- Camera links at top bar ----------

echo "<div id=\"camera-links\">";
echo "<ul>";
$cameras = get_cameras();
foreach($cameras as $camera) {
   echo "<li><a href=\"http://".$camera->get_hostname().":".$camera->get_port()."\">Camera ".$camera->get_id()."</a></li>";
}
echo "</ul>";
echo "</div>";
?>

<table id="main">
<tr>
<td class="sidebar">
<ul class="sidebar">
   <li><a href="index.php">Home</a></li>
   <li><a href="index.php?page=search">Search</a></li>
   <li><a href="index.php?page=browse">Browse</a></li>
   <li><a href="index.php?page=stats">Stats</a></li>
</ul>
</td>

<td class="main-page">
<?php $page->body(); ?>
</td>
</tr>
</table>
</div>
</body>
</html>
