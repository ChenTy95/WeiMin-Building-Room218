function scrollDown()
{
	document.getElementsByTagName('BODY')[0].scrollTop = 150 * screen.width / 400  + 80;
}

function hideInfo()
{
	document.getElementById("InfoDiv").style.display = 'none';
	document.getElementById("visiDiv2").style.display = 'none';
}

function showInfo(strid)
{
	str = document.getElementById(strid).value;
	var infoArr = new Array();
	infoArr = str.split("|");
	document.getElementById("Info_State").innerHTML = '已预约';
	document.getElementById("Info_Date").innerHTML = infoArr[4];
	document.getElementById("HiddenDate").value = infoArr[4];
	document.getElementById("HiddenTime").value = infoArr[5];
	document.getElementById("Info_Time").innerHTML = infoArr[5].split("T")[0] + ":00 - " + infoArr[5].split("T")[1] + ":00";
	document.getElementById("Info_Name").innerHTML = infoArr[2];
	document.getElementById("Info_Id").innerHTML = infoArr[1];
	document.getElementById("Info_Phone").innerHTML = infoArr[3];
	document.getElementById("PhoneNumberHref").href = "tel:" + infoArr[3];
	document.getElementById("Info_Remark").innerHTML = infoArr[7];
	
	document.getElementById("InfoDiv").style.display = 'inline';
	
	document.getElementById("visiDiv1").style.display = 'inline';
	document.getElementById("visiDiv2").style.display = 'none';
	
	if (document.getElementById("HiddenId").value==infoArr[1])
	{
		document.getElementById("CancelBtn").style.display = 'inline';
	}
	else
	{
		document.getElementById("CancelBtn").style.display = 'none';
	}
	
	scrollDown();
}

function showInfoBtn(strid)
{
	var infoArr = new Array();
	infoArr = strid.split("_");
	document.getElementById("Info_State").innerHTML = '可预约';
	document.getElementById("Info_Date").innerHTML = infoArr[2];
	document.getElementById("HiddenDate").value = infoArr[2];
	document.getElementById("HiddenTime").value = ((infoArr[1] * 1 + 3) * 2) + "T" + (((infoArr[1] * 1 + 3) * 2) + 2);
	document.getElementById("Info_Time").innerHTML = ((infoArr[1] * 1 + 3) * 2) + ":00 - " + (((infoArr[1] * 1 + 3) * 2) + 2) + ":00";
	document.getElementById("Info_Name").innerHTML = '-';
	document.getElementById("Info_Id").innerHTML = '-';
	document.getElementById("Info_Phone").innerHTML = '-';
	document.getElementById("PhoneNumberHref").href = "tel:#";
	document.getElementById("Info_Remark").innerHTML = '-';
	
	document.getElementById("InfoDiv").style.display = 'inline';
	
	document.getElementById("visiDiv1").style.display = 'none';
	document.getElementById("visiDiv2").style.display = 'inline';
	document.getElementById("Info_Id2").innerHTML = document.getElementById("HiddenId").value;
	document.getElementById("HiddenId_2").value = document.getElementById("Info_Id2").innerHTML;
	document.getElementById("Info_Name2").innerHTML = document.getElementById("HiddenName").value;
	
	scrollDown();
}

function checkInfo()
{
	phone = document.getElementById("InputPhone").value;
	remark  = document.getElementById("InputRemark").value;
	if ((phone=="")||(phone=="请输入联系电话（手机）"))
	{
		alert("请输入联系电话（手机）！");
		return false;
	}
	else if (!(/^1(3|4|5|7|8)\d{9}$/.test(phone)))
	{
		alert("手机号码输入不正确，请重新输入！");
		return false;
	}
	if ((remark=="")||(remark=="请输入借用事由（15字以内）"))
	{
		alert("请输入预约借用事由！");
		return false;
	}
	return true;
}

function cancelConfirm()
{
	var weekday = new Array('星期一','星期二','星期三','星期四','星期五','星期六','星期日');
	var b = window.confirm('你真的要取消本次预约吗？\n\n预约日期：' + document.getElementById("Info_Date").innerHTML + " "+ weekday[new Date(document.getElementById("Info_Date").innerHTML).getDay() - 1]+'\n预约时间：' + document.getElementById("Info_Time").innerHTML +'\n预约事由：' + document.getElementById("Info_Remark").innerHTML);
	if (b==true)
	{
		return true;
	}
	return false;
}