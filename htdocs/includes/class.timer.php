<?php

class timer
{
	private $start = 0;
	private $stop = 0;

	function start()
	{
		$this->start = microtime(true);
	}

	function stop()
	{
		$this->stop = microtime(true);
	}

	function taken()
	{
		return $this->stop - $this->start;
	}
}

?>