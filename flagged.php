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

class flagged_page extends page {
   protected function page_name() {
      echo "Flagged";
   }

   public function body() {

      echo "<h2>Flagged</h2>";

      echo "<form action=\"index.php?page=flagged\" method=\"post\">";
      $cameras = $this->put_camera_check_boxes();
      echo "<input name='submit' type='submit' value='Add/Remove Cameras'>";
      echo "</form>";

      $action = "index.php?page=flagged";
      $this->display(0, mktime(), $cameras, $action, 1);
   }
}
?>
