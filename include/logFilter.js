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
				if (parseInt(document.getElementById('No_'+i).innerHTML)<=document.getElementById('NoTo').value && parseInt(document.getElementById('No_'+i).innerHTML)>=document.getElementById('NoFrom').value)
				{
					if (document.getElementById('Id_'+i).innerHTML.indexOf(document.getElementById('IdInput').value)>=0)
					{
						if (document.getElementById('Name_'+i).innerHTML.indexOf(document.getElementById('NameInput').value)>=0)
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

function resetFilter()
{
	document.getElementById('NoFrom').value="1";
	document.getElementById('NoTo').value=document.getElementById("maxNo").value;
	document.getElementById('IdInput').value="";
	document.getElementById('NameInput').value="";
	document.getElementById('DateFrom').value="";
	document.getElementById('DateTo').value="";
	
	setFilter();
}

function checkBoxfunc(checkName)
{
	if (document.getElementById(checkName).innerHTML == '✘')
	{
		document.getElementById(checkName).innerHTML = '&#10004;';
	}
	else 
	{
		document.getElementById(checkName).innerHTML = '&#10008;';
	}
	setFilter();
}	