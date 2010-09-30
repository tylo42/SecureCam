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
require_once('functions.php');
require_once('page.php');

class manage_page extends page {
   protected function page_name() {
      echo "Manage";
   }

   public function body() {

      if(isset($_GET['camera'])){
         $camera=$_GET['camera'];
         $desc=$_POST['desc'];
         $sql = "update camera set description=\"$desc\" where camera_id=$camera";
         $result = mysql_query($sql);
      }

      for($camnum=1;$camnum<=numcamera();$camnum++){
         $sql = "select description from camera where camera_id=$camnum";
         $result = mysql_query($sql);
         $info = mysql_fetch_array($result,MYSQL_ASSOC);

         echo "<p><font size=\"6\"><u>Camera $camnum</u></font><br>";

         echo "<form action='manage.php?camera=$camnum' method='post'>";
         echo "Description: ";
         echo "<textarea name='desc' rows='1'>";
         echo $info['description'];
         echo "</textarea><br><br>";
         echo "<input type='submit' value='Edit'>";
         echo "</form>";
         echo "<br><br>";
      }
   }
}
?>
