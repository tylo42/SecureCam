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

class camera {
   function __construct($hostname, $port, $description) {
      assert(is_numeric($port));
      $this->hostname = $hostname;
      $this->port = $port;
      $this->description = $description;
   }

   public function get_hostname() { return $this->hostname; }
   public function get_port() { return $this->port; }
   public function get_description() { return $this->description; }

   private $hostname;
   private $port;
   private $description;
}

function get_cameras() {
   if(!isset($_SESSION['cameras'])) {
      $database = new securecam_database();
      $_SESSION['cameras'] = $database->get_cameras();
   }
   return $_SESSION['cameras'];
}

?>
