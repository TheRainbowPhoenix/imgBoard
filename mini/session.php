<?php
   require_once('connect.php');
   session_start();
   if ($_SESSION) {
     $ses_username = $_SESSION['username'];
   }
?>
