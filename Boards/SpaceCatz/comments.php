<?php

function addComment($id, $username="anonymousse", $text="missingno", $path="./")
{
  $return = false;

  // if (isPresent($filename, $username, $email)) return false;
 $struct = array("name"=> $username, "message"=> $text, "score"=> 0);
  if (!@fopen($path.$id.".json", 'r+')) {
    @fopen($id.".json", 'w+');
     $boardname = basename(__DIR__);
    $hmessage = $username." created the missing comment file on post id ".$id." (board of ".$boardname.")";
    HandleLog(2,$hmessage,"comments.php","../../logs/");
  }
  $readable = @fopen($path.$id.".json", 'r+');
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
  //
  //   @mkdir("./Boards/".$username);
  //
  $boardname = basename(__DIR__);

    $hmessage = $username." commented ".$text." on post id ".$id." (board of ".$boardname.")";
    HandleLog(0,$hmessage,"comments.php","../../logs/");
    return $return;
}

//removeComment("149108095417afcf9.png","admin","yay");
//removeComment("149108095417afcf9.png","admin");
function removeComment($id, $username=null, $text=null)
{
  $readable = @fopen("./".$id.".json", 'r+');

  if (!$readable) die("file error x_x");
  $datas = file_get_contents("./".$id.".json");

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
      file_put_contents($id.'.json', $tmpfile);
    }
}

function genComm($cfile) {
			if (!(is_file($cfile) && is_readable($cfile))){
    if ( @file_get_contents('http://'.$_SERVER['HTTP_HOST'].$cfile, 0, NULL, 0, 1)) {
					$cfiledatas  = @file_get_contents($cfile);
					$comms = json_decode($cfiledatas,true);

						//echo 'readed  !<br/>';

					foreach ($comms as $comm) {
								if ($comm["name"] && $comm["message"]) {
								echo '<div><span>'.$comm["score"].'</span><span>'.$comm["name"].'</span><p>'.$comm["message"].'</p></div>';
							}
					}
    }
      $wmsg = "No Img infos";
      $cfile = '';
  } else {
					$cfiledatas  = @file_get_contents($cfile);

          if ($cfiledatas == "null" || $cfiledatas == null) {
             return false;
          }
          else {
            $comms = json_decode($cfiledatas,true);
				$islogged=false;
				if (@$_SESSION['username']) $islogged=true;

            echo '<ul class="full-list">';

            foreach ($comms as $comm) {
                  if ($comm["name"] && $comm["message"]) {
                    echo '<li class="list-item"><div class="list-content" style="max-width: 80%;">';
                    echo '<a href="'.'/mini/Boards/'.$comm["name"].'/"class="list-item-title">'.$comm["name"].'</a>';
                    echo '<p class="list-item-desc">'.$comm["message"].'</p>';
                    echo '</div>';
                    echo '<div class="right" style="display: inline-flex;">
                    <span style="line-height: 2rem;">score : '.$comm["score"].'</span>';
						if ($islogged) {
							echo '<div class="multybtn">
                      				<a class="btn">up</a>
                   				   <a class="btn">dn</a>
                  			  </div>';
						}
                    echo '</div>';
                    echo '</li>';
                }
            }
            echo '</ul>';
          }

			}

}

function findComments($url="./") {
  $folderFiles = array();
  $folderDir = opendir($url);
  if (! $url) $url = '.';
  while(($tmpfile = readdir($folderDir)) !== false) {                   // No dirs and temp files       // No thumbnails
    if(preg_match('#\.(jpe?g|png|gif).json$#i', $tmpfile)) {
        $commFiles[] = $tmpfile;
    }
  }
  sort($commFiles);
  return $commFiles;
}

// echo 'a ';
//
// if(isset($_GET['id']) && !empty($_GET['id'])) {
//   	echo 'b ';
// 	$cfile = @trim($_GET['id']);
// 	echo $cfile;
// 	  // if ( @file_get_contents('http://'.$_SERVER['HTTP_HOST'].$cfile.'json', 0, NULL, 0, 1) || (is_file($cfile.'.json')))
// 			   if ((is_file($cfile.'.json')) && is_readable($cfile.'.json')) {
// 					echo 'readed  !<br/>';
// 					$cfiledatas  = file_get_contents($cfile.'.json');
// 					$comms = json_decode($cfiledatas,true);
//
// 					foreach ($comms as $comm) {
// 								if ($comm["name"] && $comm["message"]) {
// 								echo '<div><span>'.$comm["score"].'</span><span>'.$comm["name"].'</span><p>'.$comm["message"].'</p></div>';
// 							}
// 					}
//
// 					$commstrct = array("name" => "spammer", "message" => "a spam test", "score" => "0");
// 					//echo join('', file($cfile.'.json'));
// 		}
// 	}

//
// $comms = findComments("../../Boards/t/");
// foreach ($comms as $key => $value) {
//   echo $key." ".$value." ";
//   # code...
// }

?>
