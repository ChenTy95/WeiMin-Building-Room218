<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_0.css" />
	<link rel="stylesheet" type="text/css" href=".\include\mobile_1.css" disabled="true" />
	<link rel="stylesheet" type="text/css" href=".\include\common.css" />
	<script type="text/javascript" src=".\include\userindex.js"></script>
	<title>主页 - 为民楼218活动室自助预约平台_Alpha</title>
</head>

<body>

<div class="ResDiv">

<?php
	include_once("conn.php");
	session_start();
	// 10分钟 600s↓
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
	
	$sql = "SELECT * FROM userinfo WHERE id='".$_SESSION['IdToken']."';";
	$result = mysqli_query($conn,$sql) OR die("<br /> Error: ".mysqli_error()."<br /> Wrong SQL: ".$sql);
	if ($num = mysqli_num_rows($result))
	{
		while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
?>
	<div style="margin-top:10px; text-align:center;">
		<img src="./include/TopPic.jpg" width="95%" />
		<button class="TextBtn1" style="pointer-events:auto;">欢迎您，<?php echo $rows['name']."［".$rows['id']."］" ?><a href="check.php" style="text-decoration:none;" onclick="return logoutConfirm();";><span id='logoutText'>［注销］</span></a></button>
		
		<input id="HiddenId" type="hidden" value="<?php echo $rows['id']; ?>" />
		<input id="HiddenName" type="hidden" value="<?php echo $rows['name']; ?>" />
		
		<button class="TextBtn2">今天是 <?php echo date("Y-m-d"); $date = date_create(date("Y-m-d"));	date_add($date,date_interval_create_from_date_string("2 days")); echo "，这是您第 ".($rows['count']+1)." 次使用本平台"; ?></button>
	</div>
<?php
		}
	}
	// 检查status表
	$sql_check = "SELECT COUNT(*) FROM status WHERE Date='".date_format($date,"Y-m-d")."';";
	$result = mysqli_query($conn,$sql_check);
	if ($num = mysqli_num_rows($result))
	{
		while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			// 更新status表
			if ($rows['COUNT(*)']==0)
			{
				$sql = "SELECT COUNT(*) FROM status WHERE Date='";
				for ($i=0; $i<=2; $i++)
				{
					$date = date_create(date("Y-m-d"));
					date_add($date,date_interval_create_from_date_string($i." days"));
					$sqli = $sql.date_format($date,"Y-m-d")."';";
					$result = mysqli_query($conn,$sqli);
					if ($num = mysqli_num_rows($result))
					{
						while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
						{
							if ($rows['COUNT(*)']==0)
							{
								$sqlnew = "INSERT INTO status(Date) VALUES ('".date_format($date,"Y-m-d")."');";
								mysqli_query($conn,$sqlnew);
							}
						}
					}
				}
			}
		}
	}
	
	$dateNo = 0;
	$sql = "SELECT * FROM status WHERE Date='".date("Y-m-d")."';";
	$result = mysqli_query($conn,$sql);
	if ($num = mysqli_num_rows($result))
	{
		while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			$dateNo = $rows['No'];
		}
	}
	
?>
	<div class="ChartDiv">
		<button class="TextBtn1" style="text-align:center;">当前预约状态</button>
		<div style="margin: 0 auto; width:95%;">
			<button class="DateBtn" style="width:16%; border-left:0px; float:left; font-size:10px; text-align:right">日期</button>
			<?php
				$sql = "SELECT * FROM status WHERE No BETWEEN '".$dateNo."' AND '".($dateNo+2)."' ;";
				$result = mysqli_query($conn,$sql);
				if ($num = mysqli_num_rows($result))
				{
					$row = 0;
					$ti = 0;
					while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{
						$row++;
						echo "<button class='DateBtn' id='".$rows['Date']."'>".substr($rows['Date'],2,8)."</button>\r\n";
					}
				}
			?>
			<button class="DateBtn" style="width:16%; border:0px; float:left; font-size:10px; text-align:left;">时间</button>
			<?php
				$result = mysqli_query($conn,$sql);
				if ($num = mysqli_num_rows($result))
				{
					$row = 0;
					while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{
						$row++;
						echo "<button class='DateBtn' style='border-top-style:dashed;'>".date("D",strtotime($rows['Date']))."</button>\r\n";
					}
				}
			?>
			<div class="TimeDiv">
				<button class="TimeBtn">08－10</button>
				<button class="TimeBtn">10－12</button>
				<button class="TimeBtn">12－14</button>
				<button class="TimeBtn">14－16</button>
				<button class="TimeBtn">16－18</button>
				<button class="TimeBtn">18－20</button>
				<button class="TimeBtn">20－22</button>
			</div>
			
			<?php
				$result = mysqli_query($conn,$sql);
				if ($num = mysqli_num_rows($result))
				{
					$row = 0;
					while ($rows = mysqli_fetch_array($result,MYSQLI_ASSOC))
					{
						$row++;
						echo "<div class='StatusDiv'>";
						for ($i=1; $i<=7; $i++)
						{
							if ($rows[(($i+3)*2)."T".((($i+3)*2)+2)]==NULL)
							{
								// 检查当日时间，确定可借时段(if 当前日期<区间上限，即可借)
								if (($row==1) and (date("G")>=($i+4)*2))
								{
									echo "<button class='StatusBtnGreyN'  onclick='hideInfo();'>不可借</button>\r\n";
								}
								else
								{
									echo "<button class='StatusBtnGreen' id='s_".$i."_".$rows['Date']."' onclick=showInfoBtn('h_".$i."_".$rows['Date']."'); >可 借</button>\r\n";
								}
							}
							else
							{
								$strinfo = $rows[(($i+3)*2)."T".((($i+3)*2)+2)];
								echo "<button class='StatusBtnGrey' id='s_".$i."_".$rows['Date']."' onclick=showInfo('h_".$i."_".$rows['Date']."');>已预约</button>\r\n";
								// 获取预约信息
								$sql_info = "SELECT log.*, userinfo.name FROM log, userinfo WHERE log.no='".substr($strinfo,0,(strpos($strinfo,'|')))."' AND log.id='".substr($strinfo,(strpos($strinfo,'|')+1),strlen($strinfo)-strpos($strinfo,'|')-1)."' AND userinfo.id='".substr($strinfo,(strpos($strinfo,'|')+1),strlen($strinfo)-strpos($strinfo,'|')-1)."';";
								// echo $sql_info;
								$result_info = mysqli_query($conn,$sql_info);
								if ($numi = mysqli_num_rows($result_info))
								{
									while ($rowsi = mysqli_fetch_array($result_info,MYSQLI_ASSOC))
									{
										// 本人预约的时段突出显示
										if ($rowsi['id']==$_SESSION['IdToken'])
										{
											echo "<script>document.getElementById('s_".$i."_".$rows['Date']."').className='StatusBtnBlue'; document.getElementById('s_".$i."_".$rows['Date']."').innerHTML='我的预约';</script>";
										}
										// 存入hidden，等待调用
										echo "<input type='hidden' id='h_".$i."_".$rows['Date']."' value='".$rowsi['no']."|".$rowsi['id']."|".$rowsi['name']."|".$rowsi['phone']."|".$rowsi['date']."|".$rowsi['time']."|".$rowsi['state']."|".$rowsi['remark']."|".$rowsi['log']."' />\r\n";
									}
								}
							}
							
						}
						echo "</div>\r\n";
					}
				}
				mysqli_close($conn);
			?>
		</div>
	</div>
	
	<form action="editStatus.php" method="POST">
	<div id="InfoDiv" style="margin-top:5px; margin-left:2.5%; display:none;">
		<button class="TextBtn1" style="text-align:center;">预约详情</button>
		<div class="InfoLabel">预约状态</div><div class="InfoText" id="Info_State"></div>
		<div class="InfoLabel">预约日期</div><div class="InfoText" id="Info_Date"></div>
		<div class="InfoLabel">预约时间</div><div class="InfoText" id="Info_Time"></div>
		<div id="visiDiv1" style="display:inline;">
			<div class="InfoLabel">预约人员</div><div class="InfoText" id="Info_Name"></div>
			<div class="InfoLabel">人员编号</div><div class="InfoText" id="Info_Id"></div>
			<div class="InfoLabel">联系电话</div><a id="PhoneNumberHref" href="tel:#"><div class="InfoText" id="Info_Phone"></div></a>
			<div class="InfoLabel">借用事由</div><div class="InfoText" id="Info_Remark" style="font-size:12px;"></div>
			
			<button name="Cancel" id="CancelBtn" class="TextBtn1" style="margin-left:2.5%; text-align:center; pointer-events:auto; background-color:#1E90FF; color:#FFF; display:none;" onclick="return cancelConfirm();">取消预约</button>
		</div>
	</div>
	
	<div id="visiDiv2" style="margin-left:2.5%; display:none;">
		<button class="TextBtn1" style="text-align:center;">预约借用</button>
		<div class="InfoLabel">预约人员</div><div class="InfoText" id="Info_Name2"></div>
		<div class="InfoLabel">人员编号</div><div class="InfoText" id="Info_Id2"></div>
		<div class="InfoLabel">联系电话</div>
		<div class="InfoText" id="Info_Phone">
			<input type="number" id="InputPhone" name="InputPhone" class="InputBox" style="font-size:14px;" value="" />
		</div>
		<div class="InfoLabel">借用事由</div>
		<div class="InfoText" id="Info_Remark" onclick="if (document.getElementById('InputRemark').value=='（15字以内）') { document.getElementById('InputRemark').value=''; }">
			<input type="text" id="InputRemark" name="InputRemark" class="InputBox" style="font-size:12px;" maxlength="15" value="（15字以内）" onblur="if (document.getElementById('InputRemark').value.trim()=='') {document.getElementById('InputRemark').value='（15字以内）';}" />
		</div>
		
		<input id="HiddenDate" name="InputDate" type="hidden" value="" />
		<input id="HiddenTime" name="InputTime" type="hidden" value="" />
		<input id="HiddenId_2" name="InputID" type="hidden" value="" />
		
		<button name="Apply" class="TextBtn1" style="margin-left:2.5%; text-align:center; pointer-events:auto; background-color:#1E90FF; color:#FFF;" onclick="return checkInfo();">信息填写完成，点此提交申请</button>
	</div>
	</form>
	
	<?php
		include_once(".\include\bottom.php");
	?>

	
</div>

</body>

<script>
function logoutConfirm()
{
	var b = window.confirm('你确定要注销当前用户［<?php echo $_SESSION['IdToken']; ?>］吗？');
	if (b==true)
	{
		return true;
	}
	return false;
}
</script>

</html>