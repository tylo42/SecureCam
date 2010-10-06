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
require_once('page.php');

class manage_page extends page {
   protected function page_name() {
      echo "Manage";
   }

   public function body() {

      if(isset($_POST['submit'])) {
         for($camnum=1; $camnum<=$this->number_of_cameras(); $camnum++) {
            $desc = mysql_real_escape_string($_POST['desc'.$camnum]);
            $host = mysql_real_escape_string($_POST['host'.$camnum]);
            $port = mysql_real_escape_string($_POST['port'.$camnum]);
            if( is_numeric($port) &&
                ( $desc != $this->get_description($camnum) ||
                  $host != $this->get_hostname($camnum)    || 
                  $port != $this->get_port($camnum)) ) {
               $sql = "update camera set description=\"$desc\", hostname=\"$host\", port=$port where camera_id=$camnum";
               $result = mysql_query($sql);
               unset($_SESSION['description1']); // Session variables need to be reset from database
            }
         }
      }

      echo "<form action='index.php?page=manage' method='post'>";
      for($camnum=1;$camnum<=$this->number_of_cameras();$camnum++){
         echo "<h3>Camera $camnum</h3><br />";

         echo "<table id='manage'>";
         echo "<tr><td><p>Description: </p></td><td><input type='text' name='desc$camnum' value='".$this->get_description($camnum)."' /></td></tr>";
         echo "<tr><td><p>Host: </p></td><td><input type='text' name='host$camnum' value='".$this->get_hostname($camnum)."' /></td></tr>";
         echo "<tr><td><p>Port: </p></td><td><input type='text' name='port$camnum' value='".$this->get_port($camnum)."' /></td></tr>";
         echo "</table>";
      }
      echo "<br /><br />";
      echo "<input type='submit' name='submit' value='Submit'>";
      echo "</form>";
   }
}
?>
