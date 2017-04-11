<?php

session_start();
require_once('connect.php');
if(isset($_POST) & !empty($_POST)){
	$username = mysqli_real_escape_string($connection, $_POST['username']);
	$password = md5($_POST['password']);

	$sql = "SELECT * FROM `user` WHERE username='$username' and password='$password'";
	$result = mysqli_query($connection, $sql);
	$count = mysqli_num_rows($result);
	if($count == 1){
		$_SESSION['username'] = $username;
		header("location: index.php");
	}else{
		$fmsg = "invalid";
	}

}
if(isset($_SESSION['username'])){
	$smsg = "already logged in !";
	header("location: index.php");
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Test</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

	<link rel="stylesheet" href="styles.css" >

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  </head>
  <body>
	<?php include ("nav.php"); ?>
	<div class="container">
		<?php if(isset($smsg)){ ?><div class="alert alert-succes" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
		<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
		<form class="form-signin" method="POST">
			<h2 class="form-signin-heading">Please Login</h2>
			<div class="input-group">
			  <span class="input-group-addon" id="basic-addon1">@</span>
			  <input type="text" name="username" class="form-control" placeholder="Username" required>
			</div>
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
			<a class="btn btn-lg btn-primary btn-block" href="register.php">Register</a>
		</form>
	</div>
  </body>
</html>
