<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="..\include\manage.css" />
	<script src="..\include\logFilter.js"></script>
	<script src="..\include\console.js"></script>
	<title>管理后台 - 为民楼218活动室自助预约平台</title>
</head>

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
		欢迎您，<?php echo $_SESSION['AdminName']; ?>！您本次的登录时间是<?php echo date("Y-m-d H:i"); ?>，此次登录有效期至<?php echo substr($_SESSION['T'],4,2).":".substr($_SESSION['T'],6,2); ?>。
		<span style="font-weight:bold;">数据无价，谨慎操作！</span> 
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
												echo "<input type='hidden' id='h_".$i."_".$rows['Date']."' value='".$rowsi['no']."|".$rowsi['id']."|".$rowsi['name']."|".$rowsi['phone']."|".$rowsi['date']."|".$rowsi['time']."|".$rowsi['type']."|".$rowsi['remark']."|".$rowsi['log']."' />\r\n";
												
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
	<div class="rightDiv1" style="width:345px; height:auto; margin-left:23px; margin-top:20px; float:left;"> 
		<button class="rightHead">预约详情</button>
		<button class="rightColor1" id="Info_No" style="width:95px;">日志编号</button>
		<button class="rightColor2" id="Info_DateTime" style="width:160px;">数据库访问时间</button>
		<button class="rightColor1" id="Info_Name" style="width:90px;">姓名</button>
		<button class="rightColor2" id="Info_Id" style="width:125px;">证件号码</button>
		<button class="rightColor1" id="Info_Phone" style="width:130px;">联系电话</button>
		<button class="rightColor2" id="Info_Date" style="width:90px; font-size:12px;">日期</button>
		<button class="rightColor1" id="Info_Time" style="width:55px; font-size:12px;">时段</button>
		<button class="rightColor2" id="Info_Remark" style="width:200px; font-size:12px;">借用事由</button>
	</div>
	
	<!-- 右侧 2.数据导入 -->
	<div class="rightDiv2" style="width:345px; height:auto; margin-left:23px; margin-top:10px; float:left;"> 
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
			
			<!-- 文件预览 -->
			<textarea rows="4" id="FileRead"><?php
				if (isset($_POST['CheckFile']) && ($_POST['FileSelect']!='#'))
				{
					$file = fopen("import/".$_POST['FileSelect'],"r");
					$str_Name = str_replace(PHP_EOL, '', substr(fgets($file),6));
					$str_Rec = str_replace(PHP_EOL, '', substr(fgets($file),13));
					$str_Identity = str_replace(PHP_EOL, '', substr(fgets($file),10));
					
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
					{
						if ($str_Identity=="bk" || $str_Identity=="szy" || $str_Identity=="by" || $str_Identity=="fdy" || $str_Identity=="jg")
							$EOF_Sign = 1;
						else
							$EOF_Sign = 0;
					}
					else
						$EOF_Sign = 0;
				}
				else
				{
					$EOF_Sign = 0;
					echo "&lt; FILE &gt;";
				}
			?>	</textarea>
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
			<a class="delBOM" href="UTF8_BOM.php" title="若左侧文件名显示不正常，请点击此按钮">
				<button class="color2Thin">× BOM</button>
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
			<div class="color2Thin" style="width:70px; background-color:#011935; position:absolute; margin-left:275px; margin-top:-26px;">
				<div id="progress_col" style="height:20px; width:0px; background-color:#333366; margin:3px;"></div>
				<div id="progress_per" style="height:26px; width:70px; margin-top:-26px; background-color:transparent; text-align:center; font-size:14px; font-weight:bold; line-height:26px; color:#FFF;">0%</div>
			</div>
			
			<!-- 导入详情 -->
			<button class="btn28px" style="width:155px; border-color:#333366;" onclick="document.getElementById('import_info_div').style.display='inline';">查看数据导入详情</button><!-- 日志 --><button class="btn28px" style="width:70px; border-color:#3366CC;" onclick="document.getElementById('system_log').style.display='block';">日志</button>
		</div>
		
	</div>
	
	<!-- 右侧 3.用户数据 -->
	<div class="rightDiv3" style="width:341px; height:184px; margin-left:23px; margin-top:10px; float:left; border:2px solid #011935;"> 
		<button class="rightHead" style="width:88px; height:30px;">用户数据</button>
		<?php		
			echo "<div class='data_radius' style='text-align:center;'>";
			$sql = "SELECT SUM(CASE WHEN type='Reserve' THEN 1 ELSE 0 END) AS RESERVE, SUM(CASE WHEN type='Cancel' THEN 1 ELSE 0 END) AS CANCEL, SUM(CASE WHEN type='Login' THEN 1 ELSE 0 END) AS LOGIN FROM log;";
			$result = mysqli_query($conn,$sql);
			if ($num = mysqli_num_rows($result))
			{
				while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
				{
					echo "<button class='logBtnDarkBlue' style='margin-top:3px; margin-left:0px;' >预约 ".$rows['RESERVE']."</button>";
					echo "<button class='logBtnPink' style='margin-left:5px;'>取消 ".$rows['CANCEL']."</button>";
					echo "<button class='logBtnOrange' style='margin-left:5px;'>用户登录 ".$rows['LOGIN']."</button>";
				}
			}
			echo "</div><br/>";
			echo "<div class='data_radius' style='text-align:center;'>";
			$sql =  "SELECT SUM(CASE WHEN identity='bk' THEN 1 ELSE 0 END) AS _BK, SUM(CASE WHEN identity='szy' THEN 1 ELSE 0 END) AS _SZY, SUM(CASE WHEN identity='by' THEN 1 ELSE 0 END) AS _BY, SUM(CASE WHEN identity='admin' THEN 1 ELSE 0 END) AS ADMIN, SUM(CASE WHEN identity='fdy' THEN 1 ELSE 0 END) AS FDY, SUM(CASE WHEN identity='jg' THEN 1 ELSE 0 END) AS JG FROM userinfo;";
			$result = mysqli_query($conn,$sql);
			if ($num = mysqli_num_rows($result))
			{
				while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
				{
					echo "<button class='logBtnDarkBlue' style='margin-top:2px;' >本 ".$rows['_BK']."</button>";
					echo "<button class='logBtnOrange' style='margin-left:5px;'>硕 ".$rows['_SZY']."</button>";
					echo "<button class='logBtnPink' style='margin-left:5px;'>博 ".$rows['_BY']."</button>";
					echo "<button class='logBtnBlue' style='margin-left:5px;'>辅 ".$rows['FDY']."</button>";
					echo "<button class='logBtnGray' style='margin-left:5px;'>教 ".$rows['JG']."</button>";
					echo "<button class='logBtnGreen' style='margin-left:5px;'>管理 ".$rows['ADMIN']."</button>";
				}
			}
			echo "</div>";
		?>
		
		<form action="userData.php" method="POST">
			<div class="optBorder2" style="width:76px; height:18px; margin-bottom:2px;">
				<input name="user_id_Origin" id="user_id_Origin" type="hidden" value="" />";
				<button class="optBlueTip18" disabled="disabled">号</button>
				<input name="user_id" id="IdInput_data" class="logQInput" style="width:58px; height:18px;" onfocus="document.getElementById('IdInput_data').select();"/>
			</div>
			<div class="optBorder2" style="width:68px; height:18px; margin-bottom:2px;">
				<input name="user_name_Origin" id="user_name_Origin" type="hidden" value="" />";
				<button class="optBlueTip18" disabled="disabled">名</button>
				<input name="user_name" id="NameInput_data" class="logQInput" style="width:50px; height:18px;" onfocus="document.getElementById('NameInput_data').select();"/>
			</div>
			<input name="user_identity_Origin" id="user_identity_Origin" type="hidden" value="" />";
			<select name="user_identity" id="identitySelector">
				<option value="#">请选择</option>
				<option value="bk">本科生</option>
				<option value="szy">硕士生</option>
				<option value="by">博士生</option>
				<option value="fdy">辅导员</option>
				<option value="jg">教职工</option>
				<option value="admin">管理员</option>
			</select>
			<button name="user_query" class="logBtnQuery" style="height:22px; width:25px;">查</button>
			<button name="user_edit" class="logBtnQuery" style="height:22px; width:25px;" onclick="return checkOperation('Edit');">改</button>
			<button name="user_add" class="logBtnQuery" style="height:22px; width:25px;" onclick="return checkOperation('Add');">增</button>
			<button name="user_del" class="logBtnQuery" style="height:22px; width:25px;" onclick="return checkOperation('Delete');">删</button>
		</form>
		<div style="height:95px; overflow:auto;">
		<?php
			function userColor($identityType)
			{
				switch ($identityType)
				{
					case "szy":
						echo "Orange";
						break;
					case "jg":
						echo "Gray";
						break;
					case "bk":
						echo "DarkBlue";
						break;
					case "by":
						echo "Pink";
						break;
					case "fdy":
						echo "Blue";
						break;
					case "admin":
						echo "Green";
						break;
				}
			}
			
			function showUserData($totNum)
			{
				for ($i=0; $i<=$totNum; $i++)
				{
					echo "<button id='user_id_".$_SESSION['id'][$i]."' class='logBtn";
						userColor($_SESSION['identity'][$i]);
					echo "' style='min-height:22px; width:80px'>".$_SESSION['id'][$i]."</button>\r\n";
					
					echo "<input id='user_name_".$_SESSION['id'][$i]."' type='hidden' value='".$_SESSION['name'][$i]."'/>\r\n";
					$username = "";
					if (mb_strlen($_SESSION['name'][$i])<=4)
						$username = $_SESSION['name'][$i];
					else
						$username = mb_substr($_SESSION['name'][$i],0,2)."…".mb_substr($_SESSION['name'][$i],mb_strlen($_SESSION['name'][$i])-1);
					echo "<button class='logBtn";	userColor($_SESSION['identity'][$i]);
					echo "' style='min-height:22px; width:72px'>".$username."</button>\r\n";
					
					echo "<button id='user_identity_".$_SESSION['id'][$i]."' class='logBtn";
						userColor($_SESSION['identity'][$i]);
					echo "' style='min-height:22px; width:65px'>";
					switch ($_SESSION['identity'][$i])
					{
						case "bk":
							echo "本科生";
							break;
						case "szy":
							echo "硕士生";
							break;
						case "by":
							echo "博士生";
							break;
						case "fdy":
							echo "辅导员";
							break;
						case "jg":
							echo "教职工";
							break;
						case "admin":
							echo "管理员";
							break;
					}
					echo "</button>\r\n";
					
					echo "<button class='logBtnQuery' style='height:22px; width:25px;' onclick=\"showUserInfo('".$_SESSION['id'][$i]."');\">选</button>";
					
					echo "<button id='user_css_".$_SESSION['id'][$i]."' class='logBtn";
						userColor($_SESSION['identity'][$i]);
					echo "' style='min-height:22px; width:25px'>";
					switch ($_SESSION['css'][$i])
					{
						case 0:
							echo "简";
							break;
						case 1:
							echo "彩";
							break;
					}
					echo "</button>\r\n";
					
					echo "<button class='logBtn";	userColor($_SESSION['identity'][$i]);
					echo "' style='min-height:22px; width:35px'>".$_SESSION['count'][$i]."</button>\r\n";
					
					echo "<br/>\r\n";
				}
			}
			
			if (!isset($_SESSION['user_data_state']))
				$_SESSION['user_data_state'] = "Default";
			
			function user_data_default()
			{
				global $conn;
				$sql = "SELECT * FROM userinfo ORDER BY count DESC LIMIT 4;";
				$result = mysqli_query($conn, $sql);
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
				showUserData($_SESSION['user_data_totNum']);
			}
			
			if ($_SESSION['user_data_state'] == "Default")
			{
				user_data_default();
			}
			
			if ($_SESSION['user_data_state'] == 'Query')
			{
				showUserData($_SESSION['user_data_totNum']);
			}
			
			function returnIdntity($identityCode)
			{
				switch ($identityCode)
				{
					case "bk":
						echo "本科生";
						break;
					case "szy":
						echo "硕士生";
						break;
					case "by":
						echo "博士生";
						break;
					case "fdy":
						echo "辅导员";
						break;
					case "jg":
						echo "教职工";
						break;
					case "admin":
						echo "管理员";
						break;
				}
			}
			
			if ($_SESSION['user_data_state'] == 'Edit')
			{
				showUserData($_SESSION['user_data_totNum']);
				echo "<script>setTimeout('";
				echo "alert(`用户数据修改结果：";
				if ($_SESSION['editFlag'] == 1)
					echo "【 成功 】";
				else
					echo "【 失败 】";
				echo "\\n\\n";
				echo "原用户信息：\\n【证件号码】".$_SESSION['id_old']."，【姓名】".$_SESSION['name_old']."，【用户类别】";
				returnIdntity($_SESSION['identity_old']);
				echo "\\n\\n新用户信息：\\n【证件号码】".$_SESSION['id_new']."，【姓名】".$_SESSION['name_new']."，【用户类别】";
				returnIdntity($_SESSION['identity_new']);
				echo "`)',250); </script>";
			}
			
			if ($_SESSION['user_data_state'] == 'Add')
			{
				if ($_SESSION['addFlag'] == 1)
					showUserData($_SESSION['user_data_totNum']);
				else
					user_data_default();
				echo "<script>setTimeout('";
				echo "alert(`用户数据新增结果：";
				if ($_SESSION['addFlag'] == 1)
					echo "【 成功 】";
				else
					echo "【 失败 】";
				echo "\\n\\n";
				echo "用户信息：\\n【证件号码】".$_SESSION['id_new']."，【姓名】".$_SESSION['name_new']."，【用户类别】";
				returnIdntity($_SESSION['identity_new']);
				echo "`)',250); </script>";
			}
			
			if ($_SESSION['user_data_state'] == 'Delete')
			{
				user_data_default();
				echo "<script>setTimeout('";
				echo "alert(`用户数据删除结果：";
				if ($_SESSION['delFlag'] == 1)
					echo "【 成功 】";
				else
					echo "【 失败 】";
				echo "\\n\\n";
				echo "原用户信息：\\n【证件号码】".$_SESSION['id_old']."，【姓名】".$_SESSION['name_old']."，【用户类别】";
				returnIdntity($_SESSION['identity_old']);
				echo "`)',250); </script>";
			}
			
		?>
		</div>
	</div>
	
	<!-- 数据导入详情 Div -->
	<div id="import_info_div" class="floatDiv" style="width:230px; height:502px; float:left; margin-top:-506px; margin-left:598px; z-index:999; display:none;">
		<div class="floatTopic">数据导入详情</div>
		<button class="xBtn" onclick="document.getElementById('import_info_div').style.display='none';">×</button>
		<div id="import_info_2" style="margin-top:5px; margin-left:5px; height:60px; font-family:'Microsoft Yahei','微软雅黑','sans-serif'; font-size:14px;"></div>
		<div id="import_info" style="width:225px; height:410px; margin-left:5px; font-family:'Consolas','Microsoft Yahei','微软雅黑','sans-serif'; overflow:auto; font-size:16px; line-height:18px;"></div>
	</div>
	
	<!-- 系统日志 Div -->
	<div id="system_log" class="floatDiv" style="width:900px; height:502px; margin:0 auto; margin-top:20px; margin-left:80px; display:none; position:absolute;">
		<div id="system_logTitle" class="floatTopic" style="cursor:move;">系统日志</div>
		<button class="xBtn" onclick="document.getElementById('system_log').style.display='none';">×</button>

		<!-- 系统日志查询 -->
		<div style="width:880px; height:auto; float:left; margin-top:5px; margin-left:10px;">
			<!-- 过滤器 -->
			<div class="filterOption" style="height:60px; float:left; margin-top:2px;">
				<!-- 过滤器第一行 -->
				<div class="optBorder2" style="width:61px;">
					<button class="optBlueTip" disabled="disabled">起</button>
					<input id="NoFrom" class="logQInput" style="width:37px;" value="1" onfocus="document.getElementById('NoFrom').select();" onblur="setFilter();" />
				</div>
				<div class="optBorder4">
					<button class="checkBox" id="showUsers" onclick="checkBoxfunc('showUsers');">&#10004;</button>
					<button class="checkTxt" disabled="disabled">显示用户</button>
				</div>
				<div class="optBorder4" style="border-color:#008000;">
					<button class="checkBox" id="showAdmin" onclick="checkBoxfunc('showAdmin');" style="border-color:#008000; color:#008000;">&#10008;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#008000; background-color:#008000;">显示管理</button>
				</div>
				<div class="optBorder2" style="width:126px;">
					<button class="optBlueTip" style="width:24px;" disabled="disabled">起</button>
					<input id="DateFrom" onclick="laydate();" class="logQInput" style="width:102px;" title="注意：日期填写或修改后需手动点击【查询】" />
				</div>
				<div class="optBorder4" style="border-color:#D2691E;">
					<button class="checkBox" id="showLogin" onclick="checkBoxfunc('showLogin');" style="border-color:#D2691E; color:#D2691E;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#D2691E; background-color:#D2691E;">用户登录</button>
				</div>
				<div class="optBorder4" style="width:68px;">
					<button class="checkBox" id="showReserve" onclick="checkBoxfunc('showReserve');">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="width:48px;">预约</button>
				</div>
				<div class="optBorder4" style="border-color:#4682B4;">
					<button class="checkBox" id="showUpload" onclick="checkBoxfunc('showUpload');" style="border-color:#4682B4; color:#4682B4;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#4682B4; background-color:#4682B4;">文件上传</button>
				</div>
				<div class="optBorder4" style="border-color:#4682B4;">
					<button class="checkBox" id="showImport" onclick="checkBoxfunc('showImport');" style="border-color:#4682B4; color:#4682B4;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#4682B4; background-color:#4682B4;">文件导入</button>
				</div>
				
				
				
				<button class="logBtnQuery" style="width:60px; height:26px; pointer-events:auto;" onclick="setFilter();">查询</button>
				<button class="logBtnQuery" style="width:47px; height:26px; pointer-events:auto;" onclick="resetFilter();">重置</button>
				<br/>
				
				<!-- 过滤器第二行 -->
				<div class="optBorder2" style="width:61px;">
					<button class="optBlueTip" disabled="disabled">止</button>
					<input id="NoTo" class="logQInput" style="width:37px;"
						onfocus="document.getElementById('NoTo').select();"
						onblur="if (document.getElementById('NoTo').value=='') {document.getElementById('NoTo').value=document.getElementById('maxNo').value; } setFilter();"
					/>
				</div>
				<div class="optBorder2" style="width:86px;">
					<button class="optBlueTip" disabled="disabled">号</button>
					<input id="IdInput" class="logQInput" style="width:62px;" onfocus="document.getElementById('IdInput').select();" onblur="setFilter()" />
				</div>
				<div class="optBorder2" style="width:86px;">
					<button class="optBlueTip" disabled="disabled">名</button>
					<input id="NameInput" class="logQInput" style="width:62px;" onfocus="document.getElementById('NameInput').select();" onblur="setFilter()" />
				</div>
				<div class="optBorder2" style="width:126px;">
					<button class="optBlueTip" disabled="disabled">止</button>
					<input id="DateTo" class="logQInput" onfocus="if (document.getElementById('DateTo').value == '') {document.getElementById('DateTo').value=document.getElementById('DateFrom').value;}" onclick="laydate();" title="注意：日期填写或修改后需手动点击【查询】" style="width:102px;" />
				</div>
				<div class="optBorder4" style="border-color:#696969;">
					<button class="checkBox" id="showLogout" onclick="checkBoxfunc('showLogout');" style="border-color:#696969; color:#696969;">&#10008;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#696969; background-color:#696969;">用户登出</button>
				</div>
				<div class="optBorder4" style="width:68px; border-color:#C1194E;">
					<button class="checkBox" id="showCancel" onclick="checkBoxfunc('showCancel');" style="border-color:#C1194E; color:#C1194E;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="width:48px; border-color:#C1194E; background-color:#C1194E;">取消</button>
				</div>
				<div class="optBorder4" style="border-color:#4682B4;">
					<button class="checkBox" id="showDelete" onclick="checkBoxfunc('showDelete');" style="border-color:#4682B4; color:#4682B4;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#4682B4; background-color:#4682B4;">文件删除</button>
				</div>
				<div class="optBorder4" style="border-color:#4682B4;">
					<button class="checkBox" id="showDataEdit" onclick="checkBoxfunc('showDataEdit');" style="border-color:#4682B4; color:#4682B4;">&#10004;</button>
					<button class="checkTxt" disabled="disabled" style="border-color:#4682B4; background-color:#4682B4;">数据修改</button>
				</div>
				<div class="optBorder2" style="width:106px;">
					<button class="optBlueTip" disabled="disabled">IP</button>
					<input id="IPInput" class="logQInput" style="width:82px;" onfocus="document.getElementById('IPInput').select();" onblur="setFilter()" />
				</div>
			</div>
			
			<!-- 日志表头 -->
			<button class="logBtnDarkBlue" style="width:65px; margin:0px 0px 3px 3px;">ID</button>
			<button class="logBtnDarkBlue" style="width:90px;">证件号码</button>
			<button class="logBtnDarkBlue" style="width:90px;">姓名</button>
			<button class="logBtnDarkBlue" style="width:130px;">操作时间</button>
			<button class="logBtnDarkBlue" style="width:355px;">具体操作内容</button>
			<button class="logBtnDarkBlue" style="width:110px;">IP地址</button>
			
			<!-- 日志 带滚动条 -->
			<div style="height:377px; overflow:auto;">
				<?php
					// 分级设色
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
							case "DataEdit":
								echo "Blue";
								break;
							case "AdminLogin":
								echo "Green";
								break;
						}
					}

					$sql =  "SELECT log.*,userinfo.name,userinfo.id AS userID FROM log,userinfo WHERE userinfo.id = 
					(CASE WHEN SUBSTR(log.id,1,1)='[' THEN SUBSTR(log.id,4) ELSE log.id END)
					ORDER BY no DESC;";
					$result = mysqli_query($conn,$sql);
					$js = 0;
					if ($num = mysqli_num_rows($result))
					{
						while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
						{
							$js++;
							if ($js==1) echo "<input id='maxNo' type='hidden' value='".$rows['no']."' /><script>document.getElementById('NoTo').value=document.getElementById('maxNo').value;</script>";
							echo "<div id='logdiv_".$rows['no']."'>\r\n";
							
							echo "  <button id='No_".$rows['no']."' class='logBtn"; diffColor($rows['type']);
								echo "' style='width:65px;'>".$rows['no']."</button>\r\n";
							
							echo "  <button id='Id_".$rows['no']."' class='logBtn"; diffColor($rows['type']);
							echo "' style='width:90px;'>".$rows['userID']."</button>\r\n";
							
							echo "  <button id='Name_".$rows['no']."' class='logBtn"; diffColor($rows['type']);
							echo "' style='width:90px;'>";
								if (substr($rows['id'],0,1) == '[')
									echo "[A]";
								if (mb_strlen($rows['name'])<=6)
									echo $rows['name'];
								else
									echo mb_substr($rows['name'],0,4)."…".mb_substr($rows['name'],mb_strlen($rows['name'])-1);
							echo "</button>\r\n";
							
							echo "  <button id='Time_".$rows['no']."' class='logBtn"; diffColor($rows['type']);
							echo "' style='width:130px;'>";
								if (strlen($rows['time'])==6)
									echo $rows['date']." ".substr($rows['time'],0,2).":".substr($rows['time'],2,2);
								else
									echo "20".substr($rows['log'],0,2)."-".substr($rows['log'],2,2)."-".substr($rows['log'],4,2)." ".substr($rows['log'],7,2).":".substr($rows['log'],9,2);
							echo "</button>\r\n";
							// 操作内容Type
							echo "  <input id='Type_".$rows['no']."' type='hidden' value='".$rows['type']."' />\r\n";
							// 操作内容
							echo "  <button class='logBtn"; diffColor($rows['type']);
							echo "' style='width:355px;";
							if ($rows['type']=="Login" || $rows['type']=="AdminLogin")
							{
								echo " pointer-events:auto;' title='";
								if (substr($rows['remark'],3,1) != "=") 
									echo $rows['remark'];
								else
									echo substr($rows['remark'],strpos($rows['remark'],'|')+1);
							}
							if ($rows['type']=='DataEdit')
							{
								echo " pointer-events:auto;' title='".substr($rows['remark'],strpos($rows['remark'],'|')+1);
							}
							echo "'>";
							switch ($rows['type'])
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
									echo "用户登出";
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
								case "DataEdit":
									switch (substr($rows['remark'],0,strpos($rows['remark'],'|')))
									{
										case "ADD":
											echo "<font color=#011935>【新增】</font>用户数据：";
											echo substr($rows['remark'],strpos($rows['remark'],'|')+1);
											break;
										case "EDIT":
											echo "<font color=#011935>【修改】</font>用户数据：<font color=#011935>【新】</font>";
											echo substr($rows['remark'],strpos($rows['remark'],'>')+1);
											echo " […]";
											break;
										case "DELETE":
											echo "<font color=#011935>【删除】</font>用户数据：";
											echo substr($rows['remark'],strpos($rows['remark'],'|')+1);
											break;
									}
									break;
								default:
									echo "?";
							}
							echo "</button>\r\n";
							
							echo "  <button id='IP_".$rows['no']."' class='logBtn"; diffColor($rows['type']);
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
				<script>setFilter();</script>
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript" src="../include/zxx.drag.1.0.js"></script>
<script>
	startDrag(document.getElementById('system_logTitle'),document.getElementById('system_log'));
</script>

<div style="margin:0 auto; margin-top:10px; color:#011935; text-align:center; font-weight:bold; font-size:16px;">
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
			$user_id = str_replace(PHP_EOL, '', substr($str,0,strpos($str,",")));
			$user_name = str_replace(PHP_EOL, '', substr($str,strpos($str,",") + 1));
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
		
		$sql = "INSERT INTO log(id,date,time,type,remark,log) VALUES ('[A]".$_SESSION['AdminID']."','".date("Y-m-d")."','".date("His")."','FileImport','".$_POST['HiddenName']."|SUCCESS=".$successTot."|FAIL=".($str_Rec-$successTot)."','".date("ymd")."|".getenv('REMOTE_ADDR')."');";
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
		if (document.getElementById("FileName").innerHTML.trim() == "[文件名]")
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
		if (document.getElementById('dataFileName').innerHTML.trim().substring(0,3) == "me]")
		{
			alert('检测到欲导入的数据文件存在编码问题，请您点按【× BOM】按钮进行处理后再进行导入！');
			return false;
		}
		
		var b = window.confirm('你确认要将数据文件 [ <?php echo $_SESSION['FileName']; ?> ] 中的数据导入用户数据表吗？\n\n本数据文件共包含数据记录 [ <?php if (isset($str_Rec)) echo intval($str_Rec); else echo "---" ?> ] 条');
		document.getElementById("hiddenName").value = '<?php echo $_SESSION['FileName']; ?>';
		if (b==true)
		{
			return true;
		}
		return false;
	}

</script>

</html>