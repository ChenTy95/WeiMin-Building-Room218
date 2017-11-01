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
	include_once("conn.php");								// Link Database
	
	function check_input($data)								// Check POST Data
	{
		$data = trim($data);
		$data = htmlspecialchars($data);
		if (get_magic_quotes_gpc())
		{
			$data = stripslashes($data);
		}
		return $data;
	}
	
	session_start();										// Open Session For UserID Record
	$lifeTime = 600;										// Session Availiable Time
    setcookie(session_name(), session_id(), time() + $lifeTime, "/");
	global $conn;
	
	// 注销用户登录
	if (!isset($_POST['ID']))
	{
		$sql = "INSERT INTO log(id,date,time,type,log) VALUES ('".$_SESSION['IdToken']."','".date("Y-m-d")."','".date("His")."','Logout','".date("ymd")."|".$_SESSION['ipInfo']."');";
		mysqli_query($conn,$sql);
		
		session_destroy();
		
		echo "<script>alert('已注销您的登录！'); window.location.href='login.php'; </script>";
		exit();
	}
	
	// 用户登录检查
	$sql = sprintf("SELECT COUNT(*) FROM userinfo WHERE id='%s'",mysqli_real_escape_string($conn,check_input($_POST['ID'])));
	$sql = $sql.sprintf(" AND name='%s' LIMIT 1;",mysqli_real_escape_string($conn,check_input($_POST['Name'])));
	
	$result = mysqli_query($conn,$sql);
	if ($num = mysqli_num_rows($result))
	{
		while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			if ($rows['COUNT(*)']!=1)
			{
				echo "<script>alert('未查到此用户，请检查您的输入！'); history.go(-1);</script>";
				exit();
			}
			else
			{
				$_SESSION['IdToken'] = mysqli_real_escape_string($conn,check_input($_POST['ID']));
				$_SESSION['CSS_No'] = mysqli_real_escape_string($conn,check_input($_POST['CSS_No']));
				
				$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('".$_SESSION['IdToken']."','".date("Y-m-d")."','".date("His")."','Login','CSS=".$_SESSION['CSS_No']."|".$_SESSION['sysInfo']."','".date("ymd")."|".$_SESSION['ipInfo']."');";

				mysqli_query($conn,$sql);
				
				$sql = "UPDATE userinfo SET css=".$_SESSION['CSS_No']." WHERE id='".$_SESSION['IdToken']."';";
				mysqli_query($conn,$sql);
				
				header("location:index.php");
			}
		}
	}
	
	mysqli_close($conn);
?>