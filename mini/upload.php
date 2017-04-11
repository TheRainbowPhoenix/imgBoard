<?php
require_once('session.php');
// Configuration
if((isset($_SESSION['username']))){
  $filedir = $_SESSION['username'];
}
else {
  $imsg = "Consider login to save pictures to your board.";
  $filedir = 'public';
  //header("location: index.php");
}


$title = 'imgBoard - Upload to '.$filedir;
$maxsize = 5242880; //max size in bytes
$allowedExts = array('png', 'jpg', 'jpeg', 'gif');
$allowedMime = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/gif');
$baseurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/Boards/'.$filedir;
?>
<?php
require_once('session.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php print $title; ?></title>

	<link rel="stylesheet" href="styles.css" >
  <link rel="stylesheet" href="css\Pho3-Flatty.css" >
  <link rel="stylesheet" href="css\Pho3-Flatty-Color-Scheme.css" >

  </head>
  <body>
  <?php include ("nav.php"); ?>
  <div class="container">

  	<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
  	<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
  	<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
  	<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

    <div id="upload">
      <form enctype="multipart/form-data" action="<?php print $_SERVER['PHP_SELF']; ?>" method="POST">
			<div class="input-group">    

  			<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
      		Choose a file to upload: <br />
      		<input size="62"  name="file" type="file" accept="image/*" />
   		   	<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit" value="Upload File"/>
		</div>
      </form>
      <script>
      document.on("dragover drop", function(e) {
          e.preventDefault();
      }).on("drop", function(e) {
          $("input[type='file']")
              .prop("files", e.originalEvent.dataTransfer.files)
              .closest("form")
                .submit();
      });
      </script>
    <div id="image">
    <a name="image">
  <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (! isset($_FILES['file']['name'])) {
        $fmsg = "bad extention";
        return;
      }
      $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

      if ((in_array($_FILES['file']['type'], $allowedMime))
      && (in_array(strtolower($ext), $allowedExts))
      && (@getimagesize($_FILES['file']['tmp_name']) !== false)
      && ($_FILES['file']['size'] <= $maxsize)) {
        $md5 = substr(md5_file($_FILES['file']['tmp_name']), 0, 7);
        $newname = time().$md5.'.'.$ext;
        move_uploaded_file($_FILES['file']['tmp_name'], 'Boards/'.$filedir.'/'.$newname);
        $baseurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/Boards/'.$filedir;
        $imgurl = 'http://'.$baseurl.'/'.$newname;
        print '<br />';
        print 'Your URL:<br />';
        print '<input type="text" value="'.$imgurl.'" ><br /><br />';
        print '<a href="'.$imgurl.'"><img src="'.$imgurl.'" /></a><br />';
        echo '/Boards/'.$filedir."/view.php?img=".$newname;
        $fileview = '/mini/Boards/'.$filedir."/view.php?img=".$newname;
        echo "<script language='javascript'>\n";
        echo "window.location.href='".$fileview."';";
        echo "</script>\n";;
        //if(! header( "Location: $fileview" )) echo 'redir('.$fileview.')';
      }

      else {
        $fmsg = "bad extention";
        echo '<div class="alert a-failed" role="alert">'.$fmsg.'</div>';
      }

    }
  ?>
    </div>
<div id="info">
      Max file size: 5mb <br/>
      Supported formats: png, jpg, gif <br/>
      Please don't upload anything illegal
      </div>
    </div>
  </div>

</body>
</html>
