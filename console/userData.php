<?php
	include_once("../conn.php");
	session_start();
	
	// 用户数据操作状态
	$_SESSION['user_data_state'] = 'Default';
	$_SESSION['user_data_totNum'] = -1;
	
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
	
	function showUser()
	{
		global $conn, $sql;
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			$i = -1;
			while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				$i++;
				$_SESSION['id'][$i] = $rows['id'];
				$_SESSION['name'][$i] = $rows['name'];
				$_SESSION['identity'][$i] = $rows['identity'];
				$_SESSION['count'][$i] = $rows['count'];
				$_SESSION['css'][$i] = $rows['css'];
			}
			$_SESSION['user_data_totNum'] = $i;
		}
	}
	
	function returnIdntity($identityCode)
	{
		switch ($identityCode)
		{
			case "bk":
				return "本科生";
				break;
			case "szy":
				return "硕士生";
				break;
			case "by":
				return "博士生";
				break;
			case "fdy":
				return "辅导员";
				break;
			case "jg":
				return "教职工";
				break;
			case "admin":
				return "管理员";
				break;
		}
	}
	
	if (isset($_POST['user_query']))
	{
		$_SESSION['user_data_state'] = 'Query';
		if (check_input($_POST['user_identity']) != '#') 
			$sql = "SELECT * FROM userinfo WHERE id LIKE '%".check_input($_POST['user_id'])."%' AND name LIKE '%".check_input($_POST['user_name'])."%' AND identity='".check_input($_POST['user_identity'])."';";
		else
			$sql = "SELECT * FROM userinfo WHERE id LIKE '%".check_input($_POST['user_id'])."%' AND name LIKE '%".check_input($_POST['user_name'])."%';";
		showUser();
		header('location:index.php');
		exit();
	}
	
	if (isset($_POST['user_edit']))
	{
		$_SESSION['user_data_state'] = 'Edit';
		
		$_SESSION['id_old'] = check_input($_POST['user_id_Origin']);
		$_SESSION['name_old'] = check_input($_POST['user_name_Origin']);
		$_SESSION['identity_old'] = check_input($_POST['user_identity_Origin']);
		$_SESSION['id_new'] = check_input($_POST['user_id']);
		$_SESSION['name_new'] = check_input($_POST['user_name']);
		$_SESSION['identity_new'] = check_input($_POST['user_identity']);
		
		$sql = "UPDATE userinfo SET id='".$_SESSION['id_new']."', name='".$_SESSION['name_new']."', identity='".$_SESSION['identity_new']."' WHERE id='".$_SESSION['id_old']."' AND name='".$_SESSION['name_old']."';";
		mysqli_query($conn,$sql);
		
		$affected_row = mysqli_affected_rows($conn);
		if ($affected_row == 1)
		{
			$_SESSION['editFlag'] = 1;
			$sql = "SELECT * FROM userinfo WHERE id='".$_SESSION['id_new']."' AND name='".$_SESSION['name_new']."';";
			showUser();
			
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','DataEdit','EDIT|".$_SESSION['id_old']."，".$_SESSION['name_old']."，".returnIdntity($_SESSION['identity_old'])."->".$_SESSION['id_new']."，".$_SESSION['name_new']."，".returnIdntity($_SESSION['identity_new'])."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
			mysqli_query($conn,$sql);
			
		}	
		else
		{
			$_SESSION['editFlag'] = 0;
			$sql = "SELECT * FROM userinfo WHERE id='".$_SESSION['id_old']."' AND name='".$_SESSION['name_old']."';";
			showUser();
		}	
		
		header('location:index.php');
		exit();
	}
	
	if (isset($_POST['user_add']))
	{
		$_SESSION['user_data_state'] = 'Add';
		
		$_SESSION['id_new'] = check_input($_POST['user_id']);
		$_SESSION['name_new'] = check_input($_POST['user_name']);
		$_SESSION['identity_new'] = check_input($_POST['user_identity']);
		
		$sql = "INSERT INTO userinfo (id, name, identity) VALUES ('".$_SESSION['id_new']."', '".$_SESSION['name_new']."', '".$_SESSION['identity_new']."');";
		mysqli_query($conn,$sql);
		
		$affected_row = mysqli_affected_rows($conn);
		if ($affected_row == 1)
		{
			$_SESSION['addFlag'] = 1;
			$sql = "SELECT * FROM userinfo WHERE id='".$_SESSION['id_new']."' AND name='".$_SESSION['name_new']."';";
			showUser();
			
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','DataEdit','ADD|".$_SESSION['id_new']."，".$_SESSION['name_new']."，".returnIdntity($_SESSION['identity_new'])."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
			mysqli_query($conn,$sql);
		}
		else
		{
			$_SESSION['addFlag'] = 0;
		}
		
		header('location:index.php');
		exit();
	}
	
	if (isset($_POST['user_del']))
	{
		$_SESSION['user_data_state'] = 'Delete';
		
		$_SESSION['id_old'] = check_input($_POST['user_id_Origin']);
		$_SESSION['name_old'] = check_input($_POST['user_name_Origin']);
		$_SESSION['identity_old'] = check_input($_POST['user_identity_Origin']);
		
		$sql = "DELETE FROM userinfo WHERE id='".$_SESSION['id_old']."' AND name='".$_SESSION['name_old']."';";
		mysqli_query($conn,$sql);
		
		$affected_row = mysqli_affected_rows($conn);
		if ($affected_row == 1)
		{
			$_SESSION['delFlag'] = 1;
			
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','DataEdit','DELETE|".$_SESSION['id_old']."，".$_SESSION['name_old']."，".returnIdntity($_SESSION['identity_old'])."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
			mysqli_query($conn,$sql);
		}	
		else
		{
			$_SESSION['delFlag'] = 0;
		}	
		header('location:index.php');
		exit();
	}
?>