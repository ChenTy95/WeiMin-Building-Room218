<?php
	session_start();
	date_default_timezone_set("Asia/Shanghai");
	
	$_SESSION['FileCode'] = "0";
	
	if (!isset($_POST['pass']))
	{
		session_destroy();
		echo "<script>window.location.href='login.php'; </script>";
		exit();
	}
	
	$pass = $_POST['pass'];
	$pass_server = "WML218Sys_";
	$pass_server_1 = $pass_server . date('mdHi');
	$pass_server_2 = $pass_server . date('mdHi',strtotime("-1 minute"));
	
	if (($pass == $pass_server_1) || ($pass == $pass_server_2))
	{
		$_SESSION['T'] = date("mdHi",strtotime("+20 minute"))."<";
		
		include_once("../conn.php");
		$sql = "INSERT INTO log(id,date,time,state,remark,log) VALUES ('Admin','".date("Y-m-d")."','".date("His")."','AdminLogin','".$_SERVER['HTTP_USER_AGENT']."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
		mysqli_query($conn,$sql);
	
		header("location:index.php");
	}
	else
	{
		echo "<script>alert('Password Error!'); history.go(-1);</script>";
		exit();
	}
?>