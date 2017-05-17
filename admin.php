<?php
require_once('session.php');
require_once('users.php');
require_once('log.php');
require_once('board.php');

if(strpos($_SESSION['username'], "admin")!== false) {
		//$hmessage = $_SESSION['username']." connected to Admin page";
	 // HandleLog(0,$hmessage,"admin.php");
			//$smsg = "allowed :)";
		} else {
			$hmessage = $_SESSION['username']." tried to access to admin page";
		  HandleLog(4,$hmessage,"users.php");
			// faut pas déconner avec les cons xD
			header("location: index.php");
			echo $_SESSION['username'];
			die();
		}

	if(isset($_GET['CLog']) && !empty($_GET['CLog'])) {
		$CLog = @trim($_GET['CLog']);
		if ($CLog != true) {
			$hmessage = "Someone tryed to clear logs";
			HandleLog(3,$hmessage,"users.php");
		} else {
			CleanLog();
			$hmessage = "Log was cleaned";
			HandleLog(1,$hmessage,"users.php");
		}
	}

	if(isset($_GET['ELog']) && !empty($_GET['ELog'])) {
		$ELog = @trim($_GET['ELog']);
		if ($ELog != true) {
			$hmessage = "Someone tryed to export logs";
			HandleLog(2,$hmessage,"users.php");
		} else {
			$expfile = ExportLog();
			$smsg = "Log exported to ".$expfile;
			$hmessage = "Log have been exported on ".$expfile;
			HandleLog(1,$hmessage,"users.php");
		}
	}


	if(isset($_GET['uexp']) && !empty($_GET['uexp'])) {
	  $uexp = @trim($_GET['uexp']);
		if ( isPresent($filename, $uexp)) {
			$path = QueueChanges(ExtractUser($filename, $uexp), false, $uexp."-", "export/");
			$smsg = "User ".$uexp." have been exported to ".$path;
			$hmessage = "User ".$uexp." have been exported to ".$path;
		  HandleLog(0,$hmessage,"users.php");
		} else {
			$fmsg = $uexp." cannot be exported";
			$hmessage = "User ".$uexp." cannot be exported";
		  HandleLog(2,$hmessage,"users.php");
		}
	}

	if(isset($_GET['ClDB']) && !empty($_GET['ClDB'])) {
	  $DbName = @trim($_GET['ClDB']);
		if ( $DbName !== null) {
			$cleanfile = CleanDB($DbName.".json", true, true);

			$hmessage = $DbName.".json have been cleaned to ".$cleanfile." and will replace current database";
		  HandleLog(1,$hmessage,"users.php");

			$smsg = $DbName.".json"." have been cleaned to ".$cleanfile;
		} else {
			$hmessage = $DbName.".json cannot be clean";
		  HandleLog(2,$hmessage,"users.php");

			$fmsg = $DbName.".json"." cannot be clean";
		}
	}

	if(isset($_GET['addusername']) && !empty($_GET['addusername']) && isset($_GET['addemail']) && !empty($_GET['addemail']) && isset($_GET['addpassword']) && !empty($_GET['addpassword'])) {
	  $addusername = @trim($_GET['addusername']);
		$addemail = @trim($_GET['addemail']);
		$addpassword = @trim($_GET['addpassword']);

		//addUser($filename, "zerzqer", "zerzqer", "zerzqer");

		if (addUser($filename, $addusername, $addemail,$addpassword)) {
			$hmessage = "User ".$addusername." have been created";
		  HandleLog(1,$hmessage,"users.php");
			$smsg = "User ".$addusername." have been created";
		} else {
			$hmessage = "User ".$addusername." cannot be created";
		  HandleLog(2,$hmessage,"users.php");
			$fmsg = $addusername." cannot be created";
		}
	}

	if(isset($_GET['rmuser']) && !empty($_GET['rmuser'])) {
	  $rmuser = @trim($_GET['rmuser']);
		if ($rmuser !== "admin") {
			$nstatut = QueueChanges(removeUser($filename, $rmuser), true);
			//echo $nstatut;
			$smsg = "User ".$rmuser." have been deleted";
			$hmessage = "User ".$rmuser." have been deleted";
		  HandleLog(1,$hmessage,"users.php");
		} else {
			// naméo faut pas déconner
			$hmessage = "Someone tryed to delete the admin account";
		  HandleLog(4,$hmessage,"users.php");
			$fmsg = $rmuser." cannot be deleted è_é";
		}
	}

	if(isset($_GET['cpath']) && !empty($_GET['cpath']) && isset($_GET['cfile']) && !empty($_GET['cfile']) && isset($_GET['rmCommName']) && !empty($_GET['rmCommName'])) {
	  $cpath = @trim($_GET['cpath']);
		$cfile = @trim($_GET['cfile']);
		$rmCommName = @trim($_GET['rmCommName']);
		$rmCommmessage = @trim($_GET['rmCommmessage']);
		removeFileComment($cpath,$cfile,$rmCommName,$rmCommmessage);
		header("location: admin.php");
		$smsg = "Comment(s) ".$rmCommmessage." from ".$rmCommName." have been deleted";
		$hmessage = $cpath.$cfile.' : Comment(s) "'.$rmCommmessage.'" from "'.$rmCommName.'" have been deleted';
		HandleLog(1,$hmessage,"admin.php");
		//echo $cpath.$cfile.' '.$rmCommName.' : '.$rmCommmessage;
	}

//removeFileComment("./Boards/t/","1491081102bc0fd5f.png","admin","So flat lol");

if(isset($_GET['u']) && !empty($_GET['u'])) {
  $sluser = @trim($_GET['u']);
	$nusername = @trim($_GET['nusername']);
	$nemail = @trim($_GET['nemail']);
	$npassword = @trim($_GET['npassword']);
	$nstatut = QueueChanges(modifyUser($filename, $sluser, $nusername, $nemail, $npassword, null), true);
	$hmessage = "database updated from ".$nstatut." for ".$sluser." and updated";
	HandleLog(1,$hmessage,"users.php");
	$smsg = "database updated from ".$nstatut." for ".$sluser." and updated";
}

if(isset($_GET['CQ']) && !empty($_GET['CQ'])) {
	$Qmask = null;
	//TODO : Whitelist
	$allowedrmdirs = array("./tmp/", "./export/", "./cleaned/", "./logs/");
  $toRm = @trim($_GET['CQ']);
	//TODO : moar regex
	if(isset($_GET['QMn'])) $Qmask = '*[0-9]*.json';
	if(isset($_GET['QMaj'])) $Qmask = '*.json';
	if(isset($_GET['QMaz'])) $Qmask = '*.zip';
	if(isset($_GET['QMa']) && $toRm != ("./") && in_array($toRm, $allowedrmdirs)) $Qmask = '*.*';
	if ($Qmask === null) $Qmask = 'Users.tmp-*.json';
	//echo $Qmask;
	//if(isset($_GET['Qmask']) && !empty($_GET['Qmask'])) $Qmask = @trim($_GET['Qmask']);
	if (CleanQueue($toRm,$Qmask)) {
		$hmessage = "Files in ".$toRm." matching with ".$Qmask." have been deleted";
		HandleLog(2,$hmessage,"users.php");
		//warning because you have to notice it
		$smsg = "Files in ".$toRm." matching with ".$Qmask." have been deleted";
	} else {
		$hmessage = "Files in ".$toRm." cannot be deleted, check if ".$Qmask." is valid";
		HandleLog(3,$hmessage,"users.php");
		$fmsg = "Files in ".$toRm." cannot be deleted, check if ".$Qmask." is valid";
	}
}

if(isset($_GET['removeDB']) && !empty($_GET['removeDB'])) {
  $toRm = @trim($_GET['removeDB']);
	if (unlinkDB($toRm)) {
		$hmessage = $toRm." have been deleted";
		HandleLog(1,$hmessage,"users.php");
		$smsg = $toRm." have been deleted";
	} else {
		$hmessage = $toRm." cannot be deleted, check if file exists";
		HandleLog(2,$hmessage,"users.php");
		$fmsg = $toRm." cannot be deleted, check if file exists";
	}
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>imgBoard - Administration</title>
    <link rel="prefetch" href="index.php">
    <link rel="dns-prefetch" href="//127.0.0.1/">

		<script src="<?php echo ROOTPATH; ?>js/base.js"></script>

		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  </head>
  <body>
  <?php include ("nav.php"); ?>
		<div class="large-container">
			<?php if(isset($smsg)){ ?><div class="alert a-success" role="alert"><?php echo $smsg; ?> </div> <?php } ?>
			<?php if(isset($fmsg)){ ?><div class="alert a-failed" role="alert"><?php echo $fmsg; ?> </div> <?php } ?>
			<?php if(isset($wmsg)){ ?><div class="alert a-warning" role="alert"><?php echo $wmsg; ?> </div> <?php } ?>
			<?php if(isset($imsg)){ ?><div class="alert a-info" role="alert"><?php echo $imsg; ?> </div> <?php } ?>
			<div class="row">
				<div class="col">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Users : </span>
							<span class="card-collapse" title="Make this card smaller">
								<svg id="DBMenuIndicator1" viewBox="0 0 24 24" onclick="swtcard(this);"><path fill="rgba(210,210,210,.9)" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg>
							</span>
						</div>
						<div class="card-content">
							<p id="userparser">
									<?php
									$string = file_get_contents($filename);
									$jsonRS = json_decode ($string,true);
									echo '<span class="input-info" id="basic-addon1">Filter :</span>';
									echo '<input type="text" name="ListFilter" id="ulistFilter" class="textfield" onkeyup="doFilter(';
									echo "'ulistFilter', 'ulist', false)";
									echo '" title="Input something to filter the following list">';
									echo '<ul id="ulist" class="full-list">';
									foreach ($jsonRS as $rs) {
										if (! $rs["name"] == "") {
											echo '<li class="list-item"><div class="list-content">';
											echo '<a href="'.ROOTPATH.'Boards/'.stripslashes($rs["name"]).'/"class="list-item-title">'.stripslashes($rs["name"]).'</a>';
											echo '<p class="list-item-desc">'.stripslashes($rs["mail"]).'</p>';
											echo '</div>';
											echo '<span onclick="editUser(';
											echo "'".stripslashes($rs["name"])."'";
											echo ');" title="Edit this user"><svg id="editUser" viewBox="0 0 24 24"><path class="editpen" fill="rgba(210,210,210,.9)" d="M16.95 2.8l-.2.2-.5.52-2.13 2.12-.7.7L3 16.76V21h4.24l1-1 9.42-9.4.7-.72 2.12-2.12.52-.52.2-.2-.2-.18-.52-.52-2.82-2.82-.52-.52-.2-.2zm0 1.42l2.83 2.83-2.12 2.12-2.83-2.83 2.12-2.12zm-2.83 2.83l2.83 2.83L9 17.83V17H7v-2h-.83l7.95-7.95zM5.17 16H6v2h2v.83L6.83 20H5.15L4 18.85v-1.68L5.17 16z"/></svg></span>';
											echo '</li>';
										}
									}
									echo "</ul>";

									?>
								</p>
							</div>
						</div>
						<!-- card 1 -->
				</div>
				<div class="col">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Databases : </span>
							<span class="card-collapse" title="Make this card smaller">
								<svg id="DBMenuIndicator2" viewBox="0 0 24 24" onclick="swtcard(this);"><path fill="rgba(210,210,210,.9)" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg>
							</span>
						</div>
						<div class="card-content">
							<p id="databasesparser">
								<?php
								// $pass = hash('sha256','xxx');
								// echo $pass.' ';
								// for ($i = 0; $i<50;$i++) {
								//   //echo QueueChanges(modifyUser($filename, "y", "yiff", "null", null, null)).'<br/>';
								// }
								// echo QueueChanges(modifyUser($filename, "x", "xxx", "xxx@mail.com", $pass, ["x","x","y"])).'<br/>';
								// //echo QueueChanges(removeUser($filename, "pip"),false).'<br/>';
								// addUser($filename, "pip", "pip@pip.com", "pip");
								// //$lfiles = listQueue("./a/");
								// //$lfiles = listQueue();
								// $lfiles = listQueue("./","*.json");
								// foreach ($lfiles as $it) {
								//   echo $it.', ';
								// }
								// CleanQueue();
								// echo 'done';
								?>
								<?php
									echo '<span> Current :</span>';
									echo '<li class="list-item"><div class="list-content">';
									echo '<a class="list-item-title" id="CurrentDBname">'.$filename.'</a>';
									echo '<p class="list-item-desc">'.(filesize($filename)/1024).' KB</p>';
									echo '</div>';
									echo '<span onclick="setupDB(';
									echo "'".$filename."'";
									echo ');"';
									echo '<span title="Change database options"><svg id="buildDB" viewBox="0 0 24 24"><path class="fixerkey" fill="rgba(210,210,210,.9)" d="M8.5 3a5.5 5.5 0 0 0-2.2.5L10 7 8.4 8.6 7 10 3.6 6.2A5.5 5.5 0 0 0 3 8.5 5.5 5.5 0 0 0 8.5 14a5.5 5.5 0 0 0 2.2-.5l7.7 7.7 2.8-2.8-7.7-7.7a5.5 5.5 0 0 0 .5-2.2A5.5 5.5 0 0 0 8.5 3z"/></svg></span>';
									echo '</li>';
									// echo '<span class="input-info" id="basic-addon1">Filter :</span>';
									// echo '<input type="text" name="ListFilter" id="ulistFilter" class="textfield" onkeyup="doFilter(';
									// echo "'ulistFilter', 'ulist')";
									// echo '">';
									/* list $path folder */
									$path = "./a/";
										if(is_dir($path)) {
										$databases = listQueue($path,"*.json");
										echo '<br/><span> '.$path.' folder :</span>';
										echo '<a class="list-title-action" href="?CQ='.$path.'&QMn" title="Clean temp files">';
										echo '<svg id="removeCQ" viewBox="0 0 24 24"><path class="folderRm" fill="rgba(210,210,210,.9)" d="M3 5v14h18V7H11L9 5H3zm1 1h4.6l1 1-3 3H4V6zm14.3 4l.7.8-2.3 2.2 2.3 2.3-.8.7-2.2-2.2-2.2 2.2-.8-.8 2.2-2.2-2.2-2.2.8-.8 2.2 2.3 2.3-2.3z"/></svg></a>';
										echo '<div class="list-content">';
										echo '</div>';
										echo '<ul id="ulist" class="full-list">';
										foreach ($databases as $dfile) {
											echo '<li class="list-item"><div class="list-content">';
											echo '<a class="list-item-title">'.$dfile.'</a>';
											echo '<p class="list-item-desc">'.(filesize($path.$dfile)/1024).' KB</p>';
											echo '</div>';
											echo '<span>';
											echo '<a href="?removeDB='.$path.$dfile.'" title="Remove this file">';
											echo '<svg id="removeDB" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a></span>';
											echo '</li>';
										}
										echo "</ul>";
									}
									/* list export folder*/
									$path = "./export/";
									if(is_dir($path)) {
										$databases = listQueue($path,"*.*");
										echo '<br/><span> '.$path.' folder :</span>';
										echo '<a class="list-title-action" href="?CQ='.$path.'&QMa" title="Clean temp files">';
										echo '<svg id="removeCQ" viewBox="0 0 24 24"><path class="folderRm" fill="rgba(210,210,210,.9)" d="M3 5v14h18V7H11L9 5H3zm1 1h4.6l1 1-3 3H4V6zm14.3 4l.7.8-2.3 2.2 2.3 2.3-.8.7-2.2-2.2-2.2 2.2-.8-.8 2.2-2.2-2.2-2.2.8-.8 2.2 2.3 2.3-2.3z"/></svg></a>';
										echo '<div class="list-content">';
										echo '</div>';
										echo '<ul id="ulist" class="full-list">';
										foreach ($databases as $dfile) {
											echo '<li class="list-item"><div class="list-content">';
											echo '<a href="'.$path.$dfile.'" class="list-item-title">'.$dfile.'</a>';
											echo '<p class="list-item-desc">'.(filesize($path.$dfile)/1024).' KB</p>';
											echo '</div>';
											echo '<span>';
											echo '<a href="?removeDB='.$path.$dfile.'" title="Remove this file">';
											echo '<svg id="removeDB" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a></span>';
											echo '</li>';
										}
										echo "</ul>";
									}
									/* list cleaned folder*/
									$path = "./cleaned/";
									if(is_dir($path)) {
										$databases = listQueue($path,"*.json");
										echo '<br/><span> '.$path.' folder :</span>';
										echo '<a class="list-title-action" href="?CQ='.$path.'&QMn" title="Clean temp files">';
										echo '<svg id="removeCQ" viewBox="0 0 24 24"><path class="folderRm" fill="rgba(210,210,210,.9)" d="M3 5v14h18V7H11L9 5H3zm1 1h4.6l1 1-3 3H4V6zm14.3 4l.7.8-2.3 2.2 2.3 2.3-.8.7-2.2-2.2-2.2 2.2-.8-.8 2.2-2.2-2.2-2.2.8-.8 2.2 2.3 2.3-2.3z"/></svg></a>';
										echo '<div class="list-content">';
										echo '</div>';
										echo '<ul id="ulist" class="full-list">';
										foreach ($databases as $dfile) {
											echo '<li class="list-item"><div class="list-content">';
											echo '<a class="list-item-title">'.$dfile.'</a>';
											echo '<p class="list-item-desc">'.(filesize($path.$dfile)/1024).' KB</p>';
											echo '</div>';
											echo '<span>';
											echo '<a href="?removeDB='.$path.$dfile.'" title="Remove this file">';
											echo '<svg id="removeDB" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a></span>';
											echo '</li>';
										}
										echo "</ul>";
									}

									/* list root folder */
									$path = "./";
									$databases = listQueue($path,"*.json");
									echo '<br/><span> '.$path.' folder :</span>';
									echo '<a class="list-title-action" href="?CQ='.$path.'" title="Clean temp files">';
									echo '<svg id="removeCQ" viewBox="0 0 24 24"><path class="folderRm" fill="rgba(210,210,210,.9)" d="M3 5v14h18V7H11L9 5H3zm1 1h4.6l1 1-3 3H4V6zm14.3 4l.7.8-2.3 2.2 2.3 2.3-.8.7-2.2-2.2-2.2 2.2-.8-.8 2.2-2.2-2.2-2.2.8-.8 2.2 2.3 2.3-2.3z"/></svg></a>';
									echo '<div class="list-content">';
									echo '</div>';
									echo '<ul id="ulist" class="full-list">';
									foreach ($databases as $dfile) {
										echo '<li class="list-item"><div class="list-content">';
										echo '<a class="list-item-title">'.$dfile.'</a>';
										echo '<p class="list-item-desc">'.(filesize($path.$dfile)/1024).' KB</p>';
										echo '</div>';
										echo '<span>';
										if ($dfile == $filename) {
											echo '<a title="This file is locked">';
											echo '<svg id="removeDB" viewBox="0 0 24 24"><path class="locktop" fill="rgb(230, 197, 197)" d="M12 3a4 4 0 0 0-4 4v4h1V7a3 3 0 0 1 3-3 3 3 0 0 1 3 3v4h1V7a4 4 0 0 0-4-4z"/><path class="lockbot" fill="rgb(230, 197, 197)" d="M6 10v11h12V10H6zm6 3a1.5 1.5 0 0 1 1.5 1.5 1.5 1.5 0 0 1-.8 1.3V18h-1.4v-2.2a1.5 1.5 0 0 1-.8-1.3A1.5 1.5 0 0 1 12 13z"/></svg></a></span>';
										} else {
											echo '<a href="?removeDB='.$path.$dfile.'" title="Remove this file">';
											echo '<svg id="removeDB" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></a></span>';
										}
										echo '</li>';
									}
									echo "</ul>";

									?>
								</p>
							</div>
						</div>
						<!-- card 2 -->
				</div>
		    <div class="col">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Boards : </span>
							<span class="card-collapse" title="Make this card smaller">
								<svg id="DBMenuIndicator3" viewBox="0 0 24 24" onclick="swtcard(this);"><path fill="rgba(210,210,210,.9)" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg>
							</span>
						</div>
						<div class="card-content">
							<p id="boardsparser">
									<?php
									$dirs = array_filter(glob('./Boards/*'), 'is_dir');

									//print_r( $dirs);
									echo '<span class="input-info" id="basic-addon1">Filter :</span>';
									echo '<input type="text" name="ListFilter" id="blistFilter" class="textfield" onkeyup="doFilter(';
									echo "'blistFilter', 'blist', false)";
									echo '" title="Input something to filter the following list">';
									echo '<ul id="blist" class="full-list">';
									foreach ($dirs as $rs) {
										$bname = explode("/", $rs);
										echo '<li class="list-item"><div class="list-content">';
										echo '<a href="'.ROOTPATH.$rs.'/"class="list-item-title">'.end($bname).'</a>';
										if (isPresent($filename, end($bname))) {
											echo '<p class="list-item-desc">Owned by '.'<a href="'.ROOTPATH.'Boards/'.end($bname).'/">'.end($bname).'</a></p>';
										} else {
											echo '<p class="list-item-desc">Unowned Board</p>';
										}

										echo '</div>';
										echo '<span title="Inspect this board" onclick="window.open(';
										echo ROOTPATH.$rs."/','_blank'";
										echo ')"><svg id="viewBoard" viewBox="0 0 24 24"><path class="eyetop" fill="rgba(210,210,210,.9)" d="M12 6a10 10 0 0 0-9.2 6 10 10 0 0 0 1 2A8.5 8.5 0 0 1 12 7.4a8.5 8.5 0 0 1 8.2 6.4 10 10 0 0 0 1-2A10 10 0 0 0 12 6z"/>';
										echo '<path class="eyecnt" fill="rgba(210,210,210,.9)" d="M12 8a4 4 0 0 0-4 4 4 4 0 0 0 4 4 4 4 0 0 0 4-4 4 4 0 0 0-4-4zm0 1a3 3 0 0 1 1.1.2 1.5 1.5 0 0 0-1.18 1.47 1.5 1.5 0 0 0 1.5 1.5 1.5 1.5 0 0 0 1.44-1.08 3 3 0 0 1 .14.9 3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1 3-3z"/></svg></span>';
										echo '</li>';
									}
									echo "</ul>";

									?>
								</p>
							</div>
						</div>
						<!-- card 3 -->
		    </div>
		  </div>

			<div class="row">
				<!-- Block 1 2nde line-->
				<div class="col">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Comments : </span>
							<span class="card-collapse" title="Make this card smaller">
								<svg id="DBMenuIndicator1" viewBox="0 0 24 24" onclick="swtcard(this);"><path fill="rgba(210,210,210,.9)" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg>
							</span>
						</div>
						<div class="card-content">
							<p id="commentsparser">
								<?php
								$bdirs = array_filter(glob('./Boards/*'), 'is_dir');
								$string = file_get_contents($filename);
								$jsonRS = json_decode ($string,true);
								echo '<ul id="clist" class="full-list">';
								?>
								<?php
								foreach ($bdirs as $rs) {
									$bname = explode("/", $rs);
									echo '<li class="list-item"><div class="list-content" style="width:100%;">';
									echo '<a href="'.ROOTPATH.$rs.'/"class="list-item-title">'.end($bname).'</a>';
									$commFiles=findPostComments("./".$rs."/");
									foreach ($commFiles as $cfile) {
										echo '<ul id="c'.$bname[1].'list" class="full-list">';
											echo '<li class="list-item"><div class="list-content" style="width:100%">';
											echo '<a href="'.ROOTPATH.$rs.'/view.php?img='.explode(".", $cfile)[0].'.'.explode(".", $cfile)[1].'"class="list-item-title">'.$cfile.'</a>';
											echo '<p class="list-item-desc">'.DisplayFileComm($rs."/",$cfile,true).'</p>';
											echo '</div>';
											// echo '<span title="Inspect this board" onclick="window.open(';
											// echo "'".ROOTPATH.$rs."/".$cfile."','_blank'";
											// echo ')"><svg id="viewBoard" viewBox="0 0 24 24"><path class="eyetop" fill="rgba(210,210,210,.9)" d="M12 6a10 10 0 0 0-9.2 6 10 10 0 0 0 1 2A8.5 8.5 0 0 1 12 7.4a8.5 8.5 0 0 1 8.2 6.4 10 10 0 0 0 1-2A10 10 0 0 0 12 6z"/>';
											// echo '<path class="eyecnt" fill="rgba(210,210,210,.9)" d="M12 8a4 4 0 0 0-4 4 4 4 0 0 0 4 4 4 4 0 0 0 4-4 4 4 0 0 0-4-4zm0 1a3 3 0 0 1 1.1.2 1.5 1.5 0 0 0-1.18 1.47 1.5 1.5 0 0 0 1.5 1.5 1.5 1.5 0 0 0 1.44-1.08 3 3 0 0 1 .14.9 3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1 3-3z"/></svg></span>';
											echo '</li>';
											echo "</ul>";
										}

									echo '</div>';
									echo '</li>';
								}
								echo "</ul>";

								?>
							</p>
						</div>
					</div>
				</div>

				<!-- Block 2 2nde line-->
				<div class="col">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Log : </span>
							<span class="card-collapse" title="Make this card smaller">
								<svg id="DBMenuIndicator1" viewBox="0 0 24 24" onclick="swtcard(this);"><path fill="rgba(210,210,210,.9)" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z"></path></svg>
							</span>
						</div>
						<div class="card-content">
							<p id="commentsparser">
								<?php
								$mainlog = file ('./logs/main.log');

								$string = file_get_contents($filename);
								$jsonRS = json_decode ($string,true);
								echo '<span class="input-info" id="basic-addon1">Filter :</span>';
								echo '<input type="text" name="ListFilter" id="llistFilter" class="textfield" onkeyup="doFilter(';
								echo "'llistFilter', 'llist', true)";
								echo ';eventFilter(this);" title="Input something to filter the following list">';
								echo '<li class="list-item">';
								echo '<span class="li-sandwich checked"><input type="checkbox" name="checkLogInfo" id="cLogInfo" class="hidden" onchange="eventFilter(this)" checked><label for="cLogInfo" title="Filter infos" class="statut"><svg class="loginfo" viewBox="0 0 24 24"><path class="loginfo" fill="rgba(210,210,210,.9)" d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 8 8 8 8 0 0 0 8-8 8 8 0 0 0-8-8zm0 1a7 7 0 0 1 7 7 7 7 0 0 1-7 7 7 7 0 0 1-7-7 7 7 0 0 1 7-7zm-1 2v2h2V7h-2zm0 3v7h2v-7h-2z"/></svg></label></span>';
								echo '<span class="li-sandwich checked"><input type="checkbox" name="checkLogSucc" id="cLogSucc" class="hidden" onchange="eventFilter(this)" checked/><label for="cLogSucc" title="Filter successes" class="statut"><svg class="logsucc" viewBox="0 0 24 24"><path class="logsucc" fill="rgba(210,210,210,.9)" d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 8 8 8 8 0 0 0 8-8 8 8 0 0 0-8-8zm0 1a7 7 0 0 1 7 7 7 7 0 0 1-7 7 7 7 0 0 1-7-7 7 7 0 0 1 7-7zm4.95 3.73l-3.54 3.53-3.5 3.54-2.8-2.83-.7.7 3.5 3.53.7-.7 3.53-3.5 3.53-3.53-.7-.7z"/></svg></label></span>';
								echo '<span class="li-sandwich checked"><input type="checkbox" name="checkLogWarn" id="cLogWarn" class="hidden" onchange="eventFilter(this)" checked/><label for="cLogWarn" title="Filter warnings" class="statut"><svg class="logwarn" viewBox="0 0 24 24"><path class="logwarn" fill="rgba(210,210,210,.9)" d="M12 4.4l-.6 1L4.7 17 4 18.3 3 20h18l-1-1.7-7.4-13-.6-1zm0 2L19.3 19H4.7L12 6.4zM11 10v5h2v-5h-2zm0 6v2h2v-2h-2z"/></svg></label></span>';
								echo '<span class="li-sandwich checked"><input type="checkbox" name="checkLogErr" id="cLogErr" class="hidden" onchange="eventFilter(this)" checked/><label for="cLogErr" title="Filter errors" class="statut"><svg class="logerr" viewBox="0 0 24 24"><path class="logerr" fill="rgba(210,210,210,.9)" d="M8.5 4L4 8.5v7L8.5 20h7l4.5-4.5v-7L15.5 4h-7zm.4 1h6.2L19 8.9v6.2L15.1 19H8.9L5 15.1V8.9L8.9 5zm.18 2.66L7.66 9.08 10.6 12l-2.94 2.92 1.42 1.42L12 13.4l2.92 2.94 1.42-1.42L13.4 12l2.94-2.92-1.42-1.42L12 10.6 9.08 7.65z""/></svg></label></span>';
								echo '<span class="li-sandwich checked"><input type="checkbox" name="checkLogCrit" id="cLogCrit" class="hidden" onchange="eventFilter(this)" checked/><label for="cLogCrit" title="Filter failures" class="statut"><svg class="logcrit" viewBox="0 0 24 24"><path class="logcrit" fill="rgba(210,210,210,.9)" d="M7.05 4.22L4.22 7.05 9.17 12l-4.95 4.95 2.83 2.83L12 14.83l4.95 4.95 2.83-2.83L14.83 12l4.95-4.95-2.83-2.83L12 9.17 7.05 4.22zM7 5.64l4.27 4.26.7.7.74-.72L17 5.64l1.4 1.4-4.23 4.25-.73.7.7.7 4.2 4.2-1.4 1.4-4.2-4.2-.7-.7-.7.66L7.1 18.3l-1.4-1.4 4.2-4.2.67-.68-.7-.7L5.6 7.05 7 5.65z"/></svg></label></span>';
								echo '<span class="li-sandwich right" onclick="post(';
								echo "'".ROOTPATH."admin.php', {CLog: true}, 'get'";
								echo ')" title="Clean Log"><svg id="CleanLog" viewBox="0 0 24 24"><path class="cleanlogtext" fill="rgba(210,210,210,.9)" d="M8 7v1h7V7H8zm0 2v1h5V9H8zm0 2v1h8v-1H8zm0 2v1h4v-1H8zm0 2v1h5v-1H8z"></path><path class="cleanlogoutline" fill="rgba(210,210,210,.9)" d="M5 4v16h8v-1H6V5h12v7h1V4H5z"></path><path class="cleanlogx" fill="rgba(210,210,210,.9)" d="M14.7 13l-.7.7 2.8 2.8-2.8 2.8.7.7 2.8-2.8 2.8 2.8.7-.7-2.8-2.8 2.8-2.8-.7-.7-2.8 2.8-2.8-2.8z"></path></svg></span>';
								echo '<span class="li-sandwich" onclick="post(';
								echo "'".ROOTPATH."admin.php', {ELog: true}, 'get'";
								echo ')" title="Export Log"><svg id="ExportLog" viewBox="0 0 24 24"><path class="explogbase" fill="rgba(210,210,210,.9)" d="M4 11v10h16V11h-7v1h-1v1h1v1h-1v1h1v1h-1v1h1v1h-2v-2h1v-1h-1v-1h1v-1h-1v-1h1v-1H4z"></path><path class="explogfile" fill="rgba(210,210,210,.9)" d="M5 4v7h1V5h12v6h1V4H5zm3 3v1h7V7H8zm0 2v1h5V9H8z"></path><path class="explogbasetop" fill="rgba(210,210,210,.9)" d="M4 11v6h7v-1h1v-1h-1v-1h1v-1h-1v-1h1v1h1v1h-1v1h1v1h-1v1h8v-6H4z"></path></svg></span>';

								echo '</li>';
								//post('/mini/admin.php', {CLog: true}, "get")
								echo '<ul id="llist" class="full-list">';
								foreach ($mainlog  as $line) {
									$line_details = explode('|', $line);
										echo '<li class="list-item">';
										if ($line_details[1] === "1") echo '<span title="Operation succeed" class="statut"><svg class="logsucc" viewBox="0 0 24 24"><path class="logsucc" fill="rgba(210,210,210,.9)" d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 8 8 8 8 0 0 0 8-8 8 8 0 0 0-8-8zm0 1a7 7 0 0 1 7 7 7 7 0 0 1-7 7 7 7 0 0 1-7-7 7 7 0 0 1 7-7zm4.95 3.73l-3.54 3.53-3.5 3.54-2.8-2.83-.7.7 3.5 3.53.7-.7 3.53-3.5 3.53-3.53-.7-.7z"/></svg></span>';
										if ($line_details[1] === "0") echo '<span title="This is an info" class="statut"><svg class="loginfo" viewBox="0 0 24 24"><path class="loginfo" fill="rgba(210,210,210,.9)" d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 8 8 8 8 0 0 0 8-8 8 8 0 0 0-8-8zm0 1a7 7 0 0 1 7 7 7 7 0 0 1-7 7 7 7 0 0 1-7-7 7 7 0 0 1 7-7zm-1 2v2h2V7h-2zm0 3v7h2v-7h-2z"/></svg></span>';
										if ($line_details[1] === "2") echo '<span title="This may require your attention" class="statut"><svg class="logwarn" viewBox="0 0 24 24"><path class="logwarn" fill="rgba(210,210,210,.9)" d="M12 4.4l-.6 1L4.7 17 4 18.3 3 20h18l-1-1.7-7.4-13-.6-1zm0 2L19.3 19H4.7L12 6.4zM11 10v5h2v-5h-2zm0 6v2h2v-2h-2z"/></svg></span>';
										if ($line_details[1] === "3") echo '<span title="You d better read this" class="statut"><svg class="logerr" viewBox="0 0 24 24"><path class="logerr" fill="rgba(210,210,210,.9)" d="M8.5 4L4 8.5v7L8.5 20h7l4.5-4.5v-7L15.5 4h-7zm.4 1h6.2L19 8.9v6.2L15.1 19H8.9L5 15.1V8.9L8.9 5zm.18 2.66L7.66 9.08 10.6 12l-2.94 2.92 1.42 1.42L12 13.4l2.92 2.94 1.42-1.42L13.4 12l2.94-2.92-1.42-1.42L12 10.6 9.08 7.65z""/></svg></span>';
										if ($line_details[1] === "4") echo '<span title="You have to pay attention to this" class="statut"><svg class="logcrit" viewBox="0 0 24 24"><path class="logcrit" fill="rgba(210,210,210,.9)" d="M7.05 4.22L4.22 7.05 9.17 12l-4.95 4.95 2.83 2.83L12 14.83l4.95 4.95 2.83-2.83L14.83 12l4.95-4.95-2.83-2.83L12 9.17 7.05 4.22zM7 5.64l4.27 4.26.7.7.74-.72L17 5.64l1.4 1.4-4.23 4.25-.73.7.7.7 4.2 4.2-1.4 1.4-4.2-4.2-.7-.7-.7.66L7.1 18.3l-1.4-1.4 4.2-4.2.67-.68-.7-.7L5.6 7.05 7 5.65z"/></svg></span>';

										echo '<div class="list-content">';
										echo '<a class="list-item-title">'.stripslashes(explode('-',$line_details[2])[0]).' '.stripslashes(explode('-',$line_details[2])[1]).'</a>';
										echo '<p class="list-item-desc lone-line">'.stripslashes($line_details[3]).'</p>';
										echo '<p class="event-date" style="display:none;">'.stripslashes($line_details[0]).'</p>';
										echo '</div>';
										echo '<span onclick="viewlog(this);" title="View details about this log entry"><svg id="viewBoard" viewBox="0 0 24 24"><path class="eyetop" fill="rgba(210,210,210,.9)" d="M12 6a10 10 0 0 0-9.2 6 10 10 0 0 0 1 2A8.5 8.5 0 0 1 12 7.4a8.5 8.5 0 0 1 8.2 6.4 10 10 0 0 0 1-2A10 10 0 0 0 12 6z"/><path class="eyecnt" fill="rgba(210,210,210,.9)" d="M12 8a4 4 0 0 0-4 4 4 4 0 0 0 4 4 4 4 0 0 0 4-4 4 4 0 0 0-4-4zm0 1a3 3 0 0 1 1.1.2 1.5 1.5 0 0 0-1.18 1.47 1.5 1.5 0 0 0 1.5 1.5 1.5 1.5 0 0 0 1.44-1.08 3 3 0 0 1 .14.9 3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1 3-3z"/></svg></span>';
										echo '</li>';
								}
								echo "</ul>";

								?>
								</ul>
							</p>
						</div>
					</div>
				</div>
			</div>

<div id="CurrentDBModal" class="modal">
				<form action="admin.php" class="large-container">
					<div class="card midgrey">
						<div class="card-title">
							<span class="form-signin-heading">Setup <span id="CurrentDBnamespan"> **changeme** </span></span>
							<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
								<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
							</span>
						</div>
						<div class="card-content">
							<?php
							$string = file_get_contents($filename);
							$jsonRS = json_decode ($string,true);
							echo '<span class="input-info" id="basic-addon1">Filter :</span>';
							echo '<input type="text" name="ListFilter" id="elistFilter" class="textfield" onkeyup="doFilter(';
							echo "'elistFilter', 'elist', true)";
							echo '" title="Input something to filter the following list">';
							echo '<ul id="elist" class="full-list">';
							foreach ($jsonRS as $rs) {
								if (! $rs["name"] == "") {
									echo '<li class="list-item"><div class="list-content">';
									echo '<a href="'.ROOTPATH.'Boards/'.stripslashes($rs["name"]).'/"class="list-item-title">'.stripslashes($rs["name"]).'</a>';
									echo '<p class="list-item-desc">'.stripslashes($rs["mail"]).'</p>';
									echo '</div>';
									// actions on li

									echo '<span class="li-sandwich" onclick="addnuser(';
									echo "'".stripslashes($rs["name"])."'";
									echo ');" title="Add a new user from this one"><svg id="addDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="adddbtop" fill="rgba(210,210,210,.9)" d="M12-6a4 2 0 0 0-4 2v2c0 1.1 1.8 2 4 2s4-.9 4-2v-2a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.32V-2c0 .55-1.34 1-3 1s-3-.45-3-1v-.68A4 2 0 0 0 12-2a4 2 0 0 0 3-.68z"/><path class="adddbbot" fill="rgba(210,210,210,.9)" d="M12 9a4 2 0 0 0-4 2v4c0 1 1.8 2 4 2h1v-1h-1c-1.7 0-3-.4-3-1v-.7c.7.4 1.8.7 3 .7s2.3-.3 3-.7v.7h1v-4a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.3v.7c0 .6-1.3 1-3 1s-3-.4-3-1v-.7a4 2 0 0 0 3 .7 4 2 0 0 0 3-.7zm2 .7v3h-3v1h3v3h1v-3h3v-1h-3v-3h-1z"/></svg></span>';

									echo '<span class="li-sandwich" onclick="window.open(';
									echo "'".ROOTPATH."Boards/".stripslashes($rs["name"])."/','_blank'";
									echo ');" title="View '.stripslashes($rs["name"]).' files"><svg id="filesfolder" viewBox="0 0 24 24"><path class="ffolder" fill="rgba(210,210,210,.9)" d="M3 5v14h18V7H11L9 5H3zm1 1h4.6l1 1-3 3H4V6z"/></svg></span>';

									if ($rs["name"] == "admin") {
										echo '<span class="li-sandwich" title="Cannot delete Admin o-o">';
										echo '<svg id="LockedDB" viewBox="0 0 24 24"><path class="locktop" fill="rgb(230, 197, 197)" d="M12 3a4 4 0 0 0-4 4v4h1V7a3 3 0 0 1 3-3 3 3 0 0 1 3 3v4h1V7a4 4 0 0 0-4-4z"/><path class="lockbot" fill="rgb(230, 197, 197)" d="M6 10v11h12V10H6zm6 3a1.5 1.5 0 0 1 1.5 1.5 1.5 1.5 0 0 1-.8 1.3V18h-1.4v-2.2a1.5 1.5 0 0 1-.8-1.3A1.5 1.5 0 0 1 12 13z"/></svg></span>';
									} else {
										echo '<span class="li-sandwich" onclick="rmuser(';
										echo "'".stripslashes($rs["name"])."'";
										echo ');" title="Remove '.stripslashes($rs["name"]).'"><svg id="removeDB" viewBox="0 0 24 24"><path class="trashtop" fill="rgba(210,210,210,.9)" d="M9 3v2H4v1h16V5h-5V3H9zm1 1h4v1h-4V4z"/><path class="trashbot" fill="rgba(210,210,210,.9)" d="M6 7v14h12V7h-1v13H7V7H6zm3 2v8h1V9H9zm5 0v8h1V9h-1z"/></svg></span>';
									}

									echo '<span class="li-sandwich" onclick="window.location.href =';
									echo "'"."admin.php?setupDB=true"."'";
									echo ';" title="Refresh '.stripslashes($rs["name"]).'"><svg id="RefreshDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="reloadarrows" fill="rgba(210,210,210,.9)"d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 .64 3.12 8 8 0 0 0 .35.7 8 8 0 0 0 .1.24l.73-.74 3.5-3.5-.7-.7-3.23 3.22A7 7 0 0 1 5 12a7 7 0 0 1 7-7 7 7 0 0 1 3.33.84l.73-.72A8 8 0 0 0 12 4zm6.88 3.94l-.73.74-3.5 3.5.7.7 3.23-3.22A7 7 0 0 1 19 12a7 7 0 0 1-7 7 7 7 0 0 1-3.33-.84l-.73.72A8 8 0 0 0 12 20a8 8 0 0 0 8-8 8 8 0 0 0-.64-3.12 8 8 0 0 0-.35-.7 8 8 0 0 0 0-.02 8 8 0 0 0-.1-.22z"/></svg></span>';

									echo '<span class="li-sandwich" onclick="uexp(';
									echo "'".stripslashes($rs["name"])."'";
									echo ');" title="Export '.stripslashes($rs["name"]).' infos"><svg id="exportDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="databasearrow" fill="rgba(210,210,210,.9)" d="M18.46 13l-.7.7 2.3 2.3H14v1h6.1l-2.34 2.35.7.7 2.83-2.82.7-.7L18.43 13z"/><path class="databasefile" fill="rgba(210,210,210,.9)" d="M5 4v16h12v-1H6V5h12v7h1V4H5z"/><path class="databasedata" fill="rgba(210,210,210,.9)" d="M12 7a4 2 0 0 0-4 2v4c0 1.1 1.8 2 4 2s4-.9 4-2V9a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.32V11c0 .55-1.34 1-3 1s-3-.45-3-1v-.68a4 2 0 0 0 3 .68 4 2 0 0 0 3-.68zm-6 2c.73.4 1.8.68 3 .68s2.27-.27 3-.7v.7c0 .55-1.34 1-3 1s-3-.45-3-1v-.7z"/></svg></span>';

									echo '<span class="li-sandwich" onclick="editUser(';
									echo "'".stripslashes($rs["name"])."'";
									echo ');" title="Edit '.stripslashes($rs["name"]).'"><svg id="editUser" viewBox="0 0 24 24"><path class="editpen" fill="rgba(210,210,210,.9)" d="M16.95 2.8l-.2.2-.5.52-2.13 2.12-.7.7L3 16.76V21h4.24l1-1 9.42-9.4.7-.72 2.12-2.12.52-.52.2-.2-.2-.18-.52-.52-2.82-2.82-.52-.52-.2-.2zm0 1.42l2.83 2.83-2.12 2.12-2.83-2.83 2.12-2.12zm-2.83 2.83l2.83 2.83L9 17.83V17H7v-2h-.83l7.95-7.95zM5.17 16H6v2h2v.83L6.83 20H5.15L4 18.85v-1.68L5.17 16z"/></svg></span>';


									echo '</li>';
								}
							}
							echo "</ul>";

							?>
						</div>
						<div class="card-footer">
							<div class="card-actions">
								<!-- <input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/> -->
								<span class="btn-second btn btn-lg btn-primary btn-block" onclick="window.open(document.getElementById('CurrentDBnamespan').innerHTML,'_blank');" title="Export database"><a><svg id="exportDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="databasearrow" fill="rgba(210,210,210,.9)" d="M18.46 13l-.7.7 2.3 2.3H14v1h6.1l-2.34 2.35.7.7 2.83-2.82.7-.7L18.43 13z"/><path class="databasefile" fill="rgba(210,210,210,.9)" d="M5 4v16h12v-1H6V5h12v7h1V4H5z"/><path class="databasedata" fill="rgba(210,210,210,.9)" d="M12 7a4 2 0 0 0-4 2v4c0 1.1 1.8 2 4 2s4-.9 4-2V9a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.32V11c0 .55-1.34 1-3 1s-3-.45-3-1v-.68a4 2 0 0 0 3 .68 4 2 0 0 0 3-.68zm-6 2c.73.4 1.8.68 3 .68s2.27-.27 3-.7v.7c0 .55-1.34 1-3 1s-3-.45-3-1v-.7z"/></svg></a></span>

								<span class="btn-second btn btn-lg btn-primary btn-block" id="cleanslctdb" onclick="cleanthisDB(document.getElementById('CurrentDBnamespan').innerHTML)" title="Clean database"><a><svg id="cleanDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="databasefile" fill="rgba(210,210,210,.9)" d="M5 4v16h9v-1H6V5h12v9h1V4H5z"/><path class="databasecleaner" fill="rgba(210,210,210,.9)" d="M16 15l-2 2 2 2h5v-4h-5zm.4 1H20v2h-3.6l-1-1 1-1z"/><path class="databasedatatop" fill="rgba(210,210,210,.9)" d="M12 7a4 2 0 0 0-4 2v2c0 1 1.8 2 4 2s4-1 4-2V9a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.3v.7c0 .6-1.3 1-3 1s-3-.4-3-1v-.7a4 2 0 0 0 3 .7 4 2 0 0 0 3-.7z"/><path class="databasedatabot" fill="rgba(210,210,210,.9)" d="M8 11v2c0 1 1.8 2 4 2s4-1 4-2v-2h-1v2c0 .6-1.3 1-3 1s-3-.4-3-1v-2H8z"/></svg></a></span>


								<span class="btn-second btn btn-lg btn-primary btn-block" onclick="window.location.href ='admin.php?setupDB=true'" title="Refresh database"><a><svg id="RefreshDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="reloadarrows" fill="rgba(210,210,210,.9)"d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 .64 3.12 8 8 0 0 0 .35.7 8 8 0 0 0 .1.24l.73-.74 3.5-3.5-.7-.7-3.23 3.22A7 7 0 0 1 5 12a7 7 0 0 1 7-7 7 7 0 0 1 3.33.84l.73-.72A8 8 0 0 0 12 4zm6.88 3.94l-.73.74-3.5 3.5.7.7 3.23-3.22A7 7 0 0 1 19 12a7 7 0 0 1-7 7 7 7 0 0 1-3.33-.84l-.73.72A8 8 0 0 0 12 20a8 8 0 0 0 8-8 8 8 0 0 0-.64-3.12 8 8 0 0 0-.35-.7 8 8 0 0 0 0-.02 8 8 0 0 0-.1-.22z"/></svg></a></span>
								<span class="btn-second btn btn-lg btn-primary btn-block" onclick="addnuser();" title="Add entry"><a><svg id="addDB" style="width: 24px; height: 24px;" viewBox="0 0 24 24"><path class="adddbtop" fill="rgba(210,210,210,.9)" d="M12-6a4 2 0 0 0-4 2v2c0 1.1 1.8 2 4 2s4-.9 4-2v-2a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.32V-2c0 .55-1.34 1-3 1s-3-.45-3-1v-.68A4 2 0 0 0 12-2a4 2 0 0 0 3-.68z"/><path class="adddbbot" fill="rgba(210,210,210,.9)" d="M12 9a4 2 0 0 0-4 2v4c0 1 1.8 2 4 2h1v-1h-1c-1.7 0-3-.4-3-1v-.7c.7.4 1.8.7 3 .7s2.3-.3 3-.7v.7h1v-4a4 2 0 0 0-4-2zm0 1a3 1 0 0 1 3 1 3 1 0 0 1-3 1 3 1 0 0 1-3-1 3 1 0 0 1 3-1zm3 2.3v.7c0 .6-1.3 1-3 1s-3-.4-3-1v-.7a4 2 0 0 0 3 .7 4 2 0 0 0 3-.7zm2 .7v3h-3v1h3v3h1v-3h3v-1h-3v-3h-1z"/></svg></a></span>

								<button class="right btn-main btn btn-lg btn-primary btn-block" onclick="hidemodals();">Ok</button>
							</div>
						</div>
				</div>
				</form>
			</div>

<div id="LogDetailsModal" class="modal">
	<form class="container">
		<div class="card midgrey">
			<div class="card-title">
				<span class="form-signin-heading">Event from <span id="eventnamespan"> **changeme** </span></span>
				<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
					<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
				</span>
			</div>
			<div class="card-content">
				<li class="list-item">
					<span title="Operation succeed" class="statut">
						<svg style="width: 48px; height:48px;" viewBox="0 0 24 24"><path class="logsucc" fill="rgba(210,210,210,.9)" d="M12 4a8 8 0 0 0-8 8 8 8 0 0 0 8 8 8 8 0 0 0 8-8 8 8 0 0 0-8-8zm0 1a7 7 0 0 1 7 7 7 7 0 0 1-7 7 7 7 0 0 1-7-7 7 7 0 0 1 7-7zm4.95 3.73l-3.54 3.53-3.5 3.54-2.8-2.83-.7.7 3.5 3.53.7-.7 3.53-3.5 3.53-3.53-.7-.7z"></path></svg>
					</span>
					<div class="list-content">
						<a id="eventdate" class="list-item-title">DATE - time</a>
						<p class="eventstaut list-item-desc lone-line">Statut</p>
					</div>
				</li>
				<div class="input-group">
					<span class="input-info" id="basic-addon1">triggered by:</span>
					<p id="aeventtrig" style="margin: 16px;"> **Triggered**</p>
				</div>
				<div class="input-group">
					<label class="input-info sr-only">Event details :</label>
					<p id="peventdetails" style="margin: 16px;">
						**eventdetails**
					</p>
				</div>
			</div>
			<!-- <div class="card-footer">
				<div class="card-actions">
					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/>
					<span class="btn-danger btn btn-lg btn-primary btn-block" onclick="rmuser();">Delete</span>

					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Change</button>
				</div>
			</div> -->
	</div>
	</form>
</div>

<div id="userEditModal" class="modal">
	<form action="admin.php" class="container">
		<div class="card midgrey">
			<div class="card-title">
				<span class="form-signin-heading">Edit infos of <span id="unamespan"> **changeme** </span></span>
				<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
					<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
				</span>
			</div>
			<div class="card-content">
				<input type="hidden" name="u" value="">
				<div class="input-group">
					<span class="input-info" id="basic-addon1">new username :</span>
					<input type="text" name="nusername" class="textfield" autofocus="autofocus" onblur="checkuname(this);">
				</div>
				<div class="input-group">
					<span class="input-info" id="basic-addon1">new mail:</span>
					<input type="text" name="nemail" class="textfield" onblur="checkumail(this);">
				</div>
				<div class="input-group">
					<label for="inputPassword" class="input-info sr-only">new password :</label>
					<input type="password" id="inputPassword" class="textfield" onblur="hashPsw('inputPassword','hashedPassword')">
					<input type="hidden" name="npassword" id="hashedPassword" class="textfield">
				</div>
			</div>
			<div class="card-footer">
				<div class="card-actions">
					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/>
					<span class="btn-danger btn btn-lg btn-primary btn-block" onclick="rmuser();">Delete</span>

					<!--a class="btn btn-lg btn-primary btn-block" href="login.php">Login ?</a-->
					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Change</button>
				</div>
			</div>
	</div>
	</form>
</div>

<div id="userAddModal" class="modal">
	<form action="admin.php" class="container">
		<div class="card midgrey">
			<div class="card-title">
				<span class="form-signin-heading">Create a new user</span>
				<span class="card-collapse" title="Discard changes" onclick="hidemodals()">
					<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="rgba(210, 210, 210, 0.9)" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/></svg>
				</span>
			</div>
			<div class="card-content">
				<div class="input-group">
					<span class="input-info" id="basic-addon1">username :</span>
					<input type="text" name="addusername" class="textfield" autofocus="autofocus" onblur="checkuname(this);" required="required">
				</div>
				<div class="input-group">
					<span class="input-info" id="basic-addon1">mail:</span>
					<input type="text" name="addemail" class="textfield" onblur="checkumail(this);" required="required">
				</div>
				<div class="input-group">
					<label for="inputPassword" class="input-info sr-only">password :</label>
					<input type="password" id="ninputPassword" class="textfield" onblur="hashPsw('ninputPassword','nhashedPassword')" required="required">
					<input type="hidden" name="addpassword" id="nhashedPassword" class="textfield">
				</div>
			</div>

			<div class="card-footer">
				<div class="card-actions">
					<input type="reset" class="btn btn-second btn-lg btn-primary btn-block"/>
					<!-- <span class="btn-danger btn btn-lg btn-primary btn-block" onclick="rmuser();">Delete</span> -->

					<!--a class="btn btn-lg btn-primary btn-block" href="login.php">Login ?</a-->
					<button class="right btn-main btn btn-lg btn-primary btn-block" type="submit">Create</button>
				</div>
			</div>
	</div>
	</form>
</div>

  </body>
</html>

<script>

/*
document.getElementById("ulist").getElementsByTagName("li")[2].firstElementChild.firstElementChild.innerHTML
*/
function splitUlList() {
	var ul = document.getElementById("CurrentDBModal").getElementsByClassName("card-content")[0].getElementsByTagName("ul")[0];
	var splitted = null;
	for(var li = 0;li < tosplitli.length; li++) {
		if (li%10 == 0) {
			splitted = ("<ul></ul>");
		}
		splitted.append(li);
		document.getElementById("CurrentDBModal").getElementsByClassName("card-content")[0].getElementsByTagName("ul")[0].append(splitted);
	}
	var maxItems = 10;

}

function addnuser() {
	document.getElementById('userAddModal').style.display = "block";
}

function rmuser(uname = null) {
	if (uname === null) uname = document.getElementsByName("u")[0].value;
	post('<?php echo ROOTPATH ?>admin.php', {rmuser: uname}, "get");
}

function uexp(uname) {
	post('<?php echo ROOTPATH ?>admin.php', {uexp: uname}, "get");
}

function hashPsw(o,d){
	document.getElementById(d).value = SHA256(document.getElementById(o).value);
}

function editUser(uname) {
	document.getElementById('unamespan').innerHTML = uname;
	//document.body.style.overflow = 'hidden';
	document.getElementById('userEditModal').style.display = "block";
	document.getElementsByName("u")[0].value = uname;
}

function cleanthisDB(DBname) {
	var DBnameSplit = DBname.split(".");
	DBnameSplit.pop();
	var RealDBname = DBnameSplit.join(".");
	post('<?php echo ROOTPATH ?>admin.php', {ClDB: RealDBname}, "get");
}

function setupDB(CurrentDBname) {
	document.getElementById('CurrentDBnamespan').innerHTML = CurrentDBname;

	//document.getElementById('cleanslctdb')[0].setAttribute('onclick', onclick);
	//document.body.style.overflow = 'hidden';
	document.getElementById('CurrentDBModal').style.display = "block";
	//document.getElementsByName("u")[0].value = uname;
}

function checkuname(e) {
	var inner = e.value;
	//console.log(inner);
	if (isindatabase('name', inner)) {
		if (! e.classList.contains('warn')) toggleClass(e, 'warn');
	} else {
		if (e.classList.contains('warn')) toggleClass(e, 'warn');
	}
}

function checkumail(e) {
	var inner = e.value;
	//console.log(inner);
	if (isindatabase('mail', inner)) {
		if (! e.classList.contains('warn')) toggleClass(e, 'warn');
	} else {
		if (e.classList.contains('warn')) toggleClass(e, 'warn');
	}
}

function doFilter(sid,sul,ext=false) {
		var a, i;
		var filter = document.getElementById(sid).value.toUpperCase();
		var li = document.getElementById(sul).getElementsByTagName('li');
		if (ext) {
			//console.log("plop");
			for (i = 0; i < li.length; i++) {
			        a = li[i].getElementsByTagName("a")[0];
							p = li[i].getElementsByTagName("p")[0];
			        if ((a.innerHTML.toUpperCase().indexOf(filter) > -1) || (p.innerHTML.toUpperCase().indexOf(filter) > -1) ) {
			            li[i].style.display = "";
			        } else {
			            li[i].style.display = "none";
			        }
			   }
		} else {
				//console.log("plip");
			for (i = 0; i < li.length; i++) {
			        a = li[i].getElementsByTagName("a")[0];
			        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
			            li[i].style.display = "";
			        } else {
			            li[i].style.display = "none";
			        }
			   }
		}
}

function eventFilter(elem) {
		toggleClass(ancestor(elem, 'li-sandwich'), 'checked');
		sul="llist";
		var svgclass, i;
		var li = document.getElementById(sul).getElementsByTagName('li');
		var doInfo, doSucc, doWarn, doErr, doCrit;
		//document.getElementById(sul).getElementsByTagName('li')[0].getElementsByTagName("span")[0].getElementsByTagName("svg")[0].classList[0]
		doInfo = document.getElementsByName("checkLogInfo")[0].checked;
		doSucc = document.getElementsByName("checkLogSucc")[0].checked;
		doWarn = document.getElementsByName("checkLogWarn")[0].checked;
		doErr = document.getElementsByName("checkLogErr")[0].checked;
		doCrit = document.getElementsByName("checkLogCrit")[0].checked;
		//console.log(doInfo, doSucc, doWarn, doErr, doCrit);

	for(i=0; i<li.length; i++) {
	        svgclass = li[i].getElementsByTagName("span")[0].getElementsByTagName("svg")[0].classList[0];
	        if (((svgclass == "loginfo" && doInfo) || (svgclass == "logsucc" && doSucc) || (svgclass == "logwarn"  && doWarn) || (svgclass == "logerr" && doErr) || (svgclass == "logcrit" && doCrit))) {
	            li[i].style.display = "";
	        } else {
	            li[i].style.display = "none";
	        }
	   }
}

function viewlog(elem) {
	var triggerdtl;
	var li = ancestor(elem, 'list-item').getElementsByClassName('list-content')[0];
	var p = li.getElementsByClassName('lone-line')[0].innerHTML;
	var a = li.getElementsByClassName('list-item-title')[0].innerHTML;
	var ic = ancestor(li,'list-item').getElementsByClassName('statut')[0].getElementsByTagName('svg')[0];
	var d = li.getElementsByClassName('event-date')[0].innerHTML;
	var s = ancestor(li,'list-item').getElementsByClassName('statut')[0].getAttribute("title");
	document.getElementById('eventnamespan').innerHTML = a.split(" ")[0];
	document.getElementById('peventdetails').innerHTML = p;
	var ico = ic.cloneNode(true);

	var dtime = d.split(",")[1];
	var ddate = d.split(",")[0];
	var dday = ddate.split("-")[0];
	var nmonths = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
	var dmonth = nmonths[parseInt(ddate.split("-")[1])-1];
	var dyear = ddate.split("-")[2];
	var edate = "The ".concat(dday, " ", dmonth, " ",dyear);
	var thours = dtime.split(":")[0];
	var tminutes = dtime.split(":")[1];
	var tseconds = dtime.split(":")[2];
	var etime = " at ".concat(thours , ":", tminutes, " and ",tseconds, " seconds.");

	d = edate.concat(etime);

	ico.style.width = "48px";
	ico.style.height = "48px";
	ico.style.marginRight = "8px";

	var lihead = document.getElementById('LogDetailsModal').getElementsByClassName('list-item')[0];
	lihead.innerHTML = "";
	lihead.appendChild(ico);

	var lcont = document.createElement("div");
	lcont.classList.add("list-content");
	var adate = document.createElement("a");
	adate.id="eventdate";
	adate.classList.add("list-item-title");
	var adatetext = document.createTextNode(d);
	adate.appendChild(adatetext);
	lcont.appendChild(adate);

	var sstatut = document.createElement("p");
	sstatut.classList.add("list-item-desc");
	sstatut.classList.add("eventstaut");
	var sstatuttext = document.createTextNode(s);
	sstatut.appendChild(sstatuttext);
	lcont.appendChild(sstatut);

	lihead.appendChild(lcont);

	var user = (a.split(" ")[1]).split("@")[1];
	user = '<a href="./Boards/'.concat(user,'/" target="_blank">',user,'</a>');
	var ip = (a.split(" ")[1]).split("@")[0];
	ip = '<a href="http://www.localiser-ip.com/?ip='.concat(ip ,'" target="_blank">',ip ,'</a>');
	triggerdtl = "The user ".concat(user," which is behind the IP ",ip);
	document.getElementById('aeventtrig').innerHTML = triggerdtl;
	document.getElementById('LogDetailsModal').style.display = "block";
}

function swtcard(e) {
	toggleClass(ancestor( e, 'card'), 'collapsed');
}
</script>

<?php
if(isset($_GET['setupDB']) && ($_GET['setupDB'])==true) {
	echo '<script type="text/javascript">';
  echo "setupDB('".$filename."');";
  echo '</script>';
}
 ?>
