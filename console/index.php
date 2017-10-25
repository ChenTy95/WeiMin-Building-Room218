<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="..\include\manage.css" />
	<title>管理后台 - 为民楼218活动室自助预约平台</title>
</head>

<script>
	function updateProgress(sMsg, iWidth) 
	{ 
		document.getElementById("progress_mes").innerHTML = sMsg; 
		document.getElementById("progress_col").style.width = iWidth + "px"; 
		document.getElementById("progress_per").innerHTML = parseInt(iWidth / 64 * 100) + "%"; 
	}
	
	function hideInfo()
	{
		document.getElementById("Info_No").innerHTML = "Log ID";
		document.getElementById("Info_Id").innerHTML = "学/证号";
		document.getElementById("Info_Name").innerHTML = "姓名";
		document.getElementById("Info_Phone").innerHTML = "联系电话";
		document.getElementById("Info_Date").innerHTML = "Date";
		document.getElementById("Info_Time").innerHTML = "Time";
		document.getElementById("Info_Remark").innerHTML = "活动室借用事由";
		document.getElementById("Info_DateTime").innerHTML = "数据库记录时间";
	}
	
	function showInfo(strid)
	{
		str = document.getElementById(strid).value;
		var infoArr = new Array();
		infoArr = str.split("|");
		document.getElementById("Info_No").innerHTML = "No. " + infoArr[0];
		document.getElementById("Info_Id").innerHTML = infoArr[1];
		document.getElementById("Info_Name").innerHTML = infoArr[2];
		document.getElementById("Info_Phone").innerHTML = infoArr[3];
		document.getElementById("Info_Date").innerHTML = infoArr[4];
		document.getElementById("Info_Time").innerHTML = infoArr[5].split("T")[0] + "-" + infoArr[5].split("T")[1];
		document.getElementById("Info_Remark").innerHTML = infoArr[7];
		var dt = new Array();
		dt = infoArr[8].split("");
		document.getElementById("Info_DateTime").innerHTML = dt[0]+dt[1]+"-"+dt[2]+dt[3]+"-"+dt[4]+dt[5]+" "+dt[7]+dt[8]+":"+dt[9]+dt[10];
	}
	
</script>

<body>

<?php
	include_once("../conn.php");
	session_start();
	
	if (!isset($_SESSION['T']))
	{
		echo "<script>alert('非法登录！'); window.location.href='login.php'; </script>";
		exit();
	}
	
	if (intval(date("mdHi")) > intval($_SESSION["T"]))
	{
		echo "<script>alert('登录已超时，请重新登录！\\n注：登录有效期为20分钟'); window.location.href='login.php'; </script>";
		exit();
	}
	
?>
<div style="width:1200px; height:570px; margin:0 auto; margin-top:20px;">
	<div class="Topic">
		欢迎您，管理员！您本次的登录时间是<?php echo date("Y-m-d H:i"); ?>，此次登录有效期至<?php echo substr($_SESSION['T'],4,2).":".substr($_SESSION['T'],6,2); ?>。
		<span style="font-weight:bold;">数据无价，请谨慎操作！</span> 
		注意：本页面需启用php_mbstring.dll。
		<a href="login_check.php" style="text-decoration:none;">
			<span style="float:right; margin-right:15px; color:#C7F2E7;">[退出系统]</span>
		</a>
	</div>
	
	<!-- 左侧借用情况日历表 -->
	<div style="width:830px; height:504px; margin-top:20px; border-right:2px solid; border-bottom:2px solid; border-color:#011935; float:left;">
	<div>
		<button class="ChartTop" style="width:80px;">日期</button>
		<button class='ChartTop' style="width:50px;">星期</button>
		<button class='ChartTop'>08:00-10:00</button>
		<button class='ChartTop'>10:00-12:00</button>
		<button class='ChartTop'>12:00-14:00</button>
		<button class='ChartTop'>14:00-16:00</button>
		<button class='ChartTop'>16:00-18:00</button>
		<button class='ChartTop'>18:00-20:00</button>
		<button class='ChartTop'>20:00-22:00</button>
	</div>
	
	<div> 
	<?php
		for ($date=-8; $date<=2; $date++)
		{
			if ($date!=0)
			{
				$isToday = 0;
			}
			else
			{
				$isToday = 1;
			}
			echo "<button class='DateBtn";
				if ($isToday==1) echo "HL";
				echo "'>" . substr(date("Y-m-d",strtotime($date." day")),2) . "</button>\r\n";
			echo "<button class='WeekDayBtn";
				if ($isToday==1) echo "HL";
				echo "'>" . date("D",strtotime($date." day")) . ".</button>\r\n";

			$sql =  "SELECT COUNT(*) FROM status WHERE Date='".date("Y-m-d",strtotime($date." day"))."';";
			$result = mysqli_query($conn,$sql);
			if ($num = mysqli_num_rows($result))
			{
				while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
				{
					if ($rows['COUNT(*)']==0)
					{
						// 该日在数据库中无数据
						for ($i=4; $i<=10; $i++)
						{
							echo "<button class='InfoBtn";
								if ($isToday==1) echo "HL";
								echo "' onclick=hideInfo();>-</button>\r\n";
						}
					}
					else
					{
						$sql = "SELECT * FROM status WHERE Date='".date("Y-m-d",strtotime($date." day"))."';";
						$result = mysqli_query($conn,$sql);
						if ($num = mysqli_num_rows($result))
						{
							while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
							{
								for ($i=4; $i<=10; $i++)
								{
									if ($rows[($i*2)."T".($i*2+2)]!=NULL)
									{			
										$strinfo = $rows[($i*2)."T".($i*2+2)];
										$sql_info = "SELECT log.*, userinfo.name FROM log, userinfo WHERE log.no='".substr($strinfo,0,(strpos($strinfo,'|')))."' AND log.id='".substr($strinfo,(strpos($strinfo,'|')+1),strlen($strinfo)-strpos($strinfo,'|')-1)."' AND userinfo.id='".substr($strinfo,(strpos($strinfo,'|')+1),strlen($strinfo)-strpos($strinfo,'|')-1)."';";
										// echo $sql_info;
										$result_info = mysqli_query($conn,$sql_info);
										if ($numi = mysqli_num_rows($result_info))
										{
											while ($rowsi = mysqli_fetch_array($result_info,MYSQLI_ASSOC))
											{
												echo "<input type='hidden' id='h_".$i."_".$rows['Date']."' value='".$rowsi['no']."|".$rowsi['id']."|".$rowsi['name']."|".$rowsi['phone']."|".$rowsi['date']."|".$rowsi['time']."|".$rowsi['state']."|".$rowsi['remark']."|".$rowsi['log']."' />\r\n";
												
												$infoStr = $rowsi['remark'];
												if (mb_strlen($infoStr)>7)
												{
													$infoStr = mb_substr($infoStr,0,4) . "…" . mb_substr($infoStr,mb_strlen($infoStr)-2,2,"utf-8");
												}
												
												// 该时段有人借用
												echo "<button class='InfoBtn";
													if ($isToday==1) echo "HL";
												echo "' id=s_".$i."_".$rows['Date']."' onclick=showInfo('h_".$i."_".$rows['Date']."'); onblur=hideInfo();>" .  $rowsi['name'] . "<br/>" . $infoStr . "</button>\r\n";
											}
										}
									}
									else											
									{
										// 该时段无人借用
										echo "<button class='InfoBtn";
											if ($isToday==1) echo "HL";
										echo "' onclick=hideInfo();>-</button>\r\n";
									}
								}
							}
						}
					}
				}
			}
		}
		
	?>
	</div>		
	</div>

	<!-- 右侧 1.预约详情 -->
	<div style="width:345px; height:auto; margin-left:23px; margin-top:20px; float:left;"> 
		<button class="rightHead">预约详情</button>
		<button class="rightColor1" id="Info_No" style="width:95px;">Log ID</button>
		<button class="rightColor2" id="Info_DateTime" style="width:160px;">数据库记录时间</button>
		<button class="rightColor1" id="Info_Name" style="width:90px;">姓名</button>
		<button class="rightColor2" id="Info_Id" style="width:125px;">学/证号</button>
		<button class="rightColor1" id="Info_Phone" style="width:130px;">联系电话</button>
		<button class="rightColor2" id="Info_Date" style="width:90px; font-size:12px;">Date</button>
		<button class="rightColor1" id="Info_Time" style="width:55px; font-size:12px;">Time</button>
		<button class="rightColor2" id="Info_Remark" style="width:200px; font-size:12px;">活动室借用事由</button>
	</div>
	
	<!-- 右侧 2.统计数据 -->
	<div style="width:345px; height:auto; margin-left:23px; margin-top:20px; float:left;"> 
		<button class="rightHead">统计数据</button>
		<button class="rightColor1" style="width:115px;">学/证号</button>
		<button class="rightColor2" style="width:70px;">次数</button>
		<button class="rightColor1" style="width:70px;">CSS</button>
		<?php
			$sql =  "SELECT * FROM userinfo ORDER BY count DESC LIMIT 3;";
			$result = mysqli_query($conn,$sql);
			if ($num = mysqli_num_rows($result))
			{
				$i = 0;
				while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
				{
					$i++;
		?>
		<button class="rightColor1" style="width:90px; <?php if ($i!=1) echo 'border-top:1.5px solid #011935;'; ?> "><?php echo $rows['name']; ?></button>
		<button class="rightColor2" style="width:115px; <?php if ($i!=1) echo 'border-top:1.5px solid #011935;'; ?> "><?php echo $rows['id']; ?></button>
		<button class="rightColor1" style="width:70px; <?php if ($i!=1) echo 'border-top:1.5px solid #011935;'; ?> "><?php echo $rows['count']; ?></button>
		<button class="rightColor2" style="width:70px; <?php if ($i!=1) echo 'border-top:1.5px solid #011935;'; ?> "><?php if ($rows['css']==0) echo "北欧"; if ($rows['css']==1) echo "炫彩"; ?></button>
		<?php
				}
			}
		?>
	</div>
	
	<!-- 右侧 3.数据导入 -->
	<div style="width:345px; height:auto; margin-left:23px; margin-top:20px; float:left;"> 
		<button class="rightHead" style="float:left;">数据导入</button>
		<form action="editFile.php" method="post" enctype="multipart/form-data" style="width:255px; float:left;">
			<div class="rightColor2" style="width:185px; float:left;">
				<input type="file" name="file" id="file" class="uploadFile" accept=".txt" />
			</div>
			<button name="upload_btn" class="submit_btn" style="width:70px; float:right;">上传</button>
		</form>
		<form action="editFile.php" method="POST" style="float:left;">
			<button name="download_btn" title="点此下载数据导入模板" class="submit_btn" style="width:90px;">下载模板</button>
		</form>
		<button class="rightColor1" style="width:100px;">En.txt &lt; 8k </button>
		<button class="rightColor2" style="width:155px; padding:1px 2px 1px 2px;">
			<?php
				if (isset($_SESSION['FileCode']) && ($_SESSION['FileCode']!='0'))
				{
					echo $_SESSION['FileCode'];
				}
				else
				{
					echo "[上传结果返回值]";
				}
			?>
		</button>
		<button class="rightColor1" style="width:90px;">文件列表</button>
		<form action="<?php echo "index.php";//htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" style="width:255px; float:right;">
			<select name="FileSelect" class="selector">
				<option value='#'>请选择文件 ...</option>
				<?php
					$filesnames = scandir(__DIR__ . "/import/");
					foreach ($filesnames as $name)
					{
						if (strripos($name, '.txt')==strlen($name)-4)
						echo "<option value='".$name."'>".$name."</option>\r\n";
					}
				?>
			</select>
			<button name="CheckFile" class="submit_btn" style="width:70px;">检查</button>
		</form>	
		
		<!-- 左侧文件检查框 -->
		<div style="width:120px; height:106px; background-color:transparent; float:left; text-align:center;">
			<!-- 文件删除按钮 -->
			<form action="editFile.php" method="POST" style="float:left;">
				<button class="delete_btn" name="delete_btn" title="点击删除当前文件" onclick="return deleteConfirm();">删</button>
			</form>
			
			<!-- 文件名 -->
			<button id="FileName" class="color2Thin" style="width:90px; padding:1px 1px 1px 1px;">
			<?php
				$_SESSION['FileName'] = "#";
				if (isset($_POST['CheckFile']) && ($_POST['FileSelect']!='#'))
				{
					$_SESSION['FileName'] = $_POST['FileSelect'];
					echo substr($_POST['FileSelect'],0,strlen($_POST['FileSelect'])-4);
				}	
				else
					echo "[文件名]";
			?>
			</button>
			<textarea rows="4" id="FileRead">
			<?php
				if (isset($_POST['CheckFile']) && ($_POST['FileSelect']!='#'))
				{
					$file = fopen("import/".$_POST['FileSelect'],"r");
					$str_Name = substr(fgets($file),6);
					// echo "Import Data Name = ".$str_Name."<br/>";
					$str_Rec = substr(fgets($file),13);
					// echo "Data Quantity = ".$str_Rec."<br/>";
					$str_Identity = substr(fgets($file),10);
					// echo "User Identity = ".$str_Identity."<br/>";
					$str = "";
					$File_Sign = 1;
					if ($str_Rec>5)
					{
						for ($i=1; $i<=$str_Rec; $i++)
						{
							$str = fgets($file);
							if (strpos($str,",")==FALSE)
								$File_Sign = 0;
							if ($i<=3) echo $str;
							if ($i==$str_Rec) echo "...\r\n".$str;
						}
					}
					else
					{
						for ($i=1; $i<=$str_Rec; $i++)
						{
							$str = fgets($file);
							if (strpos($str,",")==FALSE)
								$File_Sign = 0;
							echo $str;
						}
					}
					$str_EOFSign = fgets($file);
					if ($str_EOFSign=="[EOF.]")
						$EOF_Sign = 1;
					else
						$EOF_Sign = 0;
				}
				else
				{
					$EOF_Sign = 0;
					echo "&lt; FILE &gt;";
				}
			?>
			</textarea>
		</div>
		<!-- 右侧统计数据显示 -->
		<div>
			<!-- 数据名 + 去BOM按钮 -->
			<button id="dataFileName" class="color1Thin" style="width:155px; padding:1px;">
			<?php
				if (isset($_POST['CheckFile']))
					if (@$str_Name!="")
						echo $str_Name . '[' . intval($str_Rec) . ']';
					else
						echo "[数据文件标识名]";
				else
					echo "[数据文件标识名]";
			?>
			</button>
			<a href="UTF8_BOM.php" title="若左侧文件名显示不正常，请点击此按钮">
				<button class="color2Thin" style="width:70px; font-size:14px;">× BOM</button>
			</a>
			<!-- 导入数据库按钮 -->
			<form action="index.php" method="POST" style="float:right;">
				<button class="delete_btn" name="import_btn" id="import_btn" title="点击导入当前数据文件" style="width:70px;" onclick="return importConfirm();" disabled='true';>→ DB</button>
				<input name="HiddenName" type="hidden" id="hiddenName" />
			</form>
			<!-- 校验结果 -->
			<button class="color2Thin" style="width:155px; padding:1px 2px 1px 2px;">
			<?php
				if (isset($_POST['CheckFile']))
				{
					if (($EOF_Sign==1) && ($File_Sign==1))
					{
						echo "√ Verification OK!
						<script>
							document.getElementById('import_btn').disabled=false; 
							document.getElementById('import_btn').title='点击导入当前数据文件';
						</script>";
					}
					else
					{
						echo "× Verification Error!
						<script>
							document.getElementById('import_btn').disabled=true; 
							document.getElementById('import_btn').style.color='#333333'; 
							document.getElementById('import_btn').title='验证错误，不允许导入'; 
							document.getElementById('dataFileName').innerHTML='[数据文件标识名]';
						</script>";
					}
				}
				else
				{
					echo "[文件校验结果]
					<script>
						document.getElementById('import_btn').disabled=true; 
						document.getElementById('import_btn').style.color='#333333'; 
						document.getElementById('import_btn').title='验证错误，不允许导入'; 
						document.getElementById('dataFileName').innerHTML='[数据文件标识名]';
					</script>";
				}	
			?>
			</button>
			
			<!-- 导入状态显示 -->
			<button id="progress_mes" class="color1Thin" style="width:155px; padding:1px 2px 1px 2px;">[导入状态]</button>
			<!-- 导入进度条 -->
			<div class="color2Thin" style="width:70px; float:right; background-color:#011935;">
				<div id="progress_col" style="height:20px; width:0px; background-color:#333366; margin:3px;"></div>
				<div id="progress_per" style="height:26px; width:70px; margin-top:-26px; background-color:transparent; text-align:center; font-size:14px; font-weight:bold; line-height:26px; color:#FFF;">0%</div>
			</div>
			
			<!-- 导入详情 -->
			<button class="btn28px" style="width:155px; border-color:#333366;" onclick="document.getElementById('import_info_div').style.display='inline';">查看数据导入详情</button><!-- 日志 --><button class="btn28px" style="width:70px; border-color:#3366CC;" onclick="document.getElementById('system_log').style.display='block';">日志</button>
		</div>
		
	</div>
	
	<!-- 数据导入详情 -->
	<div id="import_info_div" class="floatDiv" style="width:230px; height:502px; float:left; margin-top:-506px; margin-left:598px; z-index:999; display:none;">
		<div class="floatTopic">数据导入详情</div>
		<button class="xBtn" onclick="document.getElementById('import_info_div').style.display='none';">×</button>
		<div id="import_info_2" style="margin-top:5px; margin-left:5px; height:60px; font-family:'Microsoft Yahei','微软雅黑','sans-serif'; font-size:14px;"></div>
		<div id="import_info" style="width:225px; height:410px; margin-left:5px; font-family:'Consolas','Microsoft Yahei','微软雅黑','sans-serif'; overflow:auto;"></div>
	</div>
</div>

<div id="system_log" class="floatDiv" style="width:1196px; height:500px; margin:0 auto; margin-top:15px; display:block;">
	<div class="floatTopic">用户数据查询修改及系统日志 #数据无价，谨慎操作！#</div>
	<button class="xBtn" onclick="document.getElementById('system_log').style.display='none';">×</button>
	<div style="width:300px; height:400px; float:left; margin-top:10px; border:2px solid #FF8C00; ">
		
	</div>
	<div style="width:880px; height:400px; float:left; margin-top:5px;">
		<div style="width:880px; height:60px; float:left; margin-top:5px;">
			<button class="logBtnDarkBlue" style="width:26px;">起</button>
				<input id="NoFrom" class="logQInput" style="width:36px;" value="1"  onfocus="document.getElementById('NoFrom').select();" />
			<button class="logBtnDarkBlue" style="width:185px; height:26px; border:2px solid #305496;">系统日志</button>
			<button class="logBtnDarkBlue" style="width:26px;">起</button>
				<input id="DateFrom" onclick="laydate();" class="logQInput" style="width:100px;" />
			<br/>
			
			<button class="logBtnDarkBlue" style="width:26px;">止</button>
				<input id="NoTo" class="logQInput" style="width:36px;" onfocus="document.getElementById('NoTo').select();"/>
			<button class="logBtnDarkBlue" style="width:26px;">号</button>
				<input id="IdInput" class="logQInput" style="width:60px;" onfocus="document.getElementById('IdInput').select();" />
			<button class="logBtnDarkBlue" style="width:26px;">名</button>
				<input id="NameInput" class="logQInput" style="width:60px;" onfocus="document.getElementById('NameInput').select();" />
			<button class="logBtnDarkBlue" style="width:26px;">止</button>
				<input id="DateTo" class="logQInput" onfocus="document.getElementById('DateTo').value=document.getElementById('DateFrom').value;" onclick="laydate();" style="width:100px;" />
			<button class="logBtnQuery" style="width:185px; height:26px; pointer-events:auto;" onclick="setFilter();">Click</button>
		</div>
		
		<button class="logBtnDarkBlue" style="width:65px; margin:5px 0px 5px 5px;">ID</button>
		<button class="logBtnDarkBlue" style="width:90px;">证件号码</button>
		<button class="logBtnDarkBlue" style="width:90px;">姓名</button>
		<button class="logBtnDarkBlue" style="width:130px;">操作时间</button>
		<button class="logBtnDarkBlue" style="width:355px;">内容</button>
		<button class="logBtnDarkBlue" style="width:110px;">IP地址</button>

		<div style="width:890px; height:367px; overflow:auto;">
			<?php
				function diffColor($stateType)
				{
					switch ($stateType)
					{
						case "Login":
							echo "Orange";
							break;
						case "Logout":
							echo "Gray";
							break;
						case "Reserve":
							echo "DarkBlue";
							break;
						case "Cancel":
							echo "Pink";
							break;
						case "FileUpload":
							echo "Blue";
							break;
						case "FileDelete":
							echo "Blue";
							break;
						case "FileImport":
							echo "Blue";
							break;
						case "AdminLogin":
							echo "Green";
							break;
					}
				}
				// $sql =  "SELECT log.*,userinfo.name FROM log,userinfo WHERE userinfo.id=log.id ORDER BY no DESC LIMIT 20;";
				$sql =  "SELECT log.*,userinfo.name FROM log,userinfo WHERE userinfo.id=log.id ORDER BY no DESC;";
				$result = mysqli_query($conn,$sql);
				$js = 0;
				if ($num = mysqli_num_rows($result))
				{
					while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{
						$js++;
						if ($js==1) echo "<input id='maxNo' type='hidden' value='".$rows['no']."' /><script>document.getElementById('NoTo').value=document.getElementById('maxNo').value;</script>";
						echo "<div id='logdiv_".$rows['no']."'>\r\n";
						echo "  <button id='No_".$rows['no']."' class='logBtn"; diffColor($rows['state']);
							echo "' style='width:65px;'>".$rows['no']."</button>\r\n";
						echo "  <button id='Id_".$rows['no']."' class='logBtn"; diffColor($rows['state']);
						echo "' style='width:90px;'>".$rows['id']."</button>\r\n";
						echo "  <button id='Name_".$rows['no']."' class='logBtn"; diffColor($rows['state']);
						echo "' style='width:90px;'>".$rows['name']."</button>\r\n";
						echo "  <button id='Time_".$rows['no']."' class='logBtn"; diffColor($rows['state']);
						echo "' style='width:130px;'>";
							if (strlen($rows['time'])==6)
								echo $rows['date']." ".substr($rows['time'],0,2).":".substr($rows['time'],2,2);
							else
								echo "20".substr($rows['log'],0,2)."-".substr($rows['log'],2,2)."-".substr($rows['log'],4,2)." ".substr($rows['log'],7,2).":".substr($rows['log'],9,2);
						echo "</button>\r\n";
						echo "  <input id='State_".$rows['no']."' type='hidden' value='".$rows['state']."' />\r\n";
						echo "  <button class='logBtn"; diffColor($rows['state']);
						echo "' style='width:355px;";
						if ($rows['state']=="Login" || $rows['state']=="AdminLogin")
						{
							echo " pointer-events:auto;' title='";
							echo $rows['remark'];
						}
						echo "'>";
							switch ($rows['state'])
							{
								case "Login":
									echo "用户登录 （风格：";
									if (substr($rows['remark'],0,strpos($rows['remark'],'|')) == "CSS=0")
										echo "北欧）[…]";
									else if (substr($rows['remark'],0,strpos($rows['remark'],'|')) == "CSS=1")
										echo "炫彩）[…]";
									else
										echo "?）[…]";
									break;
								case "Logout":
									echo "用户注销登录";
									break;
								case "Reserve":
									echo "预约：";
									echo substr($rows['date'],2)."，";
									echo substr($rows['time'],0,strpos($rows['time'],'T'))."-".substr($rows['time'],strpos($rows['time'],'T')+1);
									echo "，".$rows['remark'];
									break;
								case "Cancel":
									echo "取消预约：";
									echo substr($rows['date'],2)."，";
									echo substr($rows['time'],0,strpos($rows['time'],'T'))."-".substr($rows['time'],strpos($rows['time'],'T')+1);
									break;
								case "FileUpload":
									echo "上传数据文件 ".$rows['remark'];
									break;
								case "FileDelete":
									echo "删除数据文件 ".$rows['remark'];
									break;
								case "FileImport":
									echo "导入";
									echo substr($rows['remark'],0,strpos($rows['remark'],'|'));
									echo "，成功/失败=".substr($rows['remark'],strpos($rows['remark'],'=')+1,strrpos($rows['remark'],'|')-strpos($rows['remark'],'=')-1);
									echo "/".substr($rows['remark'],strrpos($rows['remark'],'=')+1);
									break;
								case "AdminLogin":
									echo "管理员登录 […]";
									break;
								default:
									echo "?";
							}
						echo "</button>\r\n";
						echo "  <button id='IP_".$rows['no']."' class='logBtn"; diffColor($rows['state']);
						echo "' style='width:110px;'>";
							$ip = "?";
							if (strlen($rows['time'])==6)
								$ip = substr($rows['log'],strpos($rows['log'],'|')+1);
							if ($ip=="") $ip="?";
						echo $ip."</button>\r\n";
						
						echo "</div>\r\n";
					}
				}
			?>
		</div>
	</div>
</div>

<div style="margin:0 auto; margin-top:10px; color:#011935; text-align:center; font-weight:bold;">
	控制台要求使用1366*768及以上分辨率 . Developed By 汤圆 [ 13141009 / ChenTy95 ]
</div>

<?php
	if (isset($_POST['import_btn']))
	{
		$file = fopen("import/".$_POST['HiddenName'],"r");
		$str_Name = rtrim(substr(fgets($file),6));
		$str_Rec = rtrim(substr(fgets($file),13));
		$str_Identity = rtrim(substr(fgets($file),10));
		
		echo "<script>document.getElementById('import_info_div').style.display='inline';document.getElementById('import_info_2').innerHTML='DATA NAME = ".$str_Name."<br/>TOTAL RECORD = ".$str_Rec."<br/>';</script>";
		
		// 进度条设置项
		set_time_limit(0);
		// ob_end_clean();
		$total = intval($str_Rec);
		$width = 64;
		$pix = 64 / $total;
		$progress = 0;
		
		flush();
		
		$successTot = 0;
		$recStr_Arr = array($str_Rec+1);
		$recCode_Arr = array($str_Rec+1);
		for ($i=1; $i<=$str_Rec; $i++)
		{
			$str = fgets($file);
			$user_id = substr($str,0,strpos($str,","));
			$user_name = substr($str,strpos($str,",") + 1);
			$recStr_Arr[$i] = $user_id." ".$user_name;
			
			$sql_insert = "INSERT IGNORE INTO userinfo(id,name,identity) VALUES ('".$user_id."','".$user_name."','".$str_Identity."');";
			mysqli_query($conn,$sql_insert);
			
			$affected_row = mysqli_affected_rows($conn);
			if ($affected_row==1)
			{
				$recCode_Arr[$i] = 1;
				$successTot += 1;
			}
			else
				$recCode_Arr[$i] = 0;
?>
<script>
	updateProgress("<?php echo $user_id; ?>...<?php echo $affected_row; ?>", <?php echo min(64,intval($progress)); ?>);
</script>
<?php
			flush();
			$progress += $pix;
		}
?>
<script>
	updateProgress("Finish !", 64);
</script>
<?php
		flush();
		echo "<script>document.getElementById('import_info_2').innerHTML+='INS SUCCESS = ".$successTot." , <font color=red>FAIL = ". ($str_Rec-$successTot) ."</font><br/>';</script>";
		
		$sql = "INSERT INTO log(id,date,time,state,remark,log) VALUES ('Admin','".date("Y-m-d")."','".date("His")."','FileImport','".$_POST['HiddenName']."|SUCCESS=".$successTot."|FAIL=".($str_Rec-$successTot)."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
		mysqli_query($conn,$sql);
		
		for ($i=1; $i<=$str_Rec; $i++)
		{
			if ($recCode_Arr[$i] != 1)
			{
				echo "<script>document.getElementById('import_info').innerHTML+='<font color=red>[FAIL] ".rtrim($recStr_Arr[$i])."</font><br/>';</script>";
			}
			else
			{
				echo "<script>document.getElementById('import_info').innerHTML+='[ OK ] ".rtrim($recStr_Arr[$i])."<br/>';</script>";
			}
		}

	}
?>

</body>	
	
<script src="..\include\laydate\laydate.js"></script>
<script>
	;!function(){
		laydate({
			elem: '#demo'
		})
	}();
	
	function deleteConfirm()
	{
		if (document.getElementById("FileName").innerHTML=="&lt; NAME &gt;")
		{
			alert("当前未选择数据文件");
			return false;
		}
		else
		{
			var b = window.confirm('你确认要删除现存数据文件 [ <?php echo $_SESSION['FileName']; ?> ] 吗？');
			if (b==true)
			{
				return true;
			}
			return false;
		}
	}
	
	function importConfirm()
	{
		var b = window.confirm('你确认要将数据文件 [ <?php echo $_SESSION['FileName']; ?> ] 中的数据导入用户数据表吗？\n\n本数据文件共包含数据记录 [ <?php if (isset($str_Rec)) echo intval($str_Rec); else echo "---" ?> ] 条');
		document.getElementById("hiddenName").value = '<?php echo $_SESSION['FileName']; ?>';
		if (b==true)
		{
			return true;
		}
		return false;
	}
	
	function setFilter()
	{
		document.getElementById('NoFrom').value=document.getElementById('NoFrom').value.trim();
		document.getElementById('NoTo').value=document.getElementById('NoTo').value.trim();
		document.getElementById('IdInput').value=document.getElementById('IdInput').value.trim();
		document.getElementById('NameInput').value=document.getElementById('NameInput').value.trim();
		// document.getElementById('NoFrom').value=document.getElementById('NoFrom').value.trim();
		// document.getElementById('NoFrom').value=document.getElementById('NoFrom').value.trim();
		for (var i=1; i<=document.getElementById('maxNo').value; i++)
		{
			if (document.getElementById('logdiv_'+i) != null) 
			{
				document.getElementById('logdiv_'+i).style.display='none';
				
				if (parseInt(document.getElementById('No_'+i).innerHTML)<=document.getElementById('NoTo').value && parseInt(document.getElementById('No_'+i).innerHTML)>=document.getElementById('NoFrom').value)
				{
					if (document.getElementById('Id_'+i).innerHTML.indexOf(document.getElementById('IdInput').value)>=0)
					{
						document.getElementById('logdiv_'+i).style.display='inline';
					}
				}		
						
			}
		}
	}
</script>

</html>