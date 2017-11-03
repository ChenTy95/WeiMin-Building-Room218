<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_0.css" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_1.css" disabled="true" />
	<link rel="stylesheet" type="text/css" href=".\include\common.css" />
	<title>登录 - 为民楼218活动室自助预约平台_Alpha</title>
</head>

<body>
<div class="ResDiv">
	<div style="margin-top:10px; text-align:center;">
		<img src="./include/TopPic.jpg" width="95%" />
		<button class="TextBtn1" style="text-align:center;">欢迎使用218活动室自助预约平台</button>
		<button class="TextBtn2" style="text-align:center;">IP地址：<?php echo getenv('REMOTE_ADDR'); ?>　登录有效时间10分钟</button>
	</div>

<form action="check.php" method="POST" id="loginForm" onsubmit="return checkInput();">
	<button class="TextBtn1" style="text-align:center; margin-left:2.5%;">请输入凭据以登录</button>
	<div class="InfoLabel" style="width:45%;">学号 / 工作证号</div>
	<div class="InfoText"  style="width:50%;">
		<input id="UserID" name="ID" class="InputBox" style="height:25px; width:80%;" value="" onblur="document.getElementById('UserID').value = document.getElementById('UserID').value.toUpperCase();"/>
	</div>
	<div class="InfoLabel" style="width:45%;">姓　　名</div>
	<div class="InfoText"  style="width:50%;">
		<input type="text" id="UserName" name="Name" class="InputBox" style="height:25px; width:80%;" value="" />
	</div>
	
	<div class="InfoLabel" style="width:45%;">界面风格</div>
	<div class="InfoText"  style="width:50%;">
		<button type="button" class="cssTag" style="width:48%; background-color:#305496; margin-top:0px;" onclick="changeCSS(0);">北欧极简</button>
		<button type="button" class="cssTag" style="width:48%; margin-left:4%;" onclick="changeCSS(1);">五色炫彩</button>
	</div>
	
	<input type="hidden" name="CSS_No" id="CSS_No" value="0"/>
	
	<button class="TextBtn1" style="margin-left:2.5%; text-align:center; pointer-events:auto; background-color:#1E90FF; color:#FFF;">登　　录</button>
</form>

<?php
	session_start();
	$lifeTime = 600;
    setcookie(session_name(), session_id(), time() + $lifeTime, "/");
	
	$_SESSION['ipInfo'] = getenv('REMOTE_ADDR');
	$_SESSION['sysInfo'] = $_SERVER['HTTP_USER_AGENT'];

	include_once(".\include\bottom.php");
?>

</div>

</body>

<script>
	function checkInput()
	{
		userID=document.getElementById("UserID");
		userName=document.getElementById("UserName");
		
		if (userID.value=="")
		{
			alert("学号/工作证号不得为空，请检查！");
			document.forms[0].elements["UserID"].focus();
			return false;
		}
		
		if (userName.value=="")
		{
			alert("姓名不得为空，请检查！");
			document.forms[0].elements["UserName"].focus();
			return false;
		}
		
		return true;
	}
	
	function changeCSS(CSS_No)
	{
		if (CSS_No==0)
		{
			document.getElementsByTagName("link")[1].disabled=true;
			document.getElementById("CSS_No").value=0;
		}
		if (CSS_No==1)
		{
			document.getElementsByTagName("link")[1].disabled=false;
			document.getElementById("CSS_No").value=1;
		}
	}
	
	// 写入及读取cookie
	function setCookie(strName, strValue, strDay)
	{
		var oDate = new Date();
		oDate.setDate(oDate.getDate()+strDay);
		document.cookie = strName + "=" + strValue + ";expires=" + oDate;
	}
	
	function getCookie(strName)
	{
		var arr = document.cookie.split("; ");
		for (var i=0; i<arr.length; i++)
		{
			var arr2 = arr[i].split("=");
			if (arr2[0]==strName)
			{
				return arr2[1];
			}			
		}
	}
	
	window.onload = function()
	{
		var form = document.getElementById("loginForm");
		var UserID = document.getElementById("UserID");
		form.onsubmit=function()
		{
			setCookie("UserID", UserID.value, 30);
			setCookie("UserCSS", document.getElementById("CSS_No").value, 30);
		}
		if (!(getCookie("UserID") == undefined))
		{
			UserID.value = getCookie("UserID");
		}
		changeCSS(getCookie("UserCSS"));
	}
</script>

</html>