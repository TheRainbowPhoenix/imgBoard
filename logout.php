<?php
require_once('session.php');
require_once('log.php');
$hmessage = $_SESSION['username']." logged out.";
HandleLog(0,$hmessage,"logout.php");
session_destroy();
if (! header("location: index.php")) {
	echo 'You have been logged out. <a href="'.ROOTPATH.'">Go back</a>';
}
die();
?>
