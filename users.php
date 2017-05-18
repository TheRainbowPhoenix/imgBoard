<?php
require_once ('connect.php');
require_once('log.php');

function isPresent($filename, $username = null, $email = null)
{
    $readable = @fopen($filename, 'r ');

    if (!$readable) die("file error x_x");
    $datas = file_get_contents($filename);
    $database = json_decode($datas, true);

    foreach ($database as $it)
    {

        if ($it["name"] == $username)
        {
            fclose($readable);
            return true;
        }

        if ($it["mail"] == $email)
        {
            fclose($readable);
            return true;
        }
    }
    fclose($readable);
    return false;
}
function addUser($filename, $username, $email, $password, $boards = null)
{
  $return = false;

  if (isPresent($filename, $username, $email)) return false;
  $struct = array("name"=> $username, "mail"=> $email, "psw"=> $password, "boards"=> "");
  $readable = @fopen("./".$filename, 'r+');
   if (!$readable) die("file error x_x");
  fseek($readable, 0, SEEK_END);
  if (ftell($readable) > 0)
    {
        fseek($readable, -1, SEEK_END);
        fwrite($readable, ',', 1);
        fwrite($readable, json_encode($struct, JSON_PRETTY_PRINT) . ']');
        $return = true;
    }
    else
    {
        fwrite($readable, json_encode(array($struct, JSON_PRETTY_PRINT)));
    }

    fclose($readable);

    @mkdir("./Boards/".$username);

	@copy('./base/comments.php', './Boards/'.$username.'/comments.php');
	@copy('./base/index.php', './Boards/'.$username.'/index.php');
	@copy('./base/view.php', './Boards/'.$username.'/view.php');

	modifyBoard("./Boards/".$username."/",$username,$username."'s board","",$username,$username);
	

    $hmessage = $username." have been created.";
    HandleLog(1,$hmessage,"users.php");

    return $return;
}

function ExtractUser($filename,$username) {
  $readable = @fopen($filename, 'r ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);

  foreach ($database as $it)
  {

      if ($it["name"] == $username)
      {
          fclose($readable);
          return $it;
      }
  }
  fclose($readable);

  return false;
}

function CleanQueue($path = "./", $mask = null, $skipfile = array())
{
  global $filename;
  $cnt = 0;
  if (!$mask) $mask='Users.tmp-*.json';
  $list = array();
  if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle)))
    {
        if (in_array($path.$file, glob($path.$mask)) && $file != $filename && ! in_array($path.$file, $skipfile))
        {
            unlink($path.$file);
            $cnt++;
        }
    }
    closedir($handle);
  }
  if ($cnt == 0) {
    return false;
    $hmessage = $path." have not been cleaned. The regex was".$mask;
    HandleLog(3,$hmessage,"users.php");
  } else {
    return true;
    $hmessage = $path." have been cleaned following".$mask." regex.";
    HandleLog(1,$hmessage,"users.php");
  }
}

function listQueue($path = "./", $mask = null)
{
  if (!$mask) $mask='Users.tmp-*.json';
  $list = array();
  if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle)))
    {
        if (in_array($path.$file, glob($path.$mask)))
        {
            array_push($list,$file);
        }
    }
    closedir($handle);
    return $list;
  }
}

function QueueChanges($database, $doUpdate = false, $output = null, $ofolder = null)
{
  global $filename;
  $extra = 0;
  if ($output === null) $output = 'Users.tmp-';
  if ($ofolder !== null && ! is_dir($ofolder)) @mkdir("./".$ofolder);
  if ($ofolder === null) $ofolder = "./";
  $timestmp = date_timestamp_get(date_create());
  $tmpfile = json_encode($database);

  while (file_exists($ofolder.$output.$timestmp.'-'.$extra.'.json'))
  {
    $extra++;
  }
  file_put_contents($ofolder.$output.$timestmp.'-'.$extra.'.json', $tmpfile);
  // $smsg = "Asking for update ";
  if ($doUpdate){
    $hmessage = $ofolder.$output.$timestmp."-".$extra.".json was created for database update and will replace the current database.";
    HandleLog(1,$hmessage,"users.php");

    updateDB($ofolder.$output.$timestmp.'-'.$extra.'.json',$filename);
  }
  $hmessage = $ofolder.$output.$timestmp."-".$extra.".json was created for database update but the main database wasn't updated.";
  HandleLog(0,$hmessage,"users.php");
  return ($ofolder.$output.$timestmp.'-'.$extra.'.json');
}

function removeUser($filename, $username)
{
  $readable = @fopen($filename, 'r');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);

  if ($username == "admin") return $database;

    foreach ($database as $it => $ent)
    {
        if (in_array($username, $ent))
        {
          $hmessage = $database[$it]["name"]." will be white-filled";
          HandleLog(0,$hmessage,"users.php");

          $database[$it]["name"] = "";
          $database[$it]["mail"] = "";
          $database[$it]["psw"] = "";
          $database[$it]["boards"] = "";
          //TODO : how to delete ?
            //$database[$it] = "missingno";
            //unset($database[$key]);
        }
    }
    fclose($readable);
    return $database;
}

function cleanRmUser($username) {
	if ($username == "admin") return 1;
	array_map('unlink', glob("./Boards/".$username."/*.*"));
   array_map('unlink', glob("./Boards/".$username."/thumbs/*.*"));
   @rmdir("./Boards/".$username."/thumbs/");
	@rmdir("./Boards/".$username."/");
	return 0;
}

function modifyUser($filename, $origin, $username = null, $mail = null, $psw = null, $boards = null) {
  $readable = @fopen($filename, 'r ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);

    foreach ($database as $it => $ent)
    {
        if(in_array($origin, $database[$it]))
        {
            $hmessage = $database[$it]["name"]." have been updated";
            HandleLog(1,$hmessage,"users.php");

            if ($username) $database[$it]["name"] = $username;
            if ($mail) $database[$it]["mail"] = $mail;
            if ($psw) $database[$it]["psw"] = $psw;
            if ($boards) $database[$it]["boards"] = $boards;
        }
    }
    fclose($readable);
    return $database;
}

function CleanDB($filename, $doUpdate = false, $isroot = false)
{
  // function QueueChanges($database, $doUpdate = false, $output = null, $ofolder = null)
  $extra = 0;
  $output = $filename.'.Cleaned-';
  if ($isroot) {
    $ofolder = "./";
  } else {
    $ofolder = "./cleaned/";
  }


  $readable = @fopen($filename, 'r ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);

  $cleanedDB = array();

    foreach ($database as $it => $ent)
    {
        if (! $database[$it]["name"] == "")
        {
          array_push($cleanedDB, $ent);
          //array_splice($database, $it, sizeof($ent));
          // $database[$it]["name"] = "";
          // $database[$it]["mail"] = "";
          // $database[$it]["psw"] = "";
          // $database[$it]["boards"] = "";
          //TODO : how to delete ?
            //$database[$it] = "missingno";
            //unset($database[$key]);
        }
    }
    fclose($readable);

    $hmessage = $filename." have been cleaned and saved to ".$ofolder.$output;
    HandleLog(1,$hmessage,"users.php");

    return QueueChanges($cleanedDB, $doUpdate, $output, $ofolder);
}


function unlinkDB($file)
{
  if (file_exists($file)) {
    unlink($file);
    $hmessage = $file." have been deleted";
    HandleLog(2,$hmessage,"users.php");
    return true;
  }
  $hmessage = $file." cannot be deleted";
  HandleLog(3,$hmessage,"users.php");
  return false;
}

/*  ==============
      test
    ==============*/
    //echo CleanDB($filename);
//echo QueueChanges(ExtractUser($filename, "x"), false, "bla-", "export/");
//$pass = hash('sha256','xxx');
// echo $pass.' ';
// for ($i = 0; $i<34;$i++) {
//   echo QueueChanges(modifyUser($filename, "y", "yiff", "null", null, null)).'<br/>';
// }
//echo QueueChanges(modifyUser($filename, "x", "xxx", "xxx@mail.com", $pass, ["x","x","y"]),true).'<br/>';
// //echo QueueChanges(removeUser($filename, "pip"),false).'<br/>';
// echo addUser($filename, "pipp", "ppip@pip.com", "pipp");
// //$lfiles = listQueue("./a/");
// //$lfiles = listQueue();
// $lfiles = listQueue("./","*c*");
// foreach ($lfiles as $it) {
//   echo $it.', ';
// }
// CleanQueue();
// echo 'done';
?>
