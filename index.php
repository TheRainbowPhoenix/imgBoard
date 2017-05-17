<?php
if (file_exists('./set.php')) header("location: welcome.php");

require_once('session.php');

if((isset($_SESSION['username']))){
  $lusername = $_SESSION['username'];
}
else {
  //$imsg = "Welcome there anonymous !";
  $lusername = 'public';
}

function trendGen($pcount=1, $tag=null) {
	global $datas;
	if ($tag==null) $tag = "cat";
	$upath = ROOTPATH."Boards/";
	$database = json_decode($datas,true);
	echo '<span class="input-info">Posts tagged <span class="tag">'.$tag.'</span></span><ul id="usrboard" class="Boards">';
	foreach ($database as $it) {
		// if user board is json-formated, search into page tags-name-desc :
		if(file_exists("./Boards/".$it["name"]."/board.json")) {
		  $banme = $it["name"];
		  $bdatas = file_get_contents("./Boards/".$it["name"]."/board.json");
		  $bdatabase = json_decode($bdatas,true);
		  foreach ($bdatabase as $bit => $ent) {
			if($pcount <= 0) break;
			if((strpos($bit, ".") !== false)) { //if there's a dot in it, so if it's a file
			  if(in_array($tag,$bdatabase[$bit][2])) {
				echo '<li><a href="'.$upath.$banme.'/view.php?img='.$bit.'"><img src="'.$upath.$banme.'/thumbs/'.$bit.'" alt=""></a></li>';
				$pcount--;
			  }
			}
		  }

		}
	}
	echo '</ul>';
}

function randomGen($ucount=1, $dcount=1){
	global $datas;
	$userscount = 0;
	$postcount = 0;
	$userslist = array();
	$postslist = array();
	$database = json_decode($datas,true);
	$dmemcount = $dcount;
	$visited = array();

	foreach ($database as $it) {
		if($it["name"] != null && $it["name"] != "" ) {
		  array_push($userslist, $it["name"]);
		  $userscount++;
		}
	}
	
	if ($userscount < $ucount){
		echo "<p>Wow it's empty over here :/</p>";
		return 1;
	} 

	while($ucount > 0) {
		if ($dcount == 0) $ucount--;
		$dcount = $dmemcount;
		$postcount = 0;
		$postslist = array();
		$randuser = $userslist[rand( 0 , $userscount-1 )];
		$randompage = "./Boards/".$randuser."/index.php";
		$lim = 0;

		while (!(file_exists("./Boards/".$randuser."/board.json")) && $lim<$userscount) {
			$randuser = $userslist[rand( 0 , $userscount-1 )];
			$lim++;
		}
		$file = null;
		while ($postcount <= 3 && !file_exists($file)) {
			$randuser = $userslist[rand( 0 , $userscount-1 )];
			$postcount = 0;
			$postslist = array();
			if(file_exists("./Boards/".$randuser."/board.json")) {
				$banme = $randuser;
				$bdatas = file_get_contents("./Boards/".$randuser."/board.json");
				$bdatabase = json_decode($bdatas,true);
				$postcount = 0;

				foreach ($bdatabase as $bit => $ent) {
				  if((strpos($bit, ".") !== false)) { //if there's a dot in it, so if it's a file
					$postcount++;
					array_push($postslist, $bit);
				  }
				}
				
				if ($postcount>= 3 && ! in_array($randuser,$visited)) {
					array_push($visited,$randuser);
					if ($dcount > 0) echo '<span class="input-info">Some randoms posts from <a href="./Boards/'.$randuser.'/">'.$randuser.'</a> </span><ul id="usrboard" class="Boards">';

					while ($dcount > 0) {
						if ($postcount >= 1) {
							$randpost = $postslist[rand( 0 , $postcount-1 )];
							$randompost = "./Boards/".$randuser."/view.php?img=".$randpost;
							$file = './Boards/'.$randuser.'/'.$randpost;
							echo '<li><a href="./Boards/'.$randuser.'/view.php?img='.$randpost.'"><img src="./Boards/'.$randuser.'/thumbs/'.$randpost.'" alt=""></a></li>';
							//echo '<img style="max-width:64px;max-heigth;64px" src="./Boards/'.$randuser.'/'.$randpost.'"/>';
						}
						$dcount--;
					}
					echo '</ul>';
				}
				//return $randompost;
			}
		}
		
	}
}


?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>imgBoard - Acceuil</title>
    <link rel="dns-prefetch" href="//127.0.0.1/">

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
				  <a class="list-item-title" id="boardname">Welcome back <?php echo $lusername; ?> !</a>
				  <p class="list-item-desc" id="boarddesc">
					<?php if ($lusername=='public') { ?>
						It looks like you're not logged in... What are you waiting for ? Join the fun ! Party is <a href="register.php" class="clink">over here.</a>
					<?php } else { ?>
						Hey ! You're back :D Glad to see you <?php echo $lusername; ?> ! Make an eggcellent post <a href="upload.php" class="clink">right now !</a> ??
					<?php } ?>
				  </p>
				  <a class="list-item-desc" id="boardtags">Or maybe you're on mood for discoveries ...</a>
				</div>
			  </li>
				</div>
				<div class="card-content">
					<div class="input-group">
						<?php randomGen(2,4); trendGen(8);?>
					</div>
				</div>
		</div>
		</div>
    </section>
  </body>
</html>
