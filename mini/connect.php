<?php
$filename = "Users.json";
$readable = @fopen($filename, 'r+');
if ($readable === null) {
    $readable = fopen($filename, 'w+');
	die("Database Error. Recreated.");
}
if ($readable) {
  //$writable =  fopen($filename, 'w');
	//echo "<span>Database opened.</span><br/>";

	$datas = file_get_contents($filename);

	/*$struct = array("name"=> "z", "mail"=> "z@mail.com", "psw"=> "z", "boards"=> "");

	fseek($readable, 0, SEEK_END);
	if (ftell($readable) > 0)
    {
        fseek($readable, -1, SEEK_END);

        fwrite($readable, ',', 1);

        fwrite($readable, json_encode($struct, JSON_PRETTY_PRINT) . ']');
    }
    else
    {
        fwrite($readable, json_encode(array($struct, JSON_PRETTY_PRINT)));
    }*/
}
if ($readable !== false) {
  fclose($readable);
}

function updateDB ($tmpfile, $DBname = "Users.json") {
  unlink("Users.json");
  rename($tmpfile, "Users.json");
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
