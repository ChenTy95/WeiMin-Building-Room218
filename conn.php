<?php

	$host = 'localhost';
	$username = 'root';
	$dbname = 'room218';
	$password = 'root';
	
	$conn = mysqli_connect($host,$username,$password,$dbname);
	if (!$conn)
	{
		die("Connect Database Error: ".mysql_error());
	}
	mysqli_query($conn,"SET NAMES UTF8");	
	mysqli_query($conn,"USE ".$dbname);
?>