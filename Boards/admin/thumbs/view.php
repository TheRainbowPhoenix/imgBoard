<?php

require_once('../../session.php');
require_once('../../log.php');
require_once('../../board.php');
require_once('./comments.php');
require_once("../../events.php");

$is404 = true;
$HaveRights = false;
$islogged = false;
$purl = basename(__DIR__)."/view.php";
$plen = substr_count(ROOTPATH,"/");
$pit = $plen+1;
$likeit = false;

$prevv = null;
$nextt = null;

$BoardUserName = basename(dirname(__FILE__));

if (@$_SESSION['username']) {
  $islogged = true;
  $suname = $_SESSION['username'];
  if((IsOwner(null,$suname)) || ($suname == "admin")) {
  		$hmessage = $suname." connected to ".basename(__DIR__)." page";
  	  HandleLog(0,$hmessage,$purl,"../../logs/");
      $HaveRights = true;
      $smsg = "Hey ! This is one of the posts you own !";


      if(isset($_GET['DlImg']) && !empty($_GET['DlImg'])) {
        $DlImgName = @trim($_GET['DlImg']);
        if(isset($_GET['img']) && !empty($_GET['img'])) {
        	$cfile = @trim($_GET['img']);
        }
        if ( $DlImgName !== "true") {
          $hmessage = $cfile." cannot be removed";
          HandleLog(2,$hmessage,$purl,"../../logs/");

          $fmsg = $cfile." cannot be removed !";
        } else {
          if (! header("location: ./index.php")) {
          	echo 'This file does not exists anymore. <a href="./index.php">Go back</a>';
          }
          @unlink("./".$cfile);
          @unlink("./thumbs/".$cfile);
          die();
          //
          // $cleanfile = CleanDB($DlImgName.".json", true, true);
          //
          $hmessage = $cfile." have been removed";
          HandleLog(1,$hmessage,$purl,"../../logs/");

          $smsg = $cfile." have been removed.";
        }
      }


      if((isset($_GET['npostname']) && !empty($_GET['npostname'])) && (isset($_GET['npostdesc']) && !empty($_GET['npostdesc'])) && (isset($_GET['fposttags']) && !empty($_GET['fposttags'])) && (isset($_GET['img']) && !empty($_GET['img']))) {
        $npostname = htmlspecialchars(trim($_GET['npostname']));
        $npostdesc = htmlspecialchars(trim($_GET['npostdesc']));
        $rawtags = htmlspecialchars(trim($_GET['fposttags']));
        $id = trim($_GET['img']);

        $hmessage = "The post id ".$id." on the board of ".$BoardUserName." have been mofified. Now called ".$npostname." and tagged ".$rawtags;
        HandleLog(1,$hmessage,"view.php","../../logs/");

        $smsg = "Rights !";
        modifyPost(null,$id,$npostname,$npostdesc,$rawtags);

        //header('Location: view.php?img='.$id, true, 303);
      }



    } //Ends owning condition

    if(isset($_GET['pcomm']) && !empty($_GET['pcomm']) && (isset($_GET['img']) && !empty($_GET['img']))) {
      $Pcomm = @trim($_GET['pcomm']);
      $id = @trim($_GET['img']);
      addComment($id,$suname,$Pcomm);
      header('Location: view.php?img='.$id, true, 303);
    }

    //Ends Logged In condition
} else {
  $HaveRights = false;
    if((isset($_GET['npostname']) && !empty($_GET['npostname'])) && (isset($_GET['npostdesc']) && !empty($_GET['npostdesc'])) && (isset($_GET['fposttags']) && !empty($_GET['fposttags'])) && (isset($_GET['img']) && !empty($_GET['img']))) {
      $fsmg="no rights :/";
    }
}



$max_width = 150;
$max_height = 150;
$notfoundimg = ROOTPATH.'Boards/public/sanic.png';
$file = $notfoundimg;
$incurrentpath = 1;

function ls($url) {
  $folderFiles = array();
  $folderDir = opendir($url);
  if (! $url) $url = '.';
  while(($tmpfile = readdir($folderDir)) !== false) {
    if($tmpfile{0}=='.') continue;                     // No dirs and temp files
    if(substr($tmpfile,0,3) == 'th_') continue;        // No thumbnails
    if(preg_match('#\.(jpe?g|png|gif)$#i', $tmpfile)) {
      // if(is_file($url.$tmpfile) && is_readable($url.$tmpfile)) {
        $ayFiles[] = $tmpfile;
      // }
    }
  }
  sort($ayFiles);
  return $ayFiles;
}

function genList($FilesList, $limit = 0){
	if (@$FilesList[1]) {
		$plen = substr_count(ROOTPATH,"/");
		$pit = $plen+1;
		$BoardUserName = basename(dirname(__FILE__));
		echo '<div class="card-content"><div class="input-group"><span class="input-info">More from '.$BoardUserName.'</span>';
		echo '<ul id="usrboard" class="Boards">';
		if ($limit == 0) {
		  foreach ($FilesList as $key) {
			echo '<li><a href="view.php?img='.$key.'" ">';
			echo '<img src="thumbs/'.$key.'" alt="" />';
			echo '</a></li>';
		  }
		} else {
		  for ($i = 0; ($i <= $limit) && @$FilesList[$i]; $i++) {
			echo '<li><a href="view.php?img='.$FilesList[$i].'" ">';
			echo '<img src="thumbs/'.$FilesList[$i].'" alt="" />';
			echo '</a></li>';
		  }
		}
		echo '</ul></div></div>';
	}
}

if(isset($_GET['img']) && !empty($_GET['img'])) {
	$file = @trim($_GET['img']);
	$cfile = @trim($_GET['img']).'.json';
 	if (!(is_file($file) && is_readable($file))){
    // If file isn't in current folder
    if (! @file_get_contents('http://'.$_SERVER['HTTP_HOST'].$file, 0, NULL, 0, 1)) {
      // if url doesn't leads to a picture
      header('HTTP/1.0 404 Not Found');
      $fmsg = "Invalid image URL";
      $file = $notfoundimg;
      $is404 = true;
    } else {
      $is404 = false;
    }
    $incurrentpath = 0;
  } else {
    $is404 = false;
  }

  if (!$is404) {
    // TODO: If base url isn't /mini/ change a lot : [3] => [2] ;)

    if (! $incurrentpath) {
      $BoardUserName = basename(dirname(__FILE__));

      $ayFiles = ls('../public/');
      //echo "<br/>";

      genList($ayFiles);

	  		$prevv = $ayFiles[0];
			$nextt = $ayFiles[0];

    }
    else {
      $tmpfile = $file;
      $BoardUserName = explode('/',$_SERVER['REQUEST_URI'])[$pit];

      $ayFiles = ls('.');



      $CONTEXT['count'] = count($ayFiles);
      $CONTEXT['files'] =& $ayFiles;

      $CONTEXT['count'] = count($ayFiles);
      $CONTEXT['files'] =& $ayFiles;

      $index = array_search($file, $ayFiles);
      //if(!isset($index) || $index===false) die("Invalid picture $file");
		$curentt = $index+1;

		$firstt = $ayFiles[0];
		$lastt = $ayFiles[count($ayFiles)-1];

		if($index>0){
			$prevv = $ayFiles[$index-1];
		} else {
			$prevv = $lastt;
		}
		if($index<count($ayFiles)-1){
			$nextt = $ayFiles[$index+1];
		} else {
			$nextt = $ayFiles[0];
		}

      $firstt = $ayFiles[0];

    }
  }

}
else {
  $fmsg = "Invalid image";
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="UTF-8">
     <title>imgBoard - View post</title>

     <script src="<?php echo ROOTPATH; ?>js/base.js"></script>

   </head>
   <body>
   <?php include ("../../nav.php"); ?>
   <div class="large-container">

   	<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
   	<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
   	<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
   	<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

   	<div class="card midgrey">
   			<div class="card-title">

            <?php
            $rawtags = "";
            $PostDetails = GetPostDetails(null,$file);
              $tags = "";

              echo '
              <li class="list-item" style="width: 100%;">';

              if($is404) {
                echo '<div class="list-content"><span class="form-signin-heading">You\'ve gone too fast D:</span></div>';
              } else {

              if($PostDetails == false) {
                echo '<div class="list-content"><span class="form-signin-heading">'.$file." -by ".$BoardUserName.'</span></div>';
              } else {

                foreach ($PostDetails[2] as $it => $tag) {
                  $tags = $tags."<span class='tag'>".$tag."</span>";
                  $rawtags = $rawtags.$tag.",";
                }

              echo '<div class="list-content">
                  <a class="list-item-title" id="postname">'.$PostDetails[0].'</a>
                  <p class="list-item-desc" id="postdesc">'.$PostDetails[1].'</p>
                  <a class="list-item-desc" id="posttags">'.$tags.'</a>
                </div>';
                    }
              }
              if ($HaveRights === true && !($is404)) {
                  echo '<a href="?img='.$file.'&DlImg=true'.'" style="margin-right:32px" title="Remove this file">';
                  echo '<svg id="DeleteImage" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a>';
                  echo '<span onclick="editPost();" title="Edit my infos"><svg id="editUser" viewBox="0 0 24 24"><path class="editpen" fill="rgba(210,210,210,.9)" d="M16.95 2.8l-.2.2-.5.52-2.13 2.12-.7.7L3 16.76V21h4.24l1-1 9.42-9.4.7-.72 2.12-2.12.52-.52.2-.2-.2-.18-.52-.52-2.82-2.82-.52-.52-.2-.2zm0 1.42l2.83 2.83-2.12 2.12-2.83-2.83 2.12-2.12zm-2.83 2.83l2.83 2.83L9 17.83V17H7v-2h-.83l7.95-7.95zM5.17 16H6v2h2v.83L6.83 20H5.15L4 18.85v-1.68L5.17 16z"></path></svg></span>';
                }
              echo '</li>';


             ?>
   			</div>
   			<div class="card-content">
   				<?php
           echo '<img class="fullimg" src="'.$file.'" alt="'.$file.'" border="0" style="cursor: pointer;" onclick="LightBoxIt(this);" />';
          ?>
   			</div>
   			<div class="card-footer">
   				<div class="card-actions">
            <?php if (!$is404) {
              ?>
   					<a class="btn btn-lg btn-primary btn-block" href="view.php?img=<?php echo $prevv; ?>">Previous</a>
					<a class="btn btn-lg btn-primary btn-block" href="view.php?img=<?php echo $nextt; ?>">Next</a>

          <?php echo '<a href="?img='.$file.'&Like='.(($likeit) ? 'true' : 'false').'"
					class="right btn-second btn btn-lg btn-primary btn-block" title="Like this content">♥</a> '; ?>



            <?php } ?>
   					<a class="right btn-main btn btn-lg btn-primary btn-block" href="index.php">View Board</a>
   				</div>
   			</div>
        <?php if (!$is404) {
          genList($ayFiles,4);
        } ?>


        <?php if ($islogged == true && !($is404)) {
          echo '        <form action="view.php" class="card midgrey">
                    			<div class="card-content">
                    				<div class="input-group">
                    					<span class="input-info" id="basic-addon1">Your comment :</span>
                              <textarea name="pcomm" class="textfield" cols="40" rows="3" maxlength="160" required="required">An interesting post bro !</textarea>
                    				</div>
                    			</div>

                    			<div class="card-footer">
                    				<div class="card-actions">
                              <input type="hidden" name="img" value="'.$file.'">
                    					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block">
                    					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Say it !</button>
                    				</div>
                    			</div>
                    	</form>';

        } ?>


<?php if (!$is404) {
  genComm($cfile);
} ?>
   	</div>

   	</div>

    <div id="LigthBoxModal" class="modal" style="background-color: rgba(34, 42, 51,0.8);z-index:4243;">
    	<form class="large-container" style="width: 90%;">
        <span class="card-collapse" title="Discard changes" onclick="hidemodals()" style="position: fixed;top: 96px;right: 64px;overflow: hidden;">
								<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path></svg>
							</span>
        <div id="imgbox" onclick="hidemodals()">

        </div>
    	</form>
</div>

<script>
function LightBoxIt(elem) {
  console.log(elem);
  var img = elem.cloneNode(true);

  document.getElementById('imgbox').innerHTML = "";
  document.getElementById('imgbox').appendChild(img);
	document.getElementById('LigthBoxModal').style.display = "block";
}

</script>
<?php if ($HaveRights === true  && !($is404)) {
echo '<div id="EditPostModal" class="modal">
  	<form action="view.php" class="container">
  		<div class="card midgrey">
  			<div class="card-title">
  				<span class="form-signin-heading">Edit this post</span>
  				<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
  					<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
  				</span>
  			</div>
  			<div class="card-content">
  				<div class="input-group">
  					<span class="input-info" id="basic-addon1">Post name :</span>
  					<input type="text" name="npostname" class="textfield" autofocus="autofocus" value="';
            if($PostDetails === false) {
               echo "Something better than ".$file;
            } else {
              echo $PostDetails[0];
            }
            echo '"required="required">
  				</div>
  				<div class="input-group">
  					<span class="input-info" id="basic-addon1">Post description:</span>
            <textarea name="npostdesc" class="textfield" cols="40" rows="5" required="required">';
            if($PostDetails === false) {
               echo "Something Cool bro !";
            } else {
              echo $PostDetails[1];
            }

            echo'</textarea>
  				</div>
          <div class="input-group">
  					<span class="input-info" id="basic-addon1">Post tags:</span>
            <input type="text" name="fposttags" id="hiddenposttags" class="textfield" value="'.$rawtags.'" oninput="updateTags(this);" onblur="updateTags(this);"required="required" maxlength="60"/>
            <ul name="fposttags" id="dispposttags" cols="40" rows="2" required="required">**changeme**</ul>
  				</div>
          <input type="hidden" name="img" value="'.$file.'">
  			</div>

  			<div class="card-footer">
  				<div class="card-actions">
  					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/>
  					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Update Post</button>
  				</div>
  			</div>
  	</div>
  	</form>
  </div>';

  echo "<script>
  function editPost() {
    updateTags();
  	document.getElementById('EditPostModal').style.display = 'block';
  }
  </script>";

}

 ?>

 <?php if ($HaveRights == true  && !($is404)) {
   echo ' <script>
 function updateTags(elem=null) {
   if (elem !== null) {
     GenTagList(elem.value);
   } else {
     var ltags = "";
     var i=0;
     ';
     if(! $PostDetails == false) {
       echo'
     var spantags = document.getElementById("posttags").getElementsByTagName("span");
     while(i<spantags.length-1) {
       ltags = ltags.concat(spantags[i].innerHTML, ",");
       i++;
     }
     ltags = ltags.concat(spantags[i].innerHTML);';
      } else {
       echo 'ltags = "MyTag,";';
     }
     echo '
     var dtags = document.createElement("div");
     document.getElementById("hiddenposttags").value=ltags;
     GenTagList(ltags);
   }
 }

 function GenTagList(taglist) {
   var utaglist = taglist.split(",");
   var dtags = document.createElement("div");
   for (var tag in utaglist) {
     var stag = document.createElement("span");
     stag.classList.add("tag");
     stag.innerHTML = utaglist[tag];
     dtags.appendChild(stag);
   }
   document.getElementById("dispposttags").innerHTML = "";
   document.getElementById("dispposttags").appendChild(dtags);
 }
 </script>'; }?>
 </body>
 </html>
