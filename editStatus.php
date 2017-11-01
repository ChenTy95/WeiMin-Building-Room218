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
	include_once("conn.php");
	// 关闭错误提示
	ini_set("error_reporting","E_ALL & ~E_NOTICE");
	
	// POST过滤
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
	
	function check_inputDate()
	{
		// 日期有效性检查（防止修改至限制范围后）
		$sql = "SELECT COUNT(*) FROM status WHERE Date='".check_input($_POST['InputDate'])."';";
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				if ($rows['COUNT(*)']==0)
				{
					$allowWriteData = 0;
					echo "<script>alert('请不要通过抓包修改数据等手段，尝试预约可预订期限范围外的日期哦！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
					exit();
				}
			}
		}
	}
	
	function check_inputTime()
	{
		// 时间段有效性检查（防止写数据库错误）
		$flag = 1;
		switch (check_input($_POST['InputTime']))
		{
			case '8T10':
				$flag = 0;
				break;
			case '10T12':
				$flag = 0;
				break;
			case '12T14':
				$flag = 0;
				break;
			case '14T16':
				$flag = 0;
				break;
			case '16T18':
				$flag = 0;
				break;
			case '18T20':
				$flag = 0;
				break;
			case '20T22':
				$flag = 0;
				break;
			default:
				$flag = 1;
		}
		if ($flag==1)
		{
			$allowWriteData = 0;
			echo "<script>alert('请不要通过抓包修改数据等手段，尝试修改给定的时间段范围哦！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
			exit();
		}
	}
	
	session_start();
	$lifeTime = 600;
    setcookie(session_name(), session_id(), time() + $lifeTime, "/");  
	
	if (!isset($_SESSION['IdToken']))
	{
		echo "<script>alert('您的登录已过期，请重新登录！'); window.location.href='login.php'; </script>";
		exit();
	}
	
	// 按下预约借用按钮
	if (isset($_POST['Apply']))
	{
		global $allowWriteData;
		$allowWriteData = 1;
		
		// 日期有效性检查（防止修改至限制范围后）
		check_inputDate();
		
		// 时间段有效性检查（防止写数据库错误）
		check_inputTime();
		
		// 手机号码有效性第二次检查
		if (!(preg_match("/^1(3|4|5|7|8)\d{9}$/",check_input($_POST['InputPhone']))))
		{
			$allowWriteData = 0;
			echo "<script>alert('抓包改手机号什么的真的是好讨厌的啦！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
			exit();
		}
		
		// 学号有效性验证（卖个萌，其实没什么用，用的是session）
		if (check_input($_POST['InputID'])!=$_SESSION['IdToken'])
		{
			$allowWriteData = 0;
			echo "<script>alert('抓包改学号有什么意义啊！换个人登录不就好了吗！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
			exit();
		}
		
		// 该账号在该日的预约情况检查（使用id查询当日log中的数量，通过奇偶性判断）
		$sql = "SELECT COUNT(*) FROM log WHERE id='".$_SESSION['IdToken']."' AND date='".check_input($_POST['InputDate'])."' AND (type='Reserve' OR type='Cancel');";
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				if ($rows['COUNT(*)'] % 2 != 0)
				{
					$allowWriteData = 0;
					echo "<script>alert('你在这一天已经预约借用过了哟！每人每天只能借一次的～\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
					exit();
				}
			}
		}
		
		// 二次检查所选日期时段是否有人借用
		$sql = "SELECT * FROM status WHERE Date='".check_input($_POST['InputDate'])."';";
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				if ($rows[check_input($_POST['InputTime'])]!=NULL)
				{
					$allowWriteData = 0;
					echo "<script>alert('这个时间段已经被人借用了哦！可能是你下手晚了一小步哟～\\n当然也可能是使用了技术手段…请不要抓包修改数据强行挤人啦！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
					exit();
				}
				// 无问题，写入数据库各表
				elseif ($allowWriteData ==1)
				{
					$sql = "INSERT INTO log(id,phone,date,time,type,remark,log) VALUES ('".$_SESSION['IdToken']."','".check_input($_POST['InputPhone'])."','".check_input($_POST['InputDate'])."','".check_input($_POST['InputTime'])."','Reserve','".check_input($_POST['InputRemark'])."','".date("ymd.Hi")."');";
					mysqli_query($conn,$sql);
					
					$sql = "SELECT no FROM log WHERE id='".$_SESSION['IdToken']."' AND date='".check_input($_POST['InputDate'])."' AND time='".check_input($_POST['InputTime'])."' AND type='Reserve' ORDER BY no DESC LIMIT 1";
					$result = mysqli_query($conn,$sql);
					if ($num = mysqli_num_rows($result))
					{
						while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
						{
							$_SESSION['ApplyNoToken'] = $rows['no'];
						}
					}
					
					$sql = "UPDATE status set ".check_input($_POST['InputTime'])."=CONCAT(".$_SESSION['ApplyNoToken'].",'|','".$_SESSION['IdToken']."') WHERE Date='".check_input($_POST['InputDate'])."';";
					mysqli_query($conn,$sql);
					
					$sql = "UPDATE userinfo SET count=count+1 WHERE id='".$_SESSION['IdToken']."';";
					mysqli_query($conn,$sql);

					echo "<script> window.location.href='reserve.php'; </script>";
					exit();
				}
			}
		}
	}
	
	// 取消预约
	if (isset($_POST['Cancel']))
	{
		global $allowWriteData;
		$allowWriteData = 1;
		
		// 日期有效性检查
		check_inputDate();
		
		// 时间段有效性检查（防止写数据库错误）
		check_inputTime();
		
		// 身份验证，确认本人取消（Session判断）
		$sql = "SELECT ".check_input($_POST['InputTime'])." FROM status WHERE Date='".check_input($_POST['InputDate'])."';";
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				$DBid = substr($rows[check_input($_POST['InputTime'])],strpos($rows[check_input($_POST['InputTime'])],"|") + 1,strlen($rows[check_input($_POST['InputTime'])])-strpos($rows[check_input($_POST['InputTime'])],"|") - 1);
				if ($DBid!=$_SESSION['IdToken'])
				{
					$allowWriteData = 0;
					echo "<script>alert('这个时间段不是你预约的啦！请不要抓包修改数据取消其他人的预约啊！\\n\\nBy 防注入防得很辛苦的汤圆'); history.go(-1); </script>";
					exit();
				}
				else
				{
					// 无问题，修改和写入数据库各表
					if ($allowWriteData==1)
					{
						$sql = "UPDATE status SET ".check_input($_POST['InputTime'])."=NULL WHERE Date='".check_input($_POST['InputDate'])."';";
						mysqli_query($conn,$sql);
						
						$DBno = substr($rows[check_input($_POST['InputTime'])],0,strpos($rows[check_input($_POST['InputTime'])],"|"));
						$sql = "UPDATE log SET log=CONCAT(log,'|Cancel') WHERE no='".$DBno."'";
						mysqli_query($conn,$sql);
						
						$sql = "INSERT INTO log(id,date,time,type,log) VALUES ('".$_SESSION['IdToken']."','".check_input($_POST['InputDate'])."','".check_input($_POST['InputTime'])."','Cancel','".date("ymd.Hi")."');";
						mysqli_query($conn,$sql);
					}
				}
			}
		}

		echo "<script>alert('取消预约成功！'); window.location.href='index.php';</script>";
		exit();
	}
	mysqli_close($conn);
?>