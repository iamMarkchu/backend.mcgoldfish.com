<?php
/*
 * FileName: Class.Benchmark.php
 * Author: Lee
 * Create Date: 2006-9-14
 * Package: 
 * Project: 
 * Remark: 
*/
if (!defined("__CLASS_BENCHMARK__"))
{
	define("__CLASS_BENCHMARK__", 1);
	
	class Benchmark
	{
		private $startTime;
		
		function __Construct()
		{
			$this->startTime = $this->setTime();
			$this->checkPoint = array();
		}
		
		function setCheckPoint($_file,$_line,$_name="")
		{
			$_file = @basename($_file);
			$key =  "$_file(Ln.$_line)$_name";
			$this->checkPoint[$key] = $this->getProcessTime();
		}
		
		function printCheckPoint()
		{
			$lasttime = 0;
			foreach($this->checkPoint as $k => $v)
			{
				if($v - $lasttime > 0.5) $color = "red";
				else $color = "black";
				echo "<font color=\"$color\">$k => $v &nbsp;&nbsp; ". ($v - $lasttime) . "</font>";
				echo "<br/>";
				$lasttime = $v;
			}
			print_r($this->checkPoint);
		}
		
		function setTime()
		{
			$time = explode(" ", microtime());
			$theTime = (double) $time[1] + (double) $time[0];
			return $theTime;
		}
		
		function resetTime()
		{
			$this->startTime = $this->setTime();
		}
		
		function getProcessTime($fix = 3)
		{
			$endTime = $this->setTime();
			$theTime = Round($endTime - $this->startTime, $fix);
			return $theTime;
		}
	}
}
?>