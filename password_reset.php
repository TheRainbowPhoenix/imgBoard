<?php

//session_start();
require_once('session.php');

if(!isset($_SESSION['username'])){
	$wmsg = "Not logged in !";
	header("location: index.php");
}

if(isset($_POST) & !empty($_POST)){
	global $filename;
	$written = 0;
	$error = 0;
	$database = json_decode($datas,true);
	$username = $_POST['username'];
	$email = $_POST['email'];

	if ($_POST['password'] == $_POST['password2']) {
		$password = hash('sha256',$_POST['password']);
	}
	else {
		$fmsg = "Passwords doesn't matches X_x";
		$error = 1;
	}

	if (!$error) {
		foreach ($database as $it => $ent) {
      if($ent["name"]== $username) {
  			if($ent["mail"]== $email) {
          $database[$it]["psw"] = $password;
          $written = 1;
        }
  			else {
  				$fmsg = "Wrong password :/";
  			}
  		}
		}
    if ($written) {
      $timestmp = date_timestamp_get(date_create());
      $tmpfile = json_encode($database);
      file_put_contents('Users.tmp-'.$timestmp.'.json', $tmpfile);
      $smsg = "Asking for update ";
      updateDB('Users.tmp-'.$timestmp.'.json',$filename);
      header("location: logout.php");
    }
	}
}

?>

<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Reset password</title>

  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />

  <style>
  html,body {font-family: Quicksand;margin: 0 auto;}
  </style>

  </head>
<body>
<?php include ("nav.php"); ?>
<div class="container">

	<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
	<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
	<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
	<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

	<div class="card midgrey">
		<form class="form-signin mt-10" method="POST">
			<div class="card-title">
				<span class="form-signin-heading">Reset your password</span>
			</div>
			<div class="card-content">
				<div class="input-group">
					<span class="input-info" id="basic-addon1">Username :</span>
					<input type="text" name="username" class="textfield" required>
				</div>
				<div class="input-group">
					<span class="input-info" id="basic-addon1">Mail:</span>
					<input type="text" name="email" class="textfield" required>
				</div>
				<div class="input-group">
					<label for="inputPassword" class="input-info sr-only">New Password :</label>
					<input type="password" name="password" id="inputPassword" class="textfield" required>
					<input type="password" name="password2" id="inputPassword" class="textfield" required>
				</div>
			</div>
			<div class="card-footer">
				<div class="card-actions">
					<a class="btn btn-lg btn-primary btn-block" href="change_mail.php">change mail ?</a>
					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Change</button>
				</div>
			</div>
		</form>
	</div>

	</div>
	</body>
	</html>
