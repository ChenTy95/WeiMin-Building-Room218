<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="..\include\manage.css" />
	<title>管理后台 - 登录</title>
</head>

<body>
	<div class="login_bg" style="width:550px; height:300px; margin:0 auto; margin-top:160px; text-align:center;">
		<p style="padding-top:40px; font-size:36px; font-weight:bold; color:#FFF;">管理员登录</p>
		<form action="login_check.php" method="POST" id="consoleLogin">
			<input name="adminid" type="text" class="pass" id="adminid" />
			<input name="pass" type="password" class="pass" id="pass" /><br/>
			<input id="loginBtn" type="submit" value=">  登录  <"/>
		</form>
		<?php
			date_default_timezone_set("Asia/Shanghai");
		?>
		<p id="timeInfo" style="font-size:16px; font-family:'Microsoft Yahei Light','微软雅黑','sans-serif';"><?php echo date("Y-m-d H:i"); ?> # Code By ChenTy</p>
	</div>
</body>

<script>
	
	document.getElementById('pass').focus();
	if (document.getElementById('adminid').value == "")
		document.getElementById('adminid').focus();
	document.getElementById('pass').value='wml218<?php echo date("mdHi"); ?>';
	
	
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
		var form = document.getElementById("consoleLogin");
		var AdminID = document.getElementById("adminid");
		form.onsubmit=function()
		{
			setCookie("AdminID", AdminID.value, 180);
		}
		if (!(getCookie("AdminID") == undefined))
		{
			AdminID.value = getCookie("AdminID");
		}	
	}
</script>

</html>