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
	document.getElementById('user_id_Origin').value = document.getElementById('user_id_'+userid).innerHTML;
	document.getElementById('IdInput_data').value = document.getElementById('user_id_'+userid).innerHTML;
	document.getElementById('user_name_Origin').value = document.getElementById('user_name_'+userid).value;
	document.getElementById('NameInput_data').value = document.getElementById('user_name_'+userid).value;
	for (var i=0; i<=6; i++)
	{
		if (document.getElementById('identitySelector')[i].text == document.getElementById('user_identity_'+userid).innerHTML)
		{
			document.getElementById('identitySelector').selectedIndex = i;
			document.getElementById('user_identity_Origin').value =document.getElementById('identitySelector')[i].value;
		}
	}
}

function identityCode(Code)
{
	switch (Code)
	{
		case "bk":
			return "本科生";
			break;
		case "szy":
			return "硕士生";
			break;
		case "by":
			return "博士生";
			break;
		case "fdy":
			return "辅导员";
			break;
		case "jg":
			return "教职工";
			break;
		case "admin":
			return "管理员";
			break;
	}
}

function checkOperation(OperationType)
{
	if (OperationType == 'Edit')
	{
		if (document.getElementById('user_id_Origin').value == "" || document.getElementById('user_name_Origin').value == "")
		{
			alert('请先选中原始数据行！');
			return false;
		}
		else if (document.getElementById('IdInput_data').value == "" || document.getElementById('NameInput_data').value == "" || document.getElementById('identitySelector').value == "#")
		{
			alert('请检查新数据合法性！');
			return false;
		}
		
		var b = window.confirm('请确认进行数据修改：\n\n原用户信息：\n【证件号码】' + document.getElementById('user_id_Origin').value + '，【姓名】' + document.getElementById('user_name_Origin').value + '，【用户类别】' + identityCode(document.getElementById('user_identity_Origin').value) + '\n\n新用户信息：\n【证件号码】'  + document.getElementById('IdInput_data').value + '，【姓名】' + document.getElementById('NameInput_data').value + '，【用户类别】' + identityCode(document.getElementById('identitySelector').value));
		if (b == true)
		{
			return true;
		}
		return false;
	}
	
	if (OperationType == 'Add')
	{
		if (document.getElementById('IdInput_data').value == "" || document.getElementById('NameInput_data').value == "" || document.getElementById('identitySelector').value == "#")
		{
			alert('请检查新数据合法性！');
			return false;
		}
		
		var b = window.confirm('请确认进行数据新增：\n\n用户信息：\n【证件号码】' + document.getElementById('IdInput_data').value + '，【姓名】' + document.getElementById('NameInput_data').value + '，【用户类别】' + identityCode(document.getElementById('identitySelector').value));
		if (b == true)
		{
			return true;
		}
		return false;
	}
	
	if (OperationType == 'Delete')
	{
		if (document.getElementById('user_id_Origin').value == "" || document.getElementById('user_name_Origin').value == "")
		{
			alert('请先选中原始数据行！');
			return false;
		}
		
		var b = window.confirm('请确认进行数据删除：\n\n原用户信息：\n【证件号码】' + document.getElementById('user_id_Origin').value + '，【姓名】' + document.getElementById('user_name_Origin').value + '，【用户类别】' + identityCode(document.getElementById('user_identity_Origin').value));
		if (b == true)
		{
			return true;
		}
		return false;
	}
}