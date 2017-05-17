<?php

//session_start();
require_once('session.php');

?>

<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Search</title>

  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />

  </head>
<body>
<?php include ("nav.php"); ?>
<div class="container">

	<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
	<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
	<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
	<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

	<?php

	if(isset($_GET['q']) && !empty($_GET['q'])) {
  $user = trim($_GET['q']);


		$database = json_decode($datas,true);
		$upath = ROOTPATH."Boards/";

		echo '<div><h1>Results for '.$user.'</h1></div>';

		echo '<ul class="full-list">';

	foreach ($database as $it) {
    // if user board is json-formated, search into page tags-name-desc :
    if(file_exists("./Boards/".$it["name"]."/board.json")) {
      $banme = $it["name"];
      $bdatas = file_get_contents("./Boards/".$it["name"]."/board.json");
      $bdatabase = json_decode($bdatas,true);
      // if string match to name / desc / tags
      if((strpos($bdatabase["boardname"], $user) !== false) || (strpos($bdatabase["boarddesc"], $user) !== false) || in_array($user,$bdatabase["boardtags"])) {
        echo '<li class="list-item"><div class="list-content">';
        echo '<a href="'.$upath.$it["name"].'/"class="list-item-title">'.$bdatabase["boardname"].'</a>';
        echo '<p class="list-item-desc">'.$bdatabase["boarddesc"].'</p>';
        echo '<a class="list-item-desc" id="boardtags">';
        foreach ($bdatabase["boardtags"] as $it => $tag) {
          echo '<span class="tag">'.$tag.'</span>';
        }
        echo '</a>';
        echo '</div></li>';
        $smsg = "user found !";
      }
      // deep search into posts
      foreach ($bdatabase as $bit => $ent) {
        if((strpos($bit, ".") !== false)) { //if there's a dot in it, so if it's a file
          //echo $bit;
          if((strpos($bdatabase[$bit][0], $user) !== false) || (strpos($bdatabase[$bit][1], $user) !== false) || in_array($user,$bdatabase[$bit][2])) {
            echo '<li class="list-item">';
            echo '<a href="'.$upath.$banme.'/view.php?img='.$bit.'"><img src="'.$upath.$banme.'/thumbs/'.$bit.'" class="minimg"></a>';
            echo '<div class="list-content">';
            echo '<a href="'.$upath.$banme.'/view.php?img='.$bit.'" class="list-item-title">'.$bdatabase[$bit][0].'</a>';
            echo '<p class="list-item-desc">'.$bdatabase[$bit][1].'</p>';
            echo '<a class="list-item-desc" id="boardtags">';
            foreach ($bdatabase[$bit][2] as $it => $tag) {
              echo '<span class="tag">'.$tag.'</span>';
            }
            echo '</a>';
            echo '</div></li>';
            $smsg = "user found !";
          }
          // echo $bdatabase[$bit][0]."<br/>";
          // echo $bdatabase[$bit][1]."<br/>";
          // foreach ($bdatabase[$bit][2] as $sbit => $sbent) {
          //   echo $sbent." ";
          //   // if((strpos($sbit, $user) !== false)) {
          //   //   echo $sbit;
          //   // }
          // }
        }
      }

    }
    // if board name match to the string :
		if(strpos($it["name"], $user) !== false) {

			echo '<li class="list-item"><div class="list-content">';
			echo '<a href="'.$upath.$it["name"].'/"class="list-item-title">page de '.$it["name"].'</a>';
			echo '<p class="list-item-desc">explorer la Board de '.$it["name"].'</p>';
			echo '</div></li>';

				$smsg = "user found !";
			//echo $it["name"]."<br/>";
			}


}

	echo '</ul>';

	}

	?>




	</div>
	</body>
	</html>
