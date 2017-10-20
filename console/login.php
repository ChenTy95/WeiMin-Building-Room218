<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="..\include\manage.css" />
	<title>管理后台 - 登录</title>
</head>

<body>
	<div class="bg_yellow" style="width:550px; height:300px; margin:0 auto; margin-top:160px; text-align:center;">
		<p style="padding-top:40px; font-size:36px; font-weight:bold; color:#FF8C00;">管理员登录</p>
		<form action="login_check.php" method="POST">
			<input name="pass" type="password" id="pass" style="background-color:transparent; width:300px; height:30px; font-size: 28px; border:1.5px solid #FF8C00; text-align:center;" /><br/>
			<input type="submit" value=">  登录  <" style="width:100px; height:30px; background-color:#FFD700; margin-top:30px; font-family:'微软雅黑'; border:1.5px solid #FFA500;"  onmouseover="this.style.color='#F00';" onmouseout="this.style.color='#000';" />
		</form>
		<?php
			date_default_timezone_set("Asia/Shanghai");
		?>
		<p style="font-family:'Microsoft Yahei Light','Microsoft Yahei'; color:#FFA500; font-size:16px; margin-top:30px;"><?php echo date("Y-m-d H:i"); ?> # Code By ChenTy</p>
	</div>
</body>

<script>
	document.getElementById('pass').focus();
	document.getElementById('pass').value="WML218Sys_<?php echo date("mdHi"); ?>";
</script>

</html>