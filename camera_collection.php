<?php

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

class camera_collection {
   public function __construct() {
      $camera_array = array();
   }

   public function add_camera($id, $hostname, $port, $description) {
      $this->camera_array[$id] = new camera($hostname, $port, $description);
   }

   public function size() {
      return count($this->camera_array);
   }

   public function get_hostname($id) { 
      assert($this->camera_array[$id]);
      return $this->camera_array[$id]->get_hostname();
   }

   public function get_port($id) {
      assert($this->camera_array[$id]);
      return $this->camera_array[$id]->get_port();
   }

   public function get_description($id) {
      assert($this->camera_array[$id]);
      return $this->camera_array[$id]->get_description();
   }

   private $camera_array;
}

?>
