<?php

require_once('../../session.php');

$max_width = 150;
$max_height = 150;

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
   				<span class="form-signin-heading"><?php echo basename(__DIR__); ?> Board</span>
   			</div>
   			<div class="card-content">
   				<?php getPicts("."); ?>
   			</div>
   			<div class="card-footer">
   				<div class="card-actions">
   					<a class="btn btn-lg btn-primary btn-block" href="register.php">Register</a>
   					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Login</button>
   				</div>
   			</div>
   	</div>

   	</div>
 </body>
 </html>
