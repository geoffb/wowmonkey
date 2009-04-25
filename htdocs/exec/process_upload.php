<?php

require_once("../includes/class.logfile.php");
require_once("../includes/class.timer.php");
require_once("../includes/class.dal.php");
require_once("../includes/common.php");
require_once("../includes/BinaryParser.php");

define("VERBOSE_LOGGING", 0); //1 = Verbose logging on, 0 = off
define("WDB_EOF", "\x00\x00\x00\x00\x00\x00\x00\x00"); //the end of file marker for *.wdb files are 8 null character bytes

//define file schemas
$schema_defs["WIDB"] = "int|int|int|int|string|string|string|string|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|float|float|int|float|float|int|float|float|int|float|float|int|float|float|int|int|int|int|int|int|int|int|int|int|float|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|string|int|int|int|int|int|int|int|int|int|int|int|int|int|int";
$schema_defs["WQST"] = "int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|string|string|string|string|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|int|string|string|string|string";
$schema_defs["WMOB"] = "int|int|string|string|string|string|string|int|int|int|int|int|int|int|short";

$log = new logfile(); //global logfile object

main();

function main() {
	global $log;

	//this script can take a long time to run
	//we don't want it ending early
	set_time_limit(0);

	$timer = new timer();

	$log->writeLine("============================================");
	$log->writeLine("Begin upload processing.");

	$timer->start();

	//process all files in the upload directory
	do_process();

	$timer->stop();

	$log->writeLine("--------------------------------------------");
	$log->writeLine("End upload processing. Took ".$timer->taken()." seconds.");
	$log->writeLine("============================================");

	$log->saveLog("blah.txt");
}

function do_process() {
	global $log;

	$upload_path = "../../upload/";

	$log->writeLine("Scanning $upload_path for files...");

	$dir = opendir($upload_path);

	while($file = readdir($dir)) {
		if($file != "." && $file != "..") {
			$log->writeLine("--------------------------------------------");
			$log->writeLine("Found file: ".$file);
			$ext = stristr($file, ".");
			switch($ext) {
				case ".wdb": //WoW Database File (Binary)
					$log->writeLine("File type is WDB (Binary).");
					process_wdb($upload_path.$file);
					break;
				case ".lua": //LUA file (Text)
					$log->writeLine("File type is LUA (Text).");
					process_lua($upload_path.$file);
					break;
				default:
					//ignore the file
					$log->writeLine("Unregonized extension. Ignoring file.");
					break;
			}
		}
	}

	closedir($dir); //all done!
}

//processes a .WDB file
function process_wdb($file) {
	global $schema_defs;
	global $log;

	$num_recs = 0;

	//open the file for binary reading
	$fh = fopen($file, "rb");

	//select schema
	$sig = strrev(fread($fh, 4));
	$schema = split("[|]", $schema_defs[$sig]);

	$log->writeLine("File sig: $sig");

	//read other useless info
	fread($fh, 16);

	$bp = new BinaryParser;

	//grab each record in the file based on the schema
	//and store it inside an arrary
	while(!feof($fh)) {
		$eof = fread($fh, 8);
		if($eof == WDB_EOF) {
			//end of file
			$log->writeLine("Found *.wdb EOF pattern. Exiting.");
			break;
		}
		else {
			//reset the cursor position
			fseek($fh, -8, SEEK_CUR);
		}

		$num_recs++;

		$record = array();
		$r = 0;
		foreach($schema as $type) {
			$r++;
			$val = "";
			switch($type) {
				case "int":
					$val = bread_int($bp, $fh);
					break;
				case "float":
					$val = bread_float($bp, $fh);
					break;
				case "short":
					$val = bread_short($bp, $fh);
					break;
				case "string":
					$val = bread_string($fh);
					break;
			}
			$record[$r] = $val;
		}
		switch($sig) {
			case "WIDB":
				if(VERBOSE_LOGGING == 1)
					$log->writeLine("Saving item: ".$record[5]);
				save_item($record);
				break;
			case "WQST":
				save_quest($record);
				break;
			case "WMOB":
				save_mob($record);
				break;
		}
	}

	//close the file
	fclose($fh);

	$log->writeLine("$num_recs records processed.");

	//TODO: delete the file if successful
	if(unlink($file)) {
		$log->writeLine("File unlinked!");
	}
	else {
		$log->writeLine("Could not unlink file!!!");
	}

}

//processes a .LUA file
function process_lua($file) {
	global $log;

	// *** NOT YET IMPLEMENTED ***
	$log->writeLine("LUA Parsing is not yet implemented. Slacker...");
}

//reads an integer from a binary file
function bread_int($parser, $file) {
	$data = fread($file, 4);
	$value = $parser->toInt($data);
	return $value;
}

//reads a float from a binary file
function bread_float($parser, $file) {
	$data = fread($file, 4);
	$value = $parser->toFloat($data);
	return $value;
}

//reads a short from a binary file
function bread_short($parser, $file) {
	$data = fread($file, 2);
	$value = $parser->toShort($data);
	return $value;
}

//reads a null-terminated string from a binary file
function bread_string($file) {
	$strTmp = "";
	$chrTmp = "";

	$chrTmp = fread($file, 1);

	while($chrTmp !== "\x00") {
		$strTmp = $strTmp.$chrTmp;
		$chrTmp = fread($file, 1);
	}

	return $strTmp;
}

?>
