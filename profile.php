<?php
require_once('session.php');
require_once('board.php');

if((isset($_SESSION['username']))){
  $lusername = $_SESSION['username'];
}
else {
  //$imsg = "Welcome there anonymous !";
  $lusername = 'public';
}

$bfiles = glob('./Boards/*/board.json');


?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>imgBoard - <?php echo $lusername?> profile</title>

  </head>
  <body>
  <?php include ("nav.php"); ?>
	<section id="intro" class="intro-section">
	   <div class="large-container">

		<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
		<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
		<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
		<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

		<div class="card midgrey">
				<div class="card-title">
			  <li class="list-item" style="width: 100%;">
				<div class="list-content">
				  <a class="list-item-title" id="boardname">I'm <?php echo $lusername; ?> !</a>
				  <p class="list-item-desc" id="boarddesc">
					<?php if ($lusername=='public') { ?>
						A public account, used by non-registered peoples...
					<?php } else { ?>
						 <?php echo $lusername; ?> is a registered user on imgBoard.
					<?php } ?>
				  </p>
				</div>
			  </li>
				</div>
				<div class="card-content">
					<div class="input-group">
					<span class="input-info">I own the following boards :</span>
						<ul id="usrboard" class="ul-box">
						<?php 
							foreach ($bfiles as $brd) {
     						   $bname = explode("/", $brd)[2];
								$bthumb = "public/thumbs/sanic.png";
								if(file_exists("./Boards/".$bname."/board.json")) {
										$bdatas = file_get_contents("./Boards/".$bname."/board.json");
      									$bdatabase = json_decode($bdatas,true);
										foreach ($bdatabase as $bit => $ent) {
   									     if((strpos($bit, ".") !== false)) { //if there's a dot in it, so if it's a file
          									$bthumb = $bname."/thumbs/".$bit;
											}
										}
									}
  						      if(IsOwner('./Boards/'.$bname.'/',$lusername)) {
       					   echo '<li class="li-box">';
       					   echo '<a href="'.ROOTPATH.'Boards/'.$bname.'/"><img src="./Boards/'.$bthumb.'" alt=""></a><span class="bname">'.$bname.'</span>';
   						       echo '</li>';
      						  }
     						 }
						
						?>
						</ul>
					</div>
				</div>
		</div>
		</div>
    </section>
  </body>
</html>
