<?php
	require_once('connect.php');
	require_once('session.php');
	session_start();

	$_SESSION['login_user']= $username;
?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header page-scroll">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand page-scroll" href="index.php">Site</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul class="nav navbar-nav">
				<!-- Hidden li included to remove active class from about link when scrolled up past about section -->
				<li>
					<a class="page-scroll" href="#">Lien 1</a>
				</li>
				<li>
					<a class="page-scroll" href="#files">Lien 2</a>
				</li>
				<li>
					<a class="page-scroll" href="#contact">Lien 3</a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if(isset($_SESSION['username'])){ ?>
				  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Welcome <?php echo $_SESSION['username']; ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
					  <li><a href="#">Do nothing</a></li>
					  <li role="separator" class="divider"></li>
					  <li class="dropdown-header">Account</li>
					  <li><a href="password_reset.php">Change Password</a></li>
					  <li><a href="logout.php">Sign Out</a></li>
					</ul>
				  </li>
				<?php }else{ ?>
				  <li><a href="./login.php">Sign In</a></li>
				  <li><a href="./register.php">Sign Up</a></li>
				<?php } ?>
            </ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>
