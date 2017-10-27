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