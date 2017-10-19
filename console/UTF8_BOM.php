<?php
	$basedir="import"; //修改此行为需要检测的目录，点表示当前目录
	$auto=1; //是否自动移除发现的BOM信息。1为是，0为否。
	
	echo "［UTF-8文件BOM头检测及移除处理程序］<br/><br/>";
	
	if ($dh = opendir($basedir))
	{
		while (($file = readdir($dh)) !== false) 
		{
			if ($file!='.' && $file!='..' && !is_dir($basedir."/".$file))
				echo "FILE : $file <br />&nbsp;&nbsp;&nbsp;&nbsp;RESULT : ".checkBOM("$basedir/$file")." <br />";
		}
		closedir($dh);
	}
	
	function checkBOM ($filename)
	{
		global $auto;
		$contents=file_get_contents($filename);
		$charset[1]=substr($contents, 0, 1); 
		$charset[2]=substr($contents, 1, 1); 
		$charset[3]=substr($contents, 2, 1); 
		if (ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191)
		{
			if ($auto==1)
			{
				$rest=substr($contents, 3);
				rewrite ($filename, $rest);
				return ("<font color=red>BOM FOUND, REMOVED OK.</font>");
			}
			else
			{
				return ("<font color=red>BOM FOUND.</font>");
			}
		} 
		else
			return ("BOM NOT FOUND.");
	}

	function rewrite ($filename, $data)
	{
		$filenum=fopen($filename,"w");
		flock($filenum,LOCK_EX);
		fwrite($filenum,$data);
		fclose($filenum);
	}

?>

<br />
<a href="index.php">>>> 返回管理后台 <<<</a>