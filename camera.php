<?php
/* Copyright 2008, 2009, 2010, 2011 Tyler Hyndman
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

class camera {
   function __construct($camera_id, $hostname, $port, $description) {
      assert(is_numeric($camera_id));
      assert(is_numeric($port));
      $this->camera_id = $camera_id;
      $this->hostname = $hostname;
      $this->port = $port;
      $this->description = $description;
      $this->checked = true;
   }

   public function get_id() { return $this->camera_id; }
   public function get_hostname() { return $this->hostname; }
   public function get_port() { return $this->port; }
   public function get_description() { return $this->description; }
   public function get_checked() { return $this->checked; }

   public function put_checked($checked) { assert(is_bool($checked)); $this->checked = $checked; }

   private $hostname;
   private $port;
   private $description;
   private $checked;
}

function get_cameras() {
   if(!isset($_SESSION['cameras'])) {
      $_SESSION['cameras'] = securecam_database::singleton()->get_cameras();
   }
   return $_SESSION['cameras'];
}

function unset_cameras() {
   unset($_SESSION['cameras']);
}

?>
