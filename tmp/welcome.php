<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>imgBoard - Acceuil</title>
	<link rel="stylesheet" href="styles.css" >
	<link rel="stylesheet" href="css/Pho3-Flatty.css" >
	<link rel="stylesheet" href="css/Pho3-Flatty-Color-Scheme.css" >
	<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">

	<script src="js/base.js"></script>
  </head>
  <body>
  <?php include ("nav.php"); ?>
	<section id="intro" class="intro-section">
	   <div class="large-container">

		<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
		<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
		<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
		<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>

			<div class="header-content-inner">
				
			<?php if (file_exists('./set.php')) { ?> 
				<h1>Hohoho, it looks like the board isn't fixed yet ... Could you please be patient ? ;)</h1>
				</div>
				<div class="card midgrey botinfo">
				<div class="card-title">
				  <li class="list-item" style="width: 100%;">
					<div class="list-content">
					  <a class="list-item-title">Note to the webmaster</a>
					</div>
				</div>
				<div class="card-content">
					<p>If you can read me, it's because imgBoard have been successfully installed !</p>
					<p>There's one more step before going, please edit your <span class="tag">connect.php</span> file</p><br/>
					<p>Please remove this page after setup. Admin account default password is <span class="tag">admin</span></p>
					<p>You must change in on the admin panel (connect as admin and get it from the menu)</p>
				</div>
				<div class="card-footer">
					<div class="card-actions">
						<a class="right btn-main btn btn-primary btn-block" href="set.php">Use auto config</a>
					</div>
				</div>
			</div>
			<?php } else {?>
				<h1>An eggcellent way to share your pictures with the internet. Make your Board a part of yourself and share the fun !</h1>
					<a href="register.php" class="btn btn-primary btn-block btn-lg">Let's go!</a>
				</div>
			<?php } ?>
		</div>
    </section>
  </body>
  	<style>
		.collapse {
			display:none !important;
		}
		.sandwich-box.right {
			display:none !important;
		}
		.navbar-logo {
			margin-right: auto !important;
		}
		.sn-list  {
			display: none;
		}
		.large-container {
			height: calc( 100vh - 120px);
		}
		.header-content-inner {
			max-width: none;
			margin: 0;
			position: absolute;
			top: 40%;
			transform: translateY(-50%);
		}
		.btn {
			border: 2px solid #222a34;
		}
		.botinfo {
			bottom: 0;
			position: absolute;
		}
		.botinfo .btn {
			border: inherit;
		}
	</style>
</html>
