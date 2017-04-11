<?php

echo 'a ';

if(isset($_GET['id']) && !empty($_GET['id'])) {
  	echo 'b ';
	$cfile = @trim($_GET['id']);
	echo $cfile;
	  // if ( @file_get_contents('http://'.$_SERVER['HTTP_HOST'].$cfile.'json', 0, NULL, 0, 1) || (is_file($cfile.'.json'))) 
			   if ((is_file($cfile.'.json')) && is_readable($cfile.'.json')) {
					echo 'readed  !<br/>';
					$cfiledatas  = file_get_contents($cfile.'.json');
					$comms = json_decode($cfiledatas,true);
					
					foreach ($comms as $comm) {
								if ($comm["name"] && $comm["message"]) {
								echo '<div><span>'.$comm["score"].'</span><span>'.$comm["name"].'</span><p>'.$comm["message"].'</p></div>';
							}
					}
			
					$commstrct = array("name" => "spammer", "message" => "a spam test", "score" => "0");											
					//echo join('', file($cfile.'.json'));
		}
	}


?>