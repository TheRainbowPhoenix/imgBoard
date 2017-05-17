<?php
require_once ('connect.php');
require_once('ip.php');

//StoreLog($log,$logfile = "main.log",$logfolder = "./logs/");

function HandleLog($level = 0, $message = 'undefined',$origin = '',$path = "./logs/") {
  $formated = null;
  $user = 'anonymous';
  if(@$_SESSION['username']) {
    $user = $_SESSION['username'];
  }
  $myip = get_ip_address();
  $origin = $origin."-".$myip."@".$user;

  $time = date("d-m-Y, H:i:s");
  $sep = '|';
  $formated = $time.$sep.$level.$sep.$origin.$sep.$message;
  StoreLog($formated,"main.log",$path);
}

function ExportLog($logfile = "main.log",$logfolder = "logs/",$exportfile = "changeme", $exportfolder="export/") {
  $zip = new ZipArchive();

  $extra = 0;
  $timestmp = date_timestamp_get(date_create());

  if ($exportfile == "changeme") {
    $exportfile = "Log.exported-".$timestmp;
  }

  while (file_exists($exportfolder.$exportfile.'-'.$extra.'.zip'))
  {
    $extra++;
  }

  $filename = $exportfolder.$exportfile.'-'.$extra.'.zip';

  if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
      exit("Impossible d'ouvrir le fichier <$filename>\n");
  }

  //$zip->addFromString("testfilephp.txt" . time(), "#1 Ceci est une chaîne texte, ajoutée comme testfilephp.txt.\n");
  //$zip->addFromString("testfilephp2.txt" . time(), "#2 Ceci est une chaîne texte, ajoutée comme testfilephp2.txt.\n");
  $zip->addFile("logs/".$logfile);

  $hmessage = "A log archive have been created on ".$exportfolder." nammed ".$exportfile."-".$extra.".zip . Zip returned statut ". $zip->status." and compressed ". $zip->numFiles." file(s)";
  HandleLog(0,$hmessage,"log.php");
  $zip->close();
  return $filename;
}

function LPing() {
  echo "LooPing !";
}
//ExportLog();
//GenLog("lolilolilolilolilolilol");

?>
