<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_0.css" />
	<link rel="stylesheet" type="text/css" href=".\include\common.css" />
	<title>开发日志 - 为民楼218活动室自助预约平台</title>
</head>

<body>

<div class="ResDiv">
	<div style="margin-top:10px; text-align:center;">
		<img src="./include/TopPic.jpg" width="95%" />
		<button class="TextBtn1" style="text-align:center;">开发日志</button>
		<button class="TextBtn2" style="height:auto; padding:5px;">
			　　如在使用中发现任何功能性或安全性问题，欢迎您与cxy95@vip.qq.com联系，不胜感激~
		
		<!-- </button> -->
		<?php
			$log = fopen("include/DevLog.log","r");
			
			while(!feof($log))
			{
				$str = trim(str_replace(PHP_EOL, '', fgets($log)));
				if (substr($str,0,1)=='[')
				{
					echo "</button>\r\n";
					echo "<button class='TextBtn1'>".substr($str,1,strlen($str)-2)."</button>\r\n";
					echo "<button class='TextBtn2' style='height:auto; padding:5px;'>\r\n";
				}	
				else
				{
					echo "    &nbsp;· ";
					if (mb_strpos($str,'（感谢')>0 || mb_strpos($str,'（特别感谢')>0)
					{
						echo mb_substr($str,0,mb_strpos($str,'（'));
						echo "<br/>\r\n";
						echo "<div style='text-align:right;'>".mb_substr($str,mb_strpos($str,'（'))."</div>\r\n";
					}
					else
						echo $str."<br/>\r\n";
				}
				
			}
				
		?>
		
	</div>
<?php
	fclose($log);
	include_once(".\include\bottom.php");
?>

</div>



</body>
</html>