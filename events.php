<?php
function test_function(){
  $jsonRS = array();
	$opath = getcwd();
	//chdir(dirname(__FILE__));
    $furl = __DIR__.'/'."Boards/".$_SESSION['username']."/events.log";
	//echo file_get_contents($furl);
  $fevents = @file($furl);
  if (! $fevents) {
    return null;
  } else {
    foreach ($fevents as $it => $line) {
      $line_details = explode('|', $line);
      $jsonRS[$it]=array("text" => $line_details[1], "id" => $line_details[0]);
    }
  }
  $return=json_encode($jsonRS);
	//chdir($opath);
  return $return;
}

/*
  = types :
  0 : comment
  1 : like
  2 : upvote (to implement)
*/

function HandleEvent($type = 0, $message = 'undefined',$path = "Boards/") {
  $sep = '|';
  $formated = $type.$sep.$message;
  StoreEvent($formated,$path);
}

function StoreEvent($ev,$EventFolder = "Boards/")
{
  $EventFile = "events.log";
  //$opath = getcwd();
  //chdir(dirname(__FILE__));
  //chdir("http://".$_SERVER['SERVER_NAME'].ROOTPATH);
  //if (! is_dir($EventFolder)) return;
  //file_put_contents($EventFolder.$EventFile, "$ev\n", FILE_APPEND );
  //fopen(dirname(__FILE__) . '/../logs/mylog.log', "a");
  $lf = fopen(__DIR__.'/'.$EventFolder.$EventFile, "a") or $lf = fopen(__DIR__.ROOTPATH.$EventFolder.$EventFile, 'w+');
  fwrite($lf, "$ev\n") or die("Cannot write file x.x");
  fclose($lf);
  //chdir($opath);
}

function CleanEvent($EventFolder = "Boards/",$Euser = null)
{
  $EventFile = "events.log";
  //$opath = getcwd();
  if ($Euser == null) $Euser = $_SESSION['username'];
  //schdir(dirname(__FILE__));
  $cf = fopen (__DIR__.'/'.$EventFolder.$Euser."/".$EventFile, "w+");
  fclose($cf);
  //chdir($opath);
}

 ?>
