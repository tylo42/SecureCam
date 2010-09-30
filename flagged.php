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
require_once('display.php');
require_once('page.php');

class flagged_page extends page {
   protected function page_name() {
      echo "Flagged";
   }

   public function body() {

      // Remove flag
      if($_GET['flag']==0)
         deflag($_GET['idvid'], $_GET['idpic']);

      echo "<u><h1 align=\"center\">Flagged</h1></u>";

      $checkarray= array (1 => $_GET['camera1'], 2 => $_GET['camera2'], 3 => $_GET['camera3'], 4 => $_GET['camera4'], 5 => $_GET['camera5'], 6 => $_GET['camera6'], 7 => $_GET['camera7'], 8 => $_GET['camera8'], 9 => $_GET['camera9']);

      //check all for first time
      if($_GET['first']==1)
         for($count=1;$count<=$this->number_of_cameras();$count++)
            $checkarray[$count]=1;

      echo "<form action=\"flagged.php?second=1\" method=\"get\">";

      for($count=1;$count<=$this->number_of_cameras();$count++){
         echo "Camera $count:";
         $checked = ($checkarray[$count]==1) ? "checked" : "";
         echo "<input type='checkbox' name='camera$count' value='1' $checked>";
         echo "<br>";
      }

      echo "<br>";
      echo "<input type='submit' value='Add/Remove Cameras'>";
      echo "</form>";

      // If no camera is specified
      for($count=1;$count<=$this->number_of_cameras();$count++){
         if($checkarray[$count]==1) {
            break;
         }
      }



      // generate sql 
      $sql = "select * from video where flagged=1 and ( ";
      $first=0;
      for($camnum=1; $camnum<=$this->number_of_cameras(); $camnum++) {
         if($checkarray[$camnum]==1) {
            if($first==0) {
               $first = 1;
               $sql .= "camera_id = $camnum";
            } else {
               $sql .= " or camera_id = $camnum";
            }
         }
      }

      // if no cameras selected
      if($first == 0) {
         echo "<p>Please select a camera</p>";
         $sql = "";
      } else {
         $sql .= ") order by time";
      }

      $action="flagged.php?";
      for($x=1;$x<=$this->number_of_cameras()&&$x<9;$x++)
         $action.="&camera$x=$checkarray[$x]";
      display($sql,$action);
   }
}
?>
