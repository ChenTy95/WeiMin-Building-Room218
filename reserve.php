<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_0.css" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_1.css" disabled="true" />
	<link rel="stylesheet" type="text/css" href=".\include\common.css" />
	<title>预约详情 - 为民楼218活动室自助预约平台_Alpha</title>
</head>

<body>

<div class="ResDiv">

<?php
	include_once("conn.php");
	session_start();
	$lifeTime = 600;
    setcookie(session_name(), session_id(), time() + $lifeTime, "/");  
	
	if (!isset($_SESSION['IdToken']))
	{
		echo "<script>alert('您的登录已过期，请重新登录！'); window.location.href='login.php'; </script>";
		exit();
	}
	
	switch ($_SESSION['CSS_No'])
	{
		case 1:
			echo "<script>document.getElementsByTagName('link')[1].disabled=false;</script>";
			break;
		default:
			echo "<script>document.getElementsByTagName('link')[1].disabled=true;</script>";
			break;
	}
	
	if (!isset($_SESSION['ApplyNoToken']))
	{
		echo "<script>alert('请不要通过抓包修改数据等手段，做一些奇奇怪怪的事情哦！\\n\\nBy 防注入防得很辛苦的汤圆'); window.location.href='index.php'; </script>";
		exit();
	}
	else
	{
		$sql = "SELECT log.*, userinfo.name FROM log, userinfo WHERE log.no='".$_SESSION['ApplyNoToken']."' AND userinfo.id=(SELECT id FROM log WHERE no='".$_SESSION['ApplyNoToken']."');";
		$result = mysqli_query($conn,$sql);
		if ($num = mysqli_num_rows($result))
		{
			while ($rows = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
?>
	<div style="margin-top:10px; text-align:center;">
		<img src="./include/TopPic.jpg" width="95%" />
		<button class="TextBtn1" style="text-align:center;">预约成功</button>
		<button class="TextBtn2" style="text-align:center; height:45px; pointer-events:auto;">请持有效证件凭本页面截图到指定地点领取钥匙<br/><span style="color:#305496; font-weight:bold;">使用完毕后请关窗、断电并按时交还钥匙</span></button>
	</div>
	
	<div id="InfoDiv" style="margin-top:0px;">
		<button class="TextBtn1" style="text-align:center; margin-left:2.5%;">预约详情</button>
		<div class="InfoLabel">预约状态</div><div class="InfoText"><?php if ($rows['state']=='Reserve') { echo '已预约'; } ?></div>
		<div class="InfoLabel">预约日期</div><div class="InfoText"><?php echo $rows['date']; ?></div>
		<div class="InfoLabel">预约时间</div>
			<div class="InfoText" id="TimeStr">
				<script>
					var timeArr = new Array();
					timeStr = <?php echo "'".$rows['time']."'"; ?>;
					timeArr = timeStr.split("T");
					document.getElementById('TimeStr').innerHTML = timeArr[0] + ":00 - " + timeArr[1] + ":00";
				</script>
			</div>
		<div class="InfoLabel">预约人员</div><div class="InfoText"><?php echo $rows['name']; ?></div>
		<div class="InfoLabel">人员编号</div><div class="InfoText"><?php echo $rows['id']; ?></div>
		<div class="InfoLabel">联系电话</div><div class="InfoText"><?php echo $rows['phone']; ?></div>
		<div class="InfoLabel">借用事由</div><div class="InfoText" id="Info_Remark" style="font-size:12px;"><?php echo $rows['remark']; ?></div>
	</div>
	
	<a href="index.php"><button class="TextBtn1" style="margin-left:2.5%; text-align:center; pointer-events:auto; background-color:#1E90FF; color:#FFF;">返回主页</button></a>

<?php
			}
		}
		
	}
	
	mysqli_close($conn);

	include_once(".\include\bottom.php");

?>

</div>

</body>

</html>