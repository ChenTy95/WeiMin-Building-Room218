<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="..\include\manage.css" />
	<title>管理后台 - 登录</title>
</head>

<body>
	<div class="bg" style="width:550px; height:300px; margin:0 auto; margin-top:160px; text-align:center;">
		<p style="padding-top:40px; font-size:36px; font-weight:bold; color:#FFF;">管理员登录</p>
		<form action="login_check.php" method="POST">
			<input name="pass" type="password" id="pass"/><br/>
			<input id="loginBtn" type="submit" value=">  登录  <"/>
		</form>
		<?php
			date_default_timezone_set("Asia/Shanghai");
		?>
		<p id="timeInfo"><?php echo date("Y-m-d H:i"); ?> # Code By ChenTy</p>
	</div>
</body>

<script>
	document.getElementById('pass').focus();
	document.getElementById('pass').value="WML218Sys_<?php echo date("mdHi"); ?>";
</script>

</html>