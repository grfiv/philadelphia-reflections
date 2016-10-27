<?php

     function bad_ip($ip) {
     /**
      * Compare an ip address to a file
      * of bad-actor ip addresses
      * and return true if there's a match
      *
      * @param $ip (string) ip address to test
      *
      * @returns (bool) true if $ip matches any of the addresses in the file
      *
      */
         $filename = $_SERVER["DOCUMENT_ROOT"] . "/inc/bad_actors_ips.txt";
         $lines = file($filename, FILE_IGNORE_NEW_LINES);

         foreach ($lines as $line) {
             if (trim($ip) == trim($line)) return true;
         }
         return false;
     }
?>
