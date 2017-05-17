<?php

  require_once('session.php');

  $userscount = 0;
  $postcount = 0;
  $userslist = array();
  $postslist = array();
  $database = json_decode($datas,true);

  foreach ($database as $it) {
    if($it["name"] !== "admin" && $it["name"] != null && $it["name"] != "" ) {
      array_push($userslist, $it["name"]);
      $userscount++;
    }
  }

$randuser = $userslist[rand( 0 , $userscount-1 )];
$randompage = "./Boards/".$randuser."/index.php";

while (!(file_exists("./Boards/".$randuser."/board.json"))) {
  $randuser = $userslist[rand( 0 , $userscount-1 )];
}
$file = null;
while ($postcount <= 1 && !file_exists($file)) {
  $randuser = $userslist[rand( 0 , $userscount-1 )];
  if(file_exists("./Boards/".$randuser."/board.json")) {
    $banme = $randuser;
    $bdatas = file_get_contents("./Boards/".$randuser."/board.json");
    $bdatabase = json_decode($bdatas,true);

    foreach ($bdatabase as $bit => $ent) {
      if((strpos($bit, ".") !== false)) { //if there's a dot in it, so if it's a file
        $postcount++;
        array_push($postslist, $bit);
      }
    }

    if ($postcount >= 1) {
      $randpost = $postslist[rand( 0 , $postcount-1 )];
      $randompost = "./Boards/".$randuser."/view.php?img=".$randpost;
      $file = './Boards/'.$randuser.'/'.$randpost;
      // echo '<img style="max-width:64px;max-heigth;64px" src="./Boards/'.$randuser.'/'.$randpost.'"/>';
    }


    //echo $randompost;

  }
  }

if(isset($_GET['randpost'])) {
  header("location: $randompost");
  // echo $randompost;
} else {
  header("location: $randompage");
  // echo $randompage;
}

//header("location: $randompage");




 ?>
