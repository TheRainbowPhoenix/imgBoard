<?php

//session_start();
require_once('session.php');

?>

<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Search</title>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" ></script>
  <script type="text/javascript" src="https://rawgithub.com/silviomoreto/bootstrap-select/master/dist/js/bootstrap-select.js"></script> -->

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
		$upath = "/mini/Boards/";


			echo '<div><h1>Results for '.$user.'</h1></div>';

		echo '<ul class="full-list">';

	foreach ($database as $it) {
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
