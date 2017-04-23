<?php

if (isset($_POST['glabelPHP']) && isset($_POST['pagePHP']))
{
	$dir = $_POST['glabelPHP'] . '/PageNo_' . $_POST['pagePHP'] . '/Without Attachments'; // name of folder;
	
	if( is_dir($dir) === false )
	{
		mkdir($dir, 0777, true);
	}
	
	if (isset($_POST['fileNamePHP']))
	{
		$file_to_write = $_POST['fileNamePHP'] . '.eml';
		
		$filecheck = file_exists($dir . '/' . $file_to_write); // will be true if file exists, false if not
		$mycount = 0;
		
		while ($filecheck) // while $filecheck is true i.e. while file exists
		{
			if ($mycount == 10) {break;}
			$mycount++;
			$file_to_write = $_POST['fileNamePHP'] . '(' . $mycount . ').eml';
			$filecheck = file_exists($dir . '/' . $file_to_write); 
		}
		
		$file = fopen($dir . '/' . $file_to_write, "w") or die('fopen failed');;
		
		if (isset($_POST['messagedetailsPHP']))
		{
			$msgarray = json_decode($_POST['messagedetailsPHP'],true);
			
			$noOfItems = sizeof($msgarray);
			for ($i=0; $i < $noOfItems; $i++)
			{
				$content = $msgarray[$i] .PHP_EOL;
			    fwrite($file, $content);
				
			}
			fclose($file);
			
			if (isset($_POST['woattachPHP']))
			{
				$mc = $_POST['woattachPHP'];
				
				if ($mc > 1)
				{
					$msg = $mc . " messages saved without attachment.";
				} else
				{
					$msg = $mc . " message saved without attachment.";
				}
		
				echo $msg;
			}
		}
	}
}
?>
