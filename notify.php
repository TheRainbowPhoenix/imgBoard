<?php
  require_once('session.php');
  require_once("events.php");

if(isset($_POST['clnot']) && ($_POST['clnot'])==true) {
  CleanEvent("./Boards/",null);
  echo "<script>history.go(-1)</script>";
	@header("location: index.php");
} else {

  //HandleEvent(1,"blaeazaob","./Boards/".$_SESSION['username']."/");
 ?>
<button class="sandwich" onclick="notdrp();">
<?php
//$string = @file_get_contents("notifs.json");
$string = test_function();
$unread = 0;
if ($string==null) {
  echo'<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M12 1a2 2 0 0 0-2 2v.3A7 7 0 0 0 5 10v6l-2 2v1h18v-1l-2-2v-6a7 7 0 0 0-5-6.7V3a2 2 0 0 0-2-2zm0 4a5 5 0 0 1 5 5v7H7v-7a5 5 0 0 1 5-5z" />
      <path fill="#fff" d="M10 20a2 2 0 0 0 2 2 2 2 0 0 0 2-2h-4z"/>
    </svg>';
} else {
  $unread = 1;
}

if ($unread>0) {
  $SvgHeart = '<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M8.72 5C6.12 5 4 7.08 4 9.64c0 2.2 1.04 3.86 2.48 5.34 1.45 1.5 3.32 2.86 5.17 4.67L12 20l.35-.35c1.85-1.8 3.72-3.18 5.17-4.67C18.96 13.5 20 11.85 20 9.64 20 7.08 17.88 5 15.28 5c-1.25 0-2.4.56-3.28 1.42C11.12 5.56 9.97 5 8.72 5zm3.67 2.34c.7-.86 1.75-1.36 2.87-1.36C17.34 5.98 19 7.6 19 9.64c0 1.92-.84 3.27-2.2 4.66-1.27 1.3-3.03 2.65-4.8 4.34-1.77-1.7-3.53-3.04-4.8-4.34C5.85 12.9 5 11.56 5 9.64 5 7.6 6.66 5.98 8.72 5.98c1.12 0 2.18.5 2.9 1.36z"/></svg>';
  $SvgComm2 = '<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M4 6v11h5l3 3 3-3h5V6H4zm1 1h14v9h-4.4L12 18.6 9.4 16H5V7zm3 2v1h8V9H8zm0 2v1h8v-1H8zm0 2v1h6v-1H8z"/></svg>';
  $SvgComm = '<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M4 6v15l1-1 3-3h12V6H4zm1 1h14v9H7.6L5 18.6V7zm3 2v1h8V9H8zm0 2v1h8v-1H8zm0 2v1h6v-1H8z"/></svg>';
  $SvgUpDown= '<svg style="width:24px;height:24px;" viewBox="0 0 24 24"><path fill="#fff" d="M7 9v7H4.5L9 20.5l4.5-4.5H11V9H7z"/><path fill="#fff" d="M15 3.5L10.5 8H13v7h4V8h2.5L15 3.5z"/></svg>';

  $jsonRS = json_decode ($string,true);
  foreach ($jsonRS as $it => $rs) $unread++;
  echo'<a class="ncount-holder"><span class="ncount">'.($unread-1).'</span></a>';
  echo '</button>';
  echo '<div class="notif-menu bg-dark-raised"><span class="pcur"></span><ul class="notif-wrap">';
  foreach ($jsonRS as $it => $rs) {
    echo '<li><span><span style="height: 24px;margin-right: 8px;"> ';
    if ($rs['id'] == "0") echo $SvgComm;
    if ($rs['id'] == "1") echo $SvgHeart;
    if ($rs['id'] == "2") echo $SvgUpDown;

    echo ' </span>'.$rs['text'].'</span></li>';
  }
  echo "</ul>";
  echo '<span class="btn-second btn btn-primary btn-block" onclick="ClNot();">Clean all</span>';
  //CleanEvent($EventFolder = "./Boards/",$Euser = null)
  echo "</div>";
}
 ?>

<script src="<?php echo ROOTPATH; ?>js/base.js"></script>
<script>
function notdrp() {
  document.getElementsByClassName("notif-menu")[0].blur();
	toggleClass(document.getElementsByClassName("notif-menu")[0], 'visibl');
}

function ClNot() {
	post('<?php echo ROOTPATH ?>notify.php', {clnot: true}, "post");
}
</script>
<?php } ?>