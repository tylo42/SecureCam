<?php

abstract class page {
   abstract protected function page_name();
   abstract public function body();

   public function title() {
      echo "SecureCam - Camera Security System - ";
      $this->page_name();
   }
}
?>
