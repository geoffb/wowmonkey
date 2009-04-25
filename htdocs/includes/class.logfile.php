<?php

class logfile
{
	private $data = ""; //holds the text of the file until written to the file

	function writeLine($line)
	{
		$timestamp = date("m/d/y h:i:s a", time());
		$this->data .= $timestamp." >> ".$line."\n";
	}

	function saveLog($filename, $append = true)
	{
		//todo: actually write this to the file specified
		echo(str_replace("\n", "<br />", $this->data));
	}
}

?>