function updateProgress(sMsg, iWidth) 
{ 
	document.getElementById("progress_mes").innerHTML = sMsg; 
	document.getElementById("progress_col").style.width = iWidth + "px"; 
	document.getElementById("progress_per").innerHTML = parseInt(iWidth / 64 * 100) + "%"; 
}

function hideInfo()
{
	document.getElementById("Info_No").innerHTML = "日志编号";
	document.getElementById("Info_Id").innerHTML = "证件号码";
	document.getElementById("Info_Name").innerHTML = "姓名";
	document.getElementById("Info_Phone").innerHTML = "联系电话";
	document.getElementById("Info_Date").innerHTML = "日期";
	document.getElementById("Info_Time").innerHTML = "时段";
	document.getElementById("Info_Remark").innerHTML = "借用事由";
	document.getElementById("Info_DateTime").innerHTML = "数据库访问时间";
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

function showUserInfo(userid)
{
	document.getElementById('IdInput_data').value = document.getElementById('user_id_'+userid).innerHTML;
	document.getElementById('NameInput_data').value = document.getElementById('user_name_'+userid).value;
	for (var i=0; i<=6; i++)
	{
		if (document.getElementById('identitySelector')[i].text == document.getElementById('user_identity_'+userid).innerHTML)
		{
			document.getElementById('identitySelector').selectedIndex = i;
		}
	}
}