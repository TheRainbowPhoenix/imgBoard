<?php
require_once('session.php');
require_once('board.php');
// Configuration
if((isset($_SESSION['username']))){
  $lusername = $_SESSION['username'];
}
else {
  $imsg = "Consider login to save pictures to your board.";
  $lusername = 'public';
  //header("location: index.php");
}
$sboard = 'public';
$filedir = $lusername;

if(isset($_GET['brd']) && !empty($_GET['brd']) && $lusername != 'public') {
  $filedir = @trim($_GET['brd']);
    $sboard = @trim($_GET['brd']);
	// $nusername = @trim($_GET['nusername']);
	// $nemail = @trim($_GET['nemail']);
	// $npassword = @trim($_GET['npassword']);
	// $nstatut = QueueChanges(modifyUser($filename, $sluser, $nusername, $nemail, $npassword, null), true);
	// $hmessage = "database updated from ".$nstatut." for ".$sluser." and updated";
	// HandleLog(1,$hmessage,"users.php");
	// $smsg = "database updated from ".$nstatut." for ".$sluser." and updated";
}



$bfiles = glob('./Boards/*/board.json');

$title = 'imgBoard - Upload to '.$filedir;
$maxsize = 5242880; //max size in bytes
$allowedExts = array('png', 'jpg', 'jpeg', 'gif');
$allowedMime = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/gif');
$baseurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/Boards/'.$filedir;
?>
<?php
require_once('log.php');
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
      <h4>Upload on <span class="flat-dropdown" id="brddrpdwn"><a class="dropdown-toggle" role="button" onclick="brddrp()"><?php echo $filedir;?></a>

        <?php
        echo '<ul id="ownblist" class="bg-light-raised dropdown-menu" >';
      foreach ($bfiles as $brd) {
        $bname = explode("/", $brd)[2];
        if(IsOwner('./Boards/'.$bname.'/',$lusername)) {
          echo '<li>';
          echo '<a href="upload.php?brd='.$bname.'"class="list-item-title">'.$bname.'</a>';
          echo '</li>';
        }
      }
	if ($lusername != 'public')  echo '<li><a href="upload.php?brd='.'public'.'"class="list-item-title">public</a></li>';
      echo "</ul>"; ?></span></h4>
      <form enctype="multipart/form-data" action="<?php print $_SERVER['PHP_SELF']; ?>" method="POST">
			<div class="input-group upload-center">
        <input type="hidden" name="BOARD_NAME" value="<?php print $filedir; ?>" />
  			<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
      		<input size="62" class="hidden" name="file" id="ufile" type="file" accept="image/*"/>
          <label for="ufile">
            <span class="rounded-btn btn-main">
              <svg style="width:96px;height:96px;" viewBox="0 0 24 24" id="uploadF">
                <path class="upbase" fill="rgba(230, 230, 230, 0.9)" d="M5 18v2h14v-2H5z"/>
                <path class="uptop" fill="rgba(240, 240, 240, 0.9)" d="M11.94 2.94L5.88 9H10v7h4V9h4l-3.94-3.94L12 3l-.06-.06z"/>
              </svg>
          </span>
          <span id="filename">Choose a picture</span>
         </label>
		</div>
    <button id="upbtn" class="right btn-second btn btn-lg btn-primary btn-block" style="display:none;" type="submit" value="Upload File">Upload</button>
      </form>
      <script>

      var inputs = document.querySelectorAll( 'input[type="file"].hidden' );
      Array.prototype.forEach.call( inputs, function( input )
      {
      	var label	 = input.nextElementSibling,
      		labelVal = label.innerHTML;

      	input.addEventListener( 'change', function( e )
      	{
      		var fileName = '';
      			fileName = e.target.value.split( '\\' ).pop();

      		if( fileName ) {
            document.getElementById('filename').innerHTML = fileName;
            document.getElementById('upbtn').style.display = "block";
          } else {
            label.innerHTML = labelVal;
            document.getElementById('upbtn').style.display = "none";
          }

      	});
      });

      document.addEventListener("paste", function(e) {
        e.stopPropagation();
        e.preventDefault();
        for (var i = 0 ; i < e.clipboardData.items.length ; i++) {
            var elem = e.clipboardData.items[i];
            console.log("Element type: " + elem.type);
            if (elem.type.indexOf("image") != -1) {
              var reader = new FileReader();

              console.log(elem);

              console.log(elem.getAsFile());

              //post('./upload.php', { BOARD_NAME: "public", MAX_FILE_SIZE: "5242880"}, 'post');

              //post(path, args, method)

              var blob = elem.getAsFile();

              //var pastefile = elem.getAsFile();

              elem.getAsString(function (s){
       e.target.appendChild(document.getElementById(s));
     });

              //document.querySelectorAll("input[type='file']").setNamedItem(pastefile);

              reader.onload = function(e2) {

                  // finished reading file data.
                  var img = document.createElement('img');
                  img.src= e2.target.result;
                  document.body.appendChild(img);
              }

              reader.readAsDataURL(elem.getAsFile()); // start reading the file data.
                //uploadFile(elem.getAsFile(elem));
            } else {
                alert("Discarding non-image paste data");
            }
        }
      });

      document.addEventListener("dragover", function(e) {
        e.stopPropagation();
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        console.log("dragover");
      });

      document.addEventListener('drop', function(e) {
        e.stopPropagation();
        e.preventDefault();
        var files = e.dataTransfer.files; // Array of all files



        for (var i=0, file; file=files[i]; i++) {
            if (file.type.match(/image.*/)) {

              //document.querySelectorAll("input[type='file']")[0].defineProperty(files[i], "file", e.dataTransfer.files[0]);

                var reader = new FileReader();

                reader.onload = function(e2) {

                    // finished reading file data.
                    var img = document.createElement('img');
                    img.src= e2.target.result;
                    document.body.appendChild(img);
                }

                reader.readAsDataURL(file); // start reading the file data.
            }
        }
    });

      // document.addEventListener("drop", function(e) {
      //     alert("drop !");
      //     document.querySelectorAll("input[type='file']")[0].defineProperty("files", e.originalEvent.dataTransfer.files).closest("form").submit();
      // });

      // document.on("dragover drop", function(e) {
      //     e.preventDefault();
      // }).on("drop", function(e) {
      //     $("input[type='file']")
      //         .prop("files", e.originalEvent.dataTransfer.files)
      //         .closest("form")
      //           .submit();
      // });
      </script>
    <div id="image">
    <a name="image">
  <?php
  echo "$filedir";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (! isset($_FILES['file']['name'])) {
        $fmsg = "bad extention";
        return;
      }
      $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

      if ((in_array($_FILES['file']['type'], $allowedMime))
      && (in_array(strtolower($ext), $allowedExts))
      && (@getimagesize($_FILES['file']['tmp_name']) !== false)
      && ($_FILES['file']['size'] <= $maxsize)
      && IsOwner("./Boards/".$_POST["BOARD_NAME"]."/",$lusername) || $_POST["BOARD_NAME"]=='public') {
        $md5 = substr(md5_file($_FILES['file']['tmp_name']), 0, 7);

        $newname = time().$md5.'.'.$ext;
        move_uploaded_file($_FILES['file']['tmp_name'], 'Boards/'.$_POST["BOARD_NAME"].'/'.$newname);

        $baseurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/Boards/'.$_POST["BOARD_NAME"];
        $imgurl = 'http://'.$baseurl.'/'.$newname;
        print '<br />';
        print 'Your URL:<br />';
        print '<input type="text" value="'.$imgurl.'" ><br /><br />';
        print '<a href="'.$imgurl.'"><img src="'.$imgurl.'" /></a><br />';
        echo '/Boards/'.$filedir."/view.php?img=".$newname;
        $fileview = ROOTPATH.'Boards/'.$_POST["BOARD_NAME"]."/view.php?img=".$newname;
        $hmessage = "File Uploaded by ".$lusername." : ".$fileview;
        HandleLog(1,$hmessage,"upload.php");
        echo "<script language='javascript'>\n";
        echo "window.location.href='".$fileview."';";
        echo "</script>\n";;
        if(! header( "Location: $fileview" )) echo 'redir('.$fileview.')';
      }

      else {
        $hmessage = $filedir." file was too big for upload.";
        HandleLog(2,$hmessage,"upload.php");
        $fmsg = "bad extention";
        echo '<div class="alert a-failed" role="alert">'.$fmsg.'</div>';
      }

    }
  ?>
    </div>
<div id="info">
      Max file size: 5mb <br/>
      Only png, jpg and gif <br/>
      </div>
    </div>
  </div>

</body>
</html>

<script>
function brddrp() {
	toggleClass(document.getElementById("brddrpdwn"), 'open');
}

</script>
