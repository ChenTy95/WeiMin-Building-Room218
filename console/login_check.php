<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href=".\include\common.css" />
	<title>正在为您跳转...</title>
</head>

<body>
	<div class="ResDiv">
		<div style="margin-top:200px; text-align:center; margin-left:2.5%; width:95%; height:80px; border:2px solid #305496; color:#305496; font-size:16px; font-weight:bold; font-family:'Helvetica','Microsoft Yahei','微软雅黑','sans-serif'; line-height:30px; padding-top:20px;">
			正在执行操作，完成后将自动跳转<br/>请耐心等待，不要手动刷新页面……
		</div>
	</div>
</body>
</html>

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
				$_SESSION['AdminID'] = $rows['id'];
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
			
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','AdminLogin','".$_SERVER['HTTP_USER_AGENT']."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
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
	
	mysqli_close($conn);
?>