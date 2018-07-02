<?php

   session_start();

   include ("top.txt");
   include ("scripts.php");
   include ("display.txt");
   $filename = $_GET['dir'];
   display_tinylink_libs($filename);
   include ("bottom.txt");

?>