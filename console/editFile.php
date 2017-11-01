<?php
	include_once("../conn.php");

	// download_btn - 下载数据导入模板
	if (isset($_POST['download_btn']))
	{
		$file = fopen('import/TEMPLET.rar',"r");
		// header("Content-Type: application/octet-stream");
		header("Content-Type: text/plain");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".filesize('import/TEMPLET.rar'));
		header("Content-Disposition: attachment; filename=TEMPLET.rar");
		echo fread($file,filesize('import/TEMPLET.rar'));
		fclose($file);
		exit();
	}
	
	// upload_btn - 上传数据文件
	if (isset($_POST['upload_btn']))
	{
		session_start();
		$_SESSION['FileCode'];
		$_SESSION['FileCode'] = "0";
		
		if ((!isset($_POST['download'])) && ($_FILES["file"]["type"] == "text/plain") && ($_FILES["file"]["size"] < 8192))
		{
			if ($_FILES["file"]["error"] > 0)
			{
				$_SESSION['FileCode'] = "Error: " . $_FILES["file"]["error"];
			}
			else
			{
				if (file_exists("import/" . $_FILES["file"]["name"]))
				{
					$_SESSION['FileCode'] = "× Already exists.";
				}
				else
				{
					move_uploaded_file($_FILES["file"]["tmp_name"], "import/" . $_FILES["file"]["name"]);
					$_SESSION['FileCode'] = "√ " . sprintf('%.2f', ($_FILES["file"]["size"] / 1024)) . " KiB OK!";
					
					$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','FileUpload','".$_FILES["file"]["name"]."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
					mysqli_query($conn,$sql);
				}
			}
		}
		else
		{
			$_SESSION['FileCode'] = "× Invalid File.";
		}
		header('location:index.php');
		exit();
	}
	
	// delete_btn - 删除数据文件
	if (isset($_POST['delete_btn']))
	{
		session_start();
		$file = "import/" . $_SESSION['FileName'];
		if (!unlink($file))
		{
			echo "<script>alert('数据文件删除不成功'); window.location.href='index.php';</script>";
		}
		else
		{
			$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','FileDelete','".$_SESSION['FileName']."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
			mysqli_query($conn,$sql);
			
			echo "<script>alert('数据文件已删除'); window.location.href='index.php';</script>";
		}
		exit();
	}
	
	echo "<script>alert('Illegal Access!'); window.location.href='login.php';</script>";
	exit();

?>