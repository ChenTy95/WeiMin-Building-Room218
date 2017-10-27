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
			
			if ((document.getElementById('showUsers').innerHTML == '✔' && document.getElementById('Id_'+i).innerHTML !='admin') ||
			(document.getElementById('showAdmin').innerHTML == '✔' && document.getElementById('Id_'+i).innerHTML =='admin'))
			{
				if ((document.getElementById('showLogin').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'Login') ||
				(document.getElementById('showLogout').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'Logout') ||
				(document.getElementById('showReserve').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'Reserve') ||
				(document.getElementById('showCancel').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'Cancel') ||
				(document.getElementById('showUpload').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'FileUpload') ||
				(document.getElementById('showDelete').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'FileDelete') ||
				(document.getElementById('showImport').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'FileImport') ||
				(document.getElementById('showAdminLogin').innerHTML == '✔' && document.getElementById('Type_'+i).value == 'AdminLogin') )
				{
					if (parseInt(document.getElementById('No_'+i).innerHTML)<=document.getElementById('NoTo').value && parseInt(document.getElementById('No_'+i).innerHTML)>=document.getElementById('NoFrom').value)
					{
						if (document.getElementById('Id_'+i).innerHTML.indexOf(document.getElementById('IdInput').value)>=0)
						{
							if (document.getElementById('Name_'+i).innerHTML.indexOf(document.getElementById('NameInput').value)>=0)
							{
								if (document.getElementById('IP_'+i).innerHTML.indexOf(document.getElementById('IPInput').value)>=0)
								{
									if (document.getElementById('DateFrom').value != '' && document.getElementById('DateTo').value != '')
									{
										if (parseInt(document.getElementById('Time_'+i).innerHTML.replace(/-/g,"").replace(/:/,'').replace(/ /,''))<=parseInt(document.getElementById('DateTo').value.replace(/-/g,'')+"2399") && 
										parseInt(document.getElementById('Time_'+i).innerHTML.replace(/-/g,"").replace(/:/,"").replace(/ /,""))>=parseInt(document.getElementById('DateFrom').value.replace(/-/g,"")+"0000"))
										{
											document.getElementById('logdiv_'+i).style.display='inline';
										}
									}
									else
									{
										document.getElementById('logdiv_'+i).style.display='inline';
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function resetFilter()
{
	document.getElementById('NoFrom').value="1";
	document.getElementById('NoTo').value=document.getElementById("maxNo").value;
	document.getElementById('IdInput').value="";
	document.getElementById('NameInput').value="";
	document.getElementById('DateFrom').value="";
	document.getElementById('DateTo').value="";
	document.getElementById('IPInput').value="";
	document.getElementById('showUsers').innerHTML = '&#10004;';
	document.getElementById('showAdmin').innerHTML = '&#10008;';
	document.getElementById('showLogin').innerHTML = '&#10004;';
	document.getElementById('showLogout').innerHTML = '&#10008;';
	document.getElementById('showReserve').innerHTML = '&#10004;';
	document.getElementById('showCancel').innerHTML = '&#10004;';
	document.getElementById('showUpload').innerHTML = '&#10008;';
	document.getElementById('showDelete').innerHTML = '&#10008;';
	document.getElementById('showImport').innerHTML = '&#10008;';
	document.getElementById('showAdminLogin').innerHTML = '&#10008;';

	setFilter();
}

function checkBoxfunc(checkName)
{
	if (document.getElementById(checkName).innerHTML == '✘')
	{
		document.getElementById(checkName).innerHTML = '&#10004;';
		if (checkName=='showAdmin')
		{
			document.getElementById('showUpload').innerHTML = '&#10004;';
			document.getElementById('showImport').innerHTML = '&#10004;';
			document.getElementById('showDelete').innerHTML = '&#10004;';
			document.getElementById('showAdminLogin').innerHTML = '&#10004;';
		}
		if (checkName=='showUpload' || checkName=='showDelete' || checkName=='showImport' || checkName=='showAdminLogin')
		{
			document.getElementById('showAdmin').innerHTML = '&#10004;';
		}
	}
	else 
	{
		document.getElementById(checkName).innerHTML = '&#10008;';
		if (checkName=='showAdmin')
		{
			document.getElementById('showUpload').innerHTML = '&#10008;';
			document.getElementById('showImport').innerHTML = '&#10008;';
			document.getElementById('showDelete').innerHTML = '&#10008;';
			document.getElementById('showAdminLogin').innerHTML = '&#10008;';
		}
	}
		
	setFilter();
}	