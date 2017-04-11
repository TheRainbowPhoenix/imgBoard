<?php

//session_start();
?>

<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Login</title>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" ></script>
  <script type="text/javascript" src="https://rawgithub.com/silviomoreto/bootstrap-select/master/dist/js/bootstrap-select.js"></script> -->

  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />

  </head>
<body>
   <?php include ("../../nav.php"); ?>

<div class="container">

	<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
	<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
	<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
	<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

	<div class="card midgrey">
		<form class="form-signin mt-10" method="POST">
			<div class="card-title">
				<span class="form-signin-heading">Please Login</span>
			</div>
			<div class="card-content">
				<div class="input-group">
					<span class="input-info" id="basic-addon1">Username :</span>
					<input type="text" name="username" class="textfield" required>
				</div>
				<label for="inputPassword" class="input-info sr-only">Password :</label>
				<input type="password" name="password" id="inputPassword" class="textfield" required>
			</div>
			<div class="card-footer">
				<div class="card-actions">
					<a class="btn btn-lg btn-primary btn-block" href="register.php">Register</a>
					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Login</button>
				</div>
			</div>
		</form>
	</div>

	</div>
	</body>
	</html>
