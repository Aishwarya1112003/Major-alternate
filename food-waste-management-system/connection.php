<?php
//change mysqli_connect(host_name,username, password); 
$connection = mysqli_connect("localhost", "root", "Ram1234*");
$db = mysqli_select_db($connection, 'fooddonation');
?>
