<?php
$connection = mysqli_connect('sql201.byethost16.com', 'b16_17065705', 'bbowser33');
if (!$connection){
    die("Database Connection Failed" . mysqli_error($connection));
}
$select_db = mysqli_select_db($connection, 'b16_17065705_users');
if (!$select_db){
    die("Database Selection Failed" . mysqli_error($connection));
}?>