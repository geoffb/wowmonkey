<?php

class skin {

	public $html = "";
	public $rowSkin = "";
	public $rowHtml = "";

	function __construct($file) {
		$this->html = file_get_contents("skins/" . $file);
		$this->parse_includes();
	}

	private function parse_includes() {
		$pattern = "%include:";
		$begin = strpos($this->html, $pattern);
		while($begin !== false) {
			$end = strpos($this->html, "%", $begin + 1);
			$include = substr($this->html, $begin, $end - $begin);			
			if(strlen($include) > 0) {
				$file = substr($include, strlen($pattern));
				$this->replace($include."%", file_get_contents("skins/".$file));
			}
			else {
				break;
			}
			$begin = strpos($this->html, $pattern);
		}
	}

	function replace($find, $value) {
		$this->html = str_replace($find, $value, $this->html);
	}

	function token($token, $value) {
		$this->replace("{" . $token . "}", $value);
	}

	function flag($flag, $value) {
		$head = "#begin_flag:" . $flag . "#";
		$foot = "#end_flag:" . $flag . "#";

		if($value) {
			//the flag is set; we need to remove the begin/end flags
			$this->replace($head, "");
			$this->replace($foot, "");
		}
		else {
			//the flag is set; we need to remove this chunk of HTML
			$begin = strpos($this->html, $head);
			$len = strpos($this->html, $foot) - $begin;
			$len += strlen($foot);
			$this->html = substr_replace($this->html, "", $begin, $len);
		}
	}

	function initRow($row) {
		if(empty($this->rowSkin[$row])) {
			$head = "@begin_row:" . $row . "@";
			$foot = "@end_row:" . $row . "@";
			$begin = strpos($this->html, $head) + strlen($head);
			$len = strpos($this->html, $foot) - $begin;
			if($len > 0) {
				$this->rowSkin[$row] = substr($this->html, $begin, $len);
			}
			else {
				$this->rowSkin[$row] = "";
			}
		}
	}

	function addRow($row) {
		$this->initRow($row);
		$newRow = $this->rowSkin[$row];
		for($v = 1; $v < func_num_args(); $v++) {
			$arg = func_get_arg($v);
			$newRow = str_replace("{".$row.".".$v."}", $arg, $newRow);
		}
		if(empty($this->rowHtml[$row])) {
			$this->rowHtml[$row] = $newRow;
		}
		else {
			$this->rowHtml[$row] .= $newRow;
		}
	}

	function flushRows($row) {
		$this->initRow($row);
		if(empty($this->rowHtml[$row])) {
			$this->rowHtml[$row] = "";
		}
		//replace the row def with the row data
		$this->replace($this->rowSkin[$row], $this->rowHtml[$row]);
		$this->rowSkin[$row] = "";
		$this->rowHtml[$row] = "";
		//now get rid of the row def header and footer
		$head = "@begin_row:" . $row . "@";
		$foot = "@end_row:" . $row . "@";
		$this->replace($head, "");
		$this->replace($foot, "");
	}

	function dump() {
		//echo $this->head;
		echo $this->html;
		//echo $this->foot;
	}
}

?>
