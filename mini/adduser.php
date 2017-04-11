<?php
require_once('connect.php');
/*if ($readable === null) {
    $readable = fopen($filename, 'w+');
	echo "<span>Database Error. Recreated.</span><br/>";
}*/
if ($readable) {
	//echo "<span>Database opened.</span><br/>";
	
	/*$struct = array("name"=> "z", "mail"=> "z@mail.com", "psw"=> "z", "boards"=> "");
	
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
    }*/
}
fclose($readable);
?>
<!DOCTYPE html>
<html><head>
  <meta charset="UTF-8">
  <title>imgBoard - Test JSON page</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" ></script>
  <script type="text/javascript" src="https://rawgithub.com/silviomoreto/bootstrap-select/master/dist/js/bootstrap-select.js"></script>

  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />

  <style>
  html,body {font-family: Quicksand;margin: 0 auto;}
  </style>

  </head>
<body>

<h2>imgBoard JSON Parsing test</h2>

<div style="max-width: 330px;padding: 15px;margin: 0 auto;">
	<p id="parsertest">
		<h5>Users :</h5>
		<?php
		$string = file_get_contents("Users.json");
		$jsonRS = json_decode ($string,true);
		foreach ($jsonRS as $rs) {
		  echo "<span>".stripslashes($rs["name"])." : </span><br/>";
		  echo "<span>".stripslashes($rs["mail"])." </span>";
		  echo "<span>".stripslashes($rs["psw"])." </span>";
		  echo "<span>".stripslashes($rs["boards"])." </span><br/>";
		}
		?>
	</p>
	
	<p id="userslist">
		<select id="usersinjson">
			<?php
			$string = file_get_contents("Users.json");
			$jsonRS = json_decode ($string,true);
			foreach ($jsonRS as $rs) {
			  echo "<option>".stripslashes($rs["name"])."</option>";
			}
			?>
		</select>
		<?php
			echo hash('sha256','password');
		?>
	</p>
	
</div>
<script>
var x = JSON.stringify({"name": "z", "mail": "z@mail.com","psw": "z", "boards": ["123456", "132457", "1234658"]});   
</script>


</body>
</html>
