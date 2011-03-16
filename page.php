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

class page {
   public function body() { echo $this->display; }

   // DATA
   private $page_name;
   private $display;

   public function __construct($display, $page_name) {
      $this->display   = $display;
      $this->page_name = $page_name;
   }

   public function page_name() { return $this->page_name; }

   public function title() {
      return "SecureCam - Camera Security System - ".$this->page_name;
   }
}
?>
