<?php

require_once('../../session.php');
require_once('../../log.php');
require_once('../../board.php');
$HaveRights = false;
$max_width = 150;
$max_height = 150;
$purl = basename(__DIR__)."/index.php";
$BoardUserName = explode('/',$_SERVER['REQUEST_URI'])[3];


if (!@fopen("board.json", 'r+')) {
  @fopen("board.json", 'w+');
  modifyBoard("./", basename(__DIR__), basename(__DIR__), basename(__DIR__), basename(__DIR__),basename(__DIR__));
}

$filename = file_get_contents("board.json");
$database = json_decode($filename, true);

$boardname = basename(__DIR__);
$boarddesc = $boardname."'s board";
$tags = "";
$rawtags = "";

if ($database["boardname"] !== null) $boardname = $database["boardname"];
if ($database["boarddesc"] !== null) $boarddesc = $database["boarddesc"];

foreach ($database["boardtags"] as $it => $tag) {
  $tags = $tags."<span class='tag'>".$tag."</span>";
  $rawtags = $rawtags.$tag.",";
}

if (@$_SESSION['username']) {
  if((IsOwner(null,$_SESSION['username'])) || ($_SESSION['username'] == "admin")) {
  		$hmessage = $_SESSION['username']." connected to ".basename(__DIR__)." board";
  	  HandleLog(0,$hmessage,$purl,"../../logs/");
      $HaveRights = true;
      $smsg = "Hey ! This is one of the boards you own !";
  }
}

if((isset($_GET['nboardname']) && !empty($_GET['nboardname'])) && (isset($_GET['nboarddesc']) && !empty($_GET['nboarddesc'])) && (isset($_GET['fboardtags']) && !empty($_GET['fboardtags']))) {
  $nboardname = htmlspecialchars(trim($_GET['nboardname']));
  $nboarddesc = htmlspecialchars(trim($_GET['nboarddesc']));
  $rawtags = htmlspecialchars(trim($_GET['fboardtags']));

  if ($HaveRights != true) {
    $fmsg = "no rights";

    $hmessage = "Someone tryed to edit the ".$boardname." without permission ( new name : ".$nboardname." News tags : ".$rawtags.")";
    HandleLog(3,$hmessage,$boardname."index.php","../../logs/");
  } else {
    $smsg = "Rights !";

    $hmessage = @$_SESSION['username']." edited the ".$boardname." ( new name : ".$nboardname." News tags : ".$rawtags.")";
    HandleLog(1,$hmessage,$boardname."index.php","../../logs/");

    modifyBoard(null,$nboardname,$nboarddesc,$rawtags);
   header('Location: index.php', true, 303);

    // $hmessage = "Log have been exported on ".$expfile;
    // HandleLog(1,$hmessage,"users.php","../../logs/");
  }
  // if ($nboardname) echo $nboardname;
  // if ($nboarddesc) echo $nboarddesc;
}

function getPictureType($ext) {
  if ( preg_match('/jpg|jpeg/i', $ext) ) {
    return 'jpg';
  } else if ( preg_match('/png/i', $ext) ) {
    return 'png';
  } else if ( preg_match('/gif/i', $ext) ) {
    return 'gif';
  } else {
    return '';
  }
}



function getPicts($path = ".") {
  global $max_width;
  global $max_height;
  if ( $handle = opendir($path) ) {
    $lightbox = rand();
    echo '<ul id="usrboard" class="Boards">';
    while ( ($file = readdir($handle)) !== false ) {
      if ( !is_dir($file) ) {
        $split = explode('.', $file);
        $ext = $split[count($split) - 1];
        if ( ($type = getPictureType($ext)) == '' ) {
          continue;
        }
        if ( ! is_dir('thumbs') ) {
          mkdir('thumbs');
        }
        if ( ! file_exists('thumbs/'.$file) ) {
            try{
              if ( $type == 'jpg' ) {
                $src = imagecreatefromjpeg($file);
              } else if ( $type == 'png' ) {
                $src = @imagecreatefrompng($file);
              } else if ( $type == 'gif' ) {
                $src = imagecreatefromgif($file);
              }
              if ( ($oldW = imagesx($src)) < ($oldH = imagesy($src)) ) {
                $newW = $oldW * ($max_width / $oldH);
                $newH = $max_height;
              } else {
                $newW = $max_width;
                $newH = $oldH * ($max_height / $oldW);
              }
              $new = imagecreatetruecolor($newW, $newH);
              imagecopyresampled($new, $src, 0, 0, 0, 0, $newW, $newH, $oldW, $oldH);
              if ( $type == 'jpg' ) {
                imagejpeg($new, 'thumbs/'.$file);
              } else if ( $type == 'png' ) {
                imagepng($new, 'thumbs/'.$file);
              } else if ( $type == 'gif' ) {
                imagegif($new, 'thumbs/'.$file);
              }
              imagedestroy($new);
              imagedestroy($src);
            }catch (Exception $e){ // failed to catch it
                echo $e->getMessage();
            }
        }
        echo '<li><a href="view.php?img='.$file.'" rel="lightbox['.$lightbox.']">';
        echo '<img src="thumbs/'.$file.'" alt="" />';
        echo '</a></li>';
      }
    }
    echo '</ul>';
  }
}

// if(isset($_GET['pic'])) {
//   $file = trim($_GET['pic']);
//   //--- Protect against hacker attacks ---
//   if(preg_match('#\.\.|/#', $file)) die("Illegal characters in path!");
//   //--- Check existence ---
//   if(!(is_file($file) && is_readable($file))) {
//     header('HTTP/1.0 404 Not Found');
//     print('Sorry, this picture was not found');
//     exit();
//   }
//   $CONTEXT['page'] = 'picture';
//   //--- Find our index ---
//   $index = array_search($file, $ayFiles);
//   if(!isset($index) || $index===false) die("Invalid picture $file");
//   $CONTEXT['current'] = $index+1;
//   //--- Get neighbour pictures ---
//   $CONTEXT['first']   = $ayFiles[0];
//   $CONTEXT['last']    = $ayFiles[count($ayFiles)-1];
//   if($index>0)
//     $CONTEXT['prev']  = $ayFiles[$index-1];
//   if($index<count($ayFiles)-1)
//     $CONTEXT['next']  = $ayFiles[$index+1];
//   //--- Assemble the content ---
//   list($pWidth,$pHeight) = getimagesize($file);
//   $page = sprintf(
//     '<img class="picimg" src="%s" width="%s" height="%s" alt="#%s" border="0" />',
//     htmlspecialchars($file),
//     htmlspecialchars($pWidth),
//     htmlspecialchars($pHeight),
//     htmlspecialchars($index+1)
//   );
//   if(isset($CONTEXT['next'])) {
//     $page = sprintf('<a href="index.php?pic=%s">%s</a>', htmlspecialchars($CONTEXT['next']), $page);
//   }
//   $CONTEXT['pictag'] = $page;
//   if(is_file($file.'.txt') && is_readable($file.'.txt')) {
//     $CONTEXT['caption'] = join('', file($file.'.txt'));
//   }
// }


 ?>
 <!DOCTYPE html>
 <html>
   <head>
    <meta charset="UTF-8">
  	<meta name="theme-color" content="#222a34">
  	<meta name="theme-color" content="#4285f4">
  	<meta name="msapplication-navbutton-color" content="#4285f4">
  	<meta name="apple-mobile-web-app-status-bar-style" content="#4285f4">

    <title>imgBoard - Boards</title>

    <link rel="stylesheet" href="../../styles.css" >
    <link rel="stylesheet" href="../../css/Pho3-Flatty.css" >
    <link rel="stylesheet" href="../../css/Pho3-Flatty-Color-Scheme.css" >

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
          <li class="list-item" style="width: 100%;">
            <div class="list-content">
              <a class="list-item-title" id="boardname"><?php echo $boardname; ?></a>
              <p class="list-item-desc" id="boarddesc"><?php echo $boarddesc; ?></p>
              <a class="list-item-desc" id="boardtags"><?php echo $tags; ?></a>
            </div>
            <?php if ($HaveRights === true) {
              echo '<span class="right" onclick="editBoard();" title="Edit my infos"><svg id="editUser" viewBox="0 0 24 24"><path class="editpen" fill="rgba(210,210,210,.9)" d="M16.95 2.8l-.2.2-.5.52-2.13 2.12-.7.7L3 16.76V21h4.24l1-1 9.42-9.4.7-.72 2.12-2.12.52-.52.2-.2-.2-.18-.52-.52-2.82-2.82-.52-.52-.2-.2zm0 1.42l2.83 2.83-2.12 2.12-2.83-2.83 2.12-2.12zm-2.83 2.83l2.83 2.83L9 17.83V17H7v-2h-.83l7.95-7.95zM5.17 16H6v2h2v.83L6.83 20H5.15L4 18.85v-1.68L5.17 16z"></path></svg></span>';
            }
               ?>
          </li>
   			</div>
   			<div class="card-content">
   				<?php getPicts("."); ?>
   			</div>
   	</div>
 	</div>
<?php if ($HaveRights === true) {
echo '<div id="EditBoardModal" class="modal">
  	<form action="index.php" class="container">
  		<div class="card midgrey">
  			<div class="card-title">
  				<span class="form-signin-heading">Edit this board</span>
  				<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
  					<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
  				</span>
  			</div>
  			<div class="card-content">
  				<div class="input-group">
  					<span class="input-info" id="basic-addon1">Board name :</span>
  					<input type="text" name="nboardname" class="textfield" autofocus="autofocus" value="'.$boardname.'"required="required">
  				</div>
  				<div class="input-group">
  					<span class="input-info" id="basic-addon1">Board description:</span>
            <textarea name="nboarddesc" class="textfield" cols="40" rows="5" required="required">'.$boarddesc.'</textarea>
  				</div>
          <div class="input-group">
  					<span class="input-info" id="basic-addon1">Board tags:</span>
            <input type="text" name="fboardtags" id="hiddenboardtags" class="textfield" value="'.$rawtags.'" oninput="updateTags(this);" onblur="updateTags(this);"required="required" maxlength="60"/>
            <ul name="fboardtags" id="dispboardtags" cols="40" rows="2" required="required">**changeme**</ul>
  				</div>
  			</div>

  			<div class="card-footer">
  				<div class="card-actions">
  					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/>
  					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Update Board</button>
  				</div>
  			</div>
  	</div>
  	</form>
  </div>';

  echo "<script>
  function editBoard() {
    updateTags();
  	document.getElementById('EditBoardModal').style.display = 'block';
  }
  </script>";

}

 ?>
<?php if ($HaveRights === true) {
  echo '
<script>
function updateTags(elem=null) {
  if (elem !== null) {
    GenTagList(elem.value);
  } else {
    var ltags = "";
    var i=0;
    var spantags = document.getElementById("boardtags").getElementsByTagName("span");
    var dtags = document.createElement("div");
    while(i<spantags.length-1) {
      ltags = ltags.concat(spantags[i].innerHTML, ",");
      i++;
    }
    ltags = ltags.concat(spantags[i].innerHTML);
    document.getElementById("hiddenboardtags").value=ltags;
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
  document.getElementById("dispboardtags").innerHTML = "";
  document.getElementById("dispboardtags").appendChild(dtags);
}
</script>'; } ?>

 </body>
 </html>
