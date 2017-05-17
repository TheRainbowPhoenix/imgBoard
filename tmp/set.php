<?php
$trootpath=dirname($_SERVER['PHP_SELF']);

if (! ($trootpath=='/' || $trootpath=='\\'))  $trootpath=$trootpath.'/';

$reading = fopen('connect.php', 'r');
$writing = fopen('connect.php.tmp', 'w');

$replaced = false;

while (!feof($reading)) {
  $line = fgets($reading);
  if (stristr($line,"define('ROOTPATH'")) {
    $line = "define('ROOTPATH', '".$trootpath."');\n";
    $replaced = true;
  }
  fputs($writing, $line);
}
fclose($reading); fclose($writing);
// might as well not overwrite the file if we didn't replace anything
if ($replaced) 
{
  rename('connect.php.tmp', 'connect.php');
} else {
  unlink('connect.php.tmp');
}

unlink('index.php');
unlink('welcome.php');
rename('index.php.new', 'index.php');
unlink('set.php');
header("location: login.php");
?>