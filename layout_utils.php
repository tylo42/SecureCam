<?php

function html_table($cols, $tds, $class) {
   assert(is_numeric($cols) && $cols > 0);
   assert(is_array($tds));
   assert(is_string($class));

   // pad out the remaining rows with empty tds
   while(sizeof($tds) % $cols != 0) {
      $tds[] = "&nbsp";
   }

   $string  = "\n<table class='$class'>\n";

   $count = 0;
   foreach($tds as $td) {
      if($count % $cols == 0) { // start of a row
         $string .= "<tr>";
      }
      $string .= "<td>$td</td>";
      if($count % $cols == ($cols-1)) { // end of row
         $string .= "</tr>\n";
      }
      $count++;
   }

   assert($count % $cols == 0); // a row should have finished

   $string .= "</table>\n";
   return $string;
} // end function html_table

?>
