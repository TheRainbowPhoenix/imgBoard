<?php
	require_once('connect.php');
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#222a34">

<link rel="prefetch" href="index.php">
<link rel="dns-prefetch" href="//127.0.0.1/">

<link rel="stylesheet" href="/mini/styles.css" >
<link rel="stylesheet" href="/mini/css/Pho3-Flatty.css" >
<link rel="stylesheet" href="/mini/css/Pho3-Flatty-Color-Scheme.css" >
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">

<script src="/mini/js/base.js"></script>

<nav class="navbar navbar-flat bg-dark navbar-fixed-top bsn" role="navigation">
	<div class="nav-container">
		<button id="hamburger-btn" onclick="swtdrw();">
			<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"></path></svg>
		</button>
		<a class="navbar-logo" href="/mini/index.php">imgBoard</a>
		<div class="collapse">
			<ul class="navbar-list left">
				<li>
					<a class="page-scroll" href="/mini/upload.php">Upload</a>
				</li>
				<li>
					<a class="page-scroll" href="#files">Lien 2</a>
				</li>
				<li>
					<a class="page-scroll" href="#contact">Lien 3</a>
				</li>
			</ul>
			<ul class="navbar-list right">
				<?php if(isset($_SESSION['username'])){ ?>
				  <li class="dropdown"><a class="dropdown-toggle" role="button" onclick="usrdrp()">Welcome <?php echo $_SESSION['username']; ?> </a>
					<ul class="bg-dark-raised dropdown-menu">
					  <li><a href="/mini/Boards/<?php echo $_SESSION['username']; ?>/">Profile</a></li>
						<li><a href="#">Images</a></li>
						<?php if (strpos($_SESSION['username'], "admin")!== false) { ?>
						<li><a href="/mini/admin.php">Administration</a></li>
						<?php } ?>
					 <li role="separator" class="divider"></li>
					  <li class="dropdown-header">Account</li>
					  <li><a href="/mini/password_reset.php">Change Password</a></li>
					  <li><a href="/mini/logout.php">Sign Out</a></li>
					</ul>
				  </li>
				<?php }else{ ?>
				  <li><a href="/mini/login.php">Sign In</a></li>
				  <li><a href="/mini/register.php">Sign Up</a></li>
				<?php } ?>
            </ul>
		</div>
		<!-- /.navbar-collapse -->
		<div class="side-nav bg-dark-raised">
			<div id="logo" class="midgrey">
				<span class="logo-pp">
					<?php if(isset($_SESSION['username'])){ ?>
							<?php if (strpos($_SESSION['username'], "admin")!== false) { ?>
						<a href="/mini/admin.php"><span class="pp-letter rounded flat-shadow"><?php echo $_SESSION['username'][0]; ?></span></a>
						<?php } else {?>
						<a href="/mini/Boards/<?php echo $_SESSION['username']; ?>/">	<span class="pp-letter rounded flat-shadow"><?php echo $_SESSION['username'][0]; ?></span></a>
						<?php } ?>
						<?php }else{ ?>
						<a href="/mini/Boards/public/"><img class="profile-pict rounded" src="/mini/Boards/public/sanic.png" alt="Logo"></a>
					<?php } ?>
				</span>
				<div class="logo-actions">
					<ul class="lactions-container">
						<?php if(isset($_SESSION['username'])){ ?>
								<?php if (strpos($_SESSION['username'], "admin")!== false) { ?>
										<li class="laction"><a href="/mini/admin.php">Admin</a></li>
									<?php } else { ?>
									<li class="laction"><a href="/mini/Boards/<?php echo $_SESSION['username']; ?>/">Profile</a></li>
								<?php } ?>
								<li class="laction"><a href="/mini/logout.php">Sign Out</a></li>
						<?php }else{ ?>
									<li class="laction"><a href="/mini/login.php">Sign In</a></li>
									<li class="laction"><a href="/mini/register.php">Sign Up</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<ul class="sn-list left">
				<li class="sn-link"><a class="page-scroll" href="/mini/upload.php">Upload</a></li>
				<li class="sn-link"><a class="page-scroll" href="#files">Lien 2</a></li>
				<li class="sn-link"><a class="page-scroll" href="#contact">Lien 3</a></li>
			</ul>

		</div>
		<!-- /.mobile drawer -->
		<div class="sandwich-box right">
			<button class="sandwich" onclick="schdrw();">
				<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M10 4a6 6 0 0 0-6 6 6 6 0 0 0 6 6 6 6 0 0 0 3.473-1.113L18.586 20 20 18.586l-5.113-5.115A6 6 0 0 0 16 10a6 6 0 0 0-6-6zm0 2a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/></svg>
			</button>
		</div>
		<!-- Search -->
		<div class="sn-right side-nav bg-dark-raised">
			<div id="logo" class="midgrey">
			<form id="search-form" action="/mini/search.php" method="GET">
				<div class="search-header">
					<span class="input-info" id="basic-addon1">Search :</span>
					<input type="text" name="q" class="textfield" required="">
				</div>
				<div class="logo-actions">
					<ul class="lactions-container">
									<li class="laction"><input type="submit" value="Search"/></li>
									<!--li class="laction"><a href="/mini/register.php">Sign Up</a></li-->
					</ul>
				</div>
				</form>
			</div>
			<ul class="sn-list left">
				<li class="sn-link"><a class="page-scroll" href="/mini/upload.php">Upload</a></li>
				<li class="sn-link"><a class="page-scroll" href="#files">Lien 2</a></li>
				<li class="sn-link"><a class="page-scroll" href="#contact">Lien 3</a></li>
			</ul>

		</div>

	</div>
	<!-- /.container -->
</nav>
<script>
function resetStates() {
	var pbody = document.getElementsByClassName('bsn')[0];
	var overlay = document.getElementsByClassName("sn-mask-modal")[0];
	if (pbody.classList.contains("snt")) toggleClass(pbody, 'snt');
	if (pbody.classList.contains("snh")) toggleClass(pbody, 'snh');
	if (overlay.classList.contains("sn-open")) toggleClass(overlay, 'sn-open');
}

function usrdrp() {
	toggleClass(document.getElementsByClassName("dropdown")[0], 'open');
}
function swtdrw() {
	toggleClass(document.getElementsByClassName("bsn")[0], 'snt');
	toggleClass(document.getElementsByClassName("sn-mask-modal")[0], 'sn-open');
}
function schdrw() {
	toggleClass(document.getElementsByClassName("bsn")[0], 'snh');
	toggleClass(document.getElementsByClassName("sn-mask-modal")[0], 'sn-open');
}
</script>

<div class="sn-mask-modal" href="javascript:;" onClick="resetStates();"></div>
<!-- TODO : Mobile Menu -->
<!-- <div class="navbar-header page-scroll">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<a class="navbar-brand page-scroll" href="index.php">Site</a>
</div> -->
