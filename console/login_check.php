<?php
	session_start();
	date_default_timezone_set("Asia/Shanghai");
	
	$_SESSION['FileCode'] = "0";
	
	// 注销登录
	if (!isset($_POST['pass']))
	{
		session_destroy();
		echo "<script>window.location.href='login.php'; </script>";
		exit();
	}
	
	// 登录密码检查
	$pass = $_POST['pass'];
	
	// 修改此行内容变更密码前缀 ↓
	$pass_server = "WML218Sys_";

	$pass_server_1 = $pass_server . date('mdHi');
	$pass_server_2 = $pass_server . date('mdHi',strtotime("-1 minute"));
	
	// 两分钟密码输入时间
	if (($pass == $pass_server_1) || ($pass == $pass_server_2))
	{
		$_SESSION['T'] = date("mdHi",strtotime("+20 minute"))."<";
		
		include_once("../conn.php");
		$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('admin','".date("Y-m-d")."','".date("His")."','AdminLogin','".$_SERVER['HTTP_USER_AGENT']."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
		mysqli_query($conn,$sql);
	
		header("location:index.php");
	}
	else
	{
		echo "<script>alert('Password Error!'); history.go(-1);</script>";
		exit();
	}
?>