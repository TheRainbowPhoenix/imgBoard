<?php
require_once('connect.php');
if(isset($_POST) & !empty($_POST)){
	#$username = mysqli_real_escape_string($connection, $_POST['username']);
	#$email = mysqli_real_escape_string($connection, $_POST['email']);
	$password = md5($_POST['password']);
	$cpassword = md5($_POST['confirmpassword']);
	echo "<br/>";
	echo $password;
	echo "<br/>";
	echo $cpassword;
	if ($password == $cpassword){
		$sql = "UPDATE `user` SET Password ='$password' WHERE username='$username'";
		$result = mysqli_query($connection, $sql);
		if($result){
			$smsg = "Password changed";
			header("location: login.php");
		}else{
			echo $result;
			$fmsg = "Error, please retry";
		}
	}else{
		$fmsg = "Passwords doesn't matches";
	}
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Test</title>
	<!-- Latest compiled and minified CSS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
			<h2 class="form-signin-heading">Reset Password</h2>
			<h4>You're about to reset the password of <?php echo $_SESSION['username']; ?></h4>
			
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
			
			<label for="confirmPassword" class="sr-only">Confirm password</label>
			<input type="password" name="confirmpassword" id="confirmPassword" class="form-control" placeholder="confirm Password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Reset</button>
			<a class="btn btn-lg btn-primary btn-block" href="index.php">Cancel</a>
		</form>
	</div>
  </body>
</html>