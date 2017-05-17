<?php
require_once ('connect.php');
require_once('log.php');

function IsOwner($path="./", $name) {
  $filename = $path."board.json";
  $readable = fopen($filename, 'r ') or fopen($filename, 'w+ ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);
  fclose($readable);

  foreach ($database["boardowners"] as $it => $user) {
      if ($name == $user)
      {
          return true;
      }
  }
  return false;
}

function GetPostDetails($path="./", $id=null) {
  $filename = $path."board.json";
  $readable = fopen($filename, 'r ') or fopen($filename, 'w+ ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);
  fclose($readable);

  if (@$database[$id] !== null) return $database[$id];

  return false;
}

function modifyBoard($path="./", $name = null, $desc = null, $tags= null, $uploaders = null,$owner = null) {
  $filename = $path."board.json";

  if (!@fopen($filename, 'r ')) {
    @fopen($filename, 'w+');
    $boardname = basename(__DIR__);
    $hmessage = $name." created the missing Board file on ".$boardname;
    HandleLog(3,$hmessage,"board.php","../../logs/");
  }
  $readable = fopen($filename, 'r ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);
  fclose($readable);

  if ($tags !== null) {
    $ntagslist = explode( ',', $tags);
    $database["boardtags"] = $ntagslist;
  }

  if ($name) $database["boardname"] = $name;
  if ($desc) $database["boarddesc"] = $desc;
  if ($owner) $database["boardowners"] = array($owner,"admin");

  $tmpfile = json_encode($database, JSON_PRETTY_PRINT);
  file_put_contents($filename, $tmpfile);

  $hmessage = $filename." have been updated";
  HandleLog(1,$hmessage,"board.php","../../logs/");

  return $database;
}

function modifyPost($path="./", $id, $name = null, $desc= null, $tags = null) {
  $filename = $path."board.json";

  if (!@fopen($filename, 'r ')) {
    @fopen($filename, 'w+');
    $boardname = basename(__DIR__);
    $hmessage = $username." created the missing post file ".$filename." on ".$boardname;
    HandleLog(2,$hmessage,"board.php","../../logs/");
  }
  $readable = fopen($filename, 'r ');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($filename);
  $database = json_decode($datas, true);
  fclose($readable);

    if ($name) $database[$id][0] = $name;
    if ($desc) $database[$id][1] = $desc;
    if ($tags !== null) {
      $ntagslist = explode( ',', $tags);
      $database[$id][2] = $ntagslist;

    $tmpfile = json_encode($database, JSON_PRETTY_PRINT);
    file_put_contents($filename, $tmpfile);

    $hmessage = $filename." have been updated from file ".$id;
    HandleLog(1,$hmessage,"board.php","../../logs/");

    return true;
  }

  return false;
}

function findPostComments($path="./",$id=null) {
  $commFiles = array();
  $folderFiles = array();
  $folderDir = opendir($path);
  while(($tmpfile = readdir($folderDir)) !== false) {                   // No dirs and temp files       // No thumbnails
    if(preg_match('#\.(jpe?g|png|gif).json$#i', $tmpfile)) {
        $commFiles[] = $tmpfile;
    }
  }
  return $commFiles;
}

function DisplayFileComm($path="./",$cfile=null,$IsOwner=false,$sscore = false) {
			if ((is_file($path.$cfile) && is_readable($path.$cfile))){
					$cfiledatas  = @file_get_contents($path.$cfile);

          if ($cfiledatas == "null" || $cfiledatas == null) {
             return false;
          }
          else {
            $comms = json_decode($cfiledatas,true);

            echo '<ul class="full-list">';

            foreach ($comms as $comm) {
                  if ($comm["name"] && $comm["message"]) {
                    echo '<li class="list-item"><div class="list-content" style="max-width: 80%;">';
                    echo '<a href="'.ROOTPATH.'Boards/'.$comm["name"].'/"class="list-item-title comttl">'.$comm["name"].'</a>';
                    echo '<p class="list-item-desc commsg">'.$comm["message"].'</p>';
                    echo '</div>';
                    if ($IsOwner){
                      echo '<a href="?cpath='.$path.'&cfile='.$cfile.'&rmCommName='.$comm["name"].'&rmCommmessage='.$comm["message"].'" title="Remove this stupid comment">';
                      echo '<svg id="removeComm" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a></span>';
                    }
                    if ($sscore) echo '<span class="right">score : '.$comm["score"].'</span>';
                    echo '</li>';
                }
                //removeFileComment("./Boards/t/","1491081102bc0fd5f.png","admin","So flat lol");
            }
            echo '</ul>';
          }

			}

}

//removeComment("149108095417afcf9.png.json","admin","yay");
//removeComment("149108095417afcf9.png.json","admin");
function removeFileComment($path="./",$id=null, $username=null, $text=null, $rmall=false) {
  $readable = @fopen($path.$id, 'r+');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents($path.$id);

if ($datas == null || $datas == "null" || $datas == "[]") return false;
  $database = json_decode($datas, true);

    if ($username !== null) {
      if ($text !== null) {
        $cleanedComm = array();
        foreach ($database as $it => $ent)
        {
            if (! ($username == $database[$it]["name"] && $text == $database[$it]["message"]) )
            {
              array_push($cleanedComm, $ent);
            }
        }
      } else {
        foreach ($database as $it => $ent)
        {
            if ($ent["name"] == $username)
            {
              @array_push($cleanedComm, $ent);
            }
        }
      }
    }
  fclose($readable);
  $tmpfile = json_encode($cleanedComm);

  if ($tmpfile != null) {
    file_put_contents($path.$id, $tmpfile);
  }

}

 ?>
