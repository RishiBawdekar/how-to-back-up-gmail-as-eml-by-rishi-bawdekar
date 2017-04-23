<?php

if (isset($_POST['glabelPHP']) && isset($_POST['pagePHP']))
{
	$dir = $_POST['glabelPHP'] . '/PageNo_' . $_POST['pagePHP']; // give name of gmail label
	
	if( is_dir($dir) === false )
	{
		mkdir($dir, 0777, true);
	}
	
	if (isset($_POST['fileNamePHP']))
	{
		$file_to_write = $_POST['fileNamePHP'] . '.eml';
		
		$filecheck = file_exists($dir . '/' . $file_to_write); // will be true if file exists, false if not
		$mycount = 0;
		
		while ($filecheck) // while $filecheck is true i.e. while file does not exist
		{
			if ($mycount == 10) {break;}
			$mycount++;
			$file_to_write = $_POST['fileNamePHP'] . '(' . $mycount . ').eml';
			$filecheck = file_exists($dir . '/' . $file_to_write); 
		}
		
		$file = fopen($dir . '/' . $file_to_write, "w") or die('fopen failed');;
		
		if (isset($_POST['emailPHP']))
		{
			fwrite($file, $_POST['emailPHP']);
			
			fclose($file);
			
			if (isset($_POST['totalmsgPHP']))
			{
				$mc = $_POST['totalmsgPHP'];
				if ($mc > 1)
				{
					$msg = $mc . " messages saved.";
				} else
				{
					$msg = $mc . " message saved.";
				}
				echo $msg;
			}
		}
	}
}
?>
