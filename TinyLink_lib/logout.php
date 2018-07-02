<?
   session_start();
   unset($_SESSION["username"]);
   unset($_SESSION["email"]);
 
   include("top.txt");
   echo "Thank you for using the Workflow System.";
   echo "You may <a href=\"login.php\">log in again</a>.";
   include("bottom.txt");
?>