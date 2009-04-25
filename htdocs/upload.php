<?php

include "includes/common.php";

$msg = "";
$max_size = 5000000; //maximum number of bytes that an uploaded file can be

if(isset($_FILES["datafile"]))
{
	$err = $_FILES["datafile"]["error"];
	if($err == 0)
	{
		//no error was found sending the file up
		if($fh = @fopen($_FILES["datafile"]["tmp_name"], "rb"))
		{
			//temp file was read successfully; grab the first 4 characters
			$id = strrev(fgets($fh, 5));
			fclose($fh);

			//verify that the file ident is one of the files we can parse
			if($id == "WMOB" || $id == "WIDB" || $id == "WQST")
			{
				//move the temp file to the upload directory
				$dir = "../upload/";
				$file = $dir.md5(time()).".wdb";
				if(@move_uploaded_file($_FILES["datafile"]["tmp_name"], $file))
				{
					//Yay! File was uploaded and moved successfully
					$msg = doOkMsg("File upload worked! Thank you for feeding WoW Monkey!");
				}
				else
				{
					//could not move the temp file for some reason
					$msg = doErrMsg("File upload failed! Could not move temp file.");
				}
			}
			else
			{
				//file is not with our select list
				$msg = doErrMsg("File upload failed! Invalid file.");
			}
		}
		else
		{
			//could not open the temp file for some reason
		 	$msg = doErrMsg("File upload failed! Could not read uploaded file.");
		}
	}
	else
	{
		//a file upload error occured
		$msg = "File upload failed!";
		if($err == 1 || $err == 2)
			$msg .= " Maximum file size exceeded.";
		$msg = doErrMsg($msg);
	}
}

$skin = new skin("upload.skn");

$url = "upload.php";
$skin->token("FORM_ACTION", $url);
$skin->token("MAX_FILE_SIZE", $max_size);
$skin->token("MAX_MB", $max_size / 1000000); //for display only
$skin->token("MESSAGE", $msg);

$skin->dump();

?>
