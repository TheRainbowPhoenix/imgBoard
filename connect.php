<?php
define('ROOTPATH', './');
$filename = "Users.json";
$readable = @fopen($filename, 'r+');
if ($readable === null) {
    $readable = fopen($filename, 'w+');
	die("Database Error. Recreated.");
}
if ($readable) {
	$datas = file_get_contents($filename);
}
if ($readable !== false) {
  fclose($readable);
}

function updateDB($tmpfile, $DBname="Users.json") {
  unlink($DBname);
  rename($tmpfile, $DBname);
    }

  function StoreLog($log,$logfile = "main.log",$logfolder = "./logs/")
  {
    if (! is_dir($logfolder)) @mkdir($logfolder);
    $lf = fopen($logfolder.$logfile, "a") or $lf = fopen($logfolder.$logfile, 'w+');
    fwrite($lf, "$log\n") or die("Cannot write file x.x");
    fclose($lf);
  }

  function CleanLog($logfile = "main.log",$logfolder = "./logs/")
  {
    $cf = fopen ($logfolder.$logfile, "w+");
    fclose($cf);
  }

?>
