<?php
	include_once("../conn.php");
	session_start();
	date_default_timezone_set("Asia/Shanghai");
	
	function check_input($data)
	{
		$data = trim($data);
		$data = htmlspecialchars($data);
		if (get_magic_quotes_gpc())
		{
			$data = stripslashes($data);
		}
		$data = str_replace('|', '', $data);
		return $data;
	}
	
	$_SESSION['FileCode'] = "0";
	
	// 注销登录
	if (!isset($_POST['pass']))
	{
		session_destroy();
		echo "<script>window.location.href='login.php'; </script>";
		exit();
	}
	
	// 登录密码检查
	$pass = check_input($_POST['pass']);
	$adminid = check_input($_POST['adminid']);
	
	$allowAdmin = 0;
	$sql = "SELECT id, name FROM userinfo WHERE identity='admin';";
	$result = mysqli_query($conn,$sql);
	if ($num = mysqli_num_rows($result))
	{
		while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			if ($adminid == $rows['id'])
			{
				$allowAdmin = 1;
				$_SESSION['AdminName'] = $rows['name'];
			}
		}
	}
	
	if ($allowAdmin == 1)
	{
		// 修改此行内容变更密码前缀 ↓
		$pass_server = "wml218";

		$pass_server_1 = $pass_server . date('mdHi');
		$pass_server_2 = $pass_server . date('mdHi',strtotime("-1 minute"));
		
		// 两分钟密码输入时间
		if (($pass == $pass_server_1) || ($pass == $pass_server_2))
		{
			$_SESSION['T'] = date("mdHi",strtotime("+20 minute"))."<";
			
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$adminid."','".date("Y-m-d")."','".date("His")."','AdminLogin','".$_SERVER['HTTP_USER_AGENT']."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
			mysqli_query($conn,$sql);
		
			header("location:index.php");
		}
		else
		{
			echo "<script>alert('Password Error!'); history.go(-1);</script>";
			exit();
		}
	}
	else
	{
		echo "<script>alert('Permission Denied!'); history.go(-1);</script>";
		exit();
	}
	
?>