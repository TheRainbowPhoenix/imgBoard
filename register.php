<?php

//session_start();
require_once('session.php');
require_once('board.php');
if(isset($_POST) & !empty($_POST)){
	$error = 0;
	$database = json_decode($datas,true);
	$username = $_POST['username'];
	$email = $_POST['email'];
	
	//TODO: Encrypt before send

	if ($_POST['password'] == $_POST['password2']) {
		$password = hash('sha256',$_POST['password']);
	}
	else {
		$fmsg = "Passwords doesn't matches X_x";
		$error = 1;
	}

	if (!$error) {
		foreach ($database as $it) {
			if($it["name"]== $username) {
					$fmsg = "Username already used :/";
					$error = 1;
				}
			if($it["mail"]== $email) {
					$fmsg = "Mail already used :/";
					$error = 1;
				}
		}
	}
	if (!$error) {
		$struct = array("name"=> $username, "mail"=> $email, "psw"=> $password, "boards"=> "");
		$readable = @fopen("./".$filename, 'r+');
		fseek($readable, 0, SEEK_END);
		if (ftell($readable) > 0)
			{
					fseek($readable, -1, SEEK_END);

					fwrite($readable, ',', 1);

					fwrite($readable, json_encode($struct, JSON_PRETTY_PRINT) . ']');
			}
			else
			{
					fwrite($readable, json_encode(array($struct, JSON_PRETTY_PRINT)));
			}

			fclose($readable);

			mkdir("./Boards/".$username);

			copy('./base/comments.php', './Boards/'.$username.'/comments.php');
			copy('./base/index.php', './Boards/'.$username.'/index.php');
			copy('./base/view.php', './Boards/'.$username.'/view.php');
			//modifyBoard($path="./", $name = null, $desc = null, $tags= null, $uploaders = null,$owner = null)
			
			modifyBoard("./Boards/".$username."/",$username,$username."'s board","",$username,$username);

			$smsg = "Welcome to imgBoard ".$username;
			$_SESSION['username'] = $username;
			$smsg = "Successfully logged in !";
			header("location: index.php");
	}
}

if(isset($_SESSION['username'])){
	$wmsg = "already logged in !";
	header("location: index.php");
}

?>

<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Register</title>

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
				<span class="form-signin-heading">Welcome !</span>
			</div>
			<div class="card-content">
				<div class="input-group">
					<span class="input-info" id="basic-addon1">Username :</span>
					<input type="text" name="username" class="textfield" autofocus="autofocus" required>
				</div>
				<div class="input-group">
					<span class="input-info" id="basic-addon1">Mail:</span>
					<input type="text" name="email" class="textfield" required>
				</div>
				<div class="input-group">
					<label for="inputPassword" class="input-info sr-only">Password :</label>
					<input type="password" name="password" id="inputPassword" class="textfield" required>
					<input type="password" name="password2" id="inputPassword" class="textfield" required>
				</div>
			</div>
			<div class="card-footer">
				<div class="card-actions">
					<a class="btn btn-lg btn-primary btn-block" href="login.php">Login ?</a>
					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Register</button>
				</div>
			</div>
		</form>
	</div>

	</div>
	</body>
	</html>
