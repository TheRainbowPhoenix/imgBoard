<?PHP
$dd=dirname($_SERVER['PHP_SELF']);
if ($dd=='/') echo $dd;
else echo $dd.'/';

?>
