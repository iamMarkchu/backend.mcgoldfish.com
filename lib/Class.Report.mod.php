<?php
/*
 * FileName: Class.Stats.mod.php
 * Author: Lee
 * Create Date: 2006-11-2
 * Package: package_name
 * Project: se
 * Remark: 
*/
if (!defined("__CLASS_STATS_MOD__"))
{
	define("__CLASS_STATS_MOD__", 1);
	
	class Stats
	{
		private $objStatsDB;
		
		public function __Construct()
		{
			$this->objStatsDB = new Mysql(TRACKING_DB_NAME, TRACKING_DB_HOST, TRACKING_DB_USER, TRACKING_DB_PASS);
		}
		
		public function getHourlyData($sql)
		{
			$sql = rtrim(trim($sql), ";");
			if($sql)
			{
				$return = array();
				$arrTmp = explode(";", $sql);//means multiple sql, just use the last one
				if(count($arrTmp))
				{
					foreach ($arrTmp as $k=>$sql)
					{
						$qryId = $this->objStatsDB->query($sql);
					}
				}
				else
				{
					$qryId = $this->objStatsDB->query($sql);
				}
				while($arrTmp = $this->objStatsDB->getRow($qryId))
				{
					$return[$arrTmp['h']] = $arrTmp['cnt'];
				}
				$this->objStatsDB->freeResult($qryId);
				return $return;
			}
			return array();
		}

		public function getSourceInclk($src="", $sTime="", $eTime="")
		{
			$sTime = ($sTime == "") ? date("Y-m-d") : $sTime;
			$eTime = ($eTime == "") ? date("Y-m-d", time()+86400) : $eTime;
			$sql = "select count(*) as cnt, source from incominglog where visittime >= '$sTime' and visittime < '$eTime'";
			if($src)
			{
				$sql .= " and source like '".addslashes($src)."' group by source";
			}
			else
			{
				$sql .= " and source <> '' group by source order by cnt desc";
			}
			$qryId = $this->objStatsDB->query($sql);
			$return = array();
			while($arrTmp = $this->objStatsDB->getRow($qryId))
			{
				$return[$arrTmp['source']] = intval($arrTmp['cnt']);
			}
			$this->objStatsDB->freeResult($qryId);
			return $return;
		}

		public function getSourceOutClk($src="", $sTime='', $eTime='')
		{
			$sTime = ($sTime == "") ? date("Y-m-d") : $sTime;
			$eTime = ($eTime == "") ? date("Y-m-d", time()+86400) : $eTime;
			$sql = "select i.source, count(*) as outClk from incominglog as i, outgoinglog as o where i.ID = o.SessionID and ";
			$sql .= "i.VisitTime >= '$sTime' and i.VisitTime < '$eTime'";
			if($src)
			{
				$sql .= " and i.source like '%".addslashes($src)."%' and o.isValid = 0 group by i.source";
			}
			else
			{
				$sql .= " and i.source <> '' and o.isValid = 0 group by i.source";
			}
			$qryId = $this->objStatsDB->query($sql);
			$return = array();
			while($arrTmp = $this->objStatsDB->getRow($qryId))
			{
				$return[$arrTmp['source']] = intval($arrTmp['outClk']);
			}
			$this->objStatsDB->freeResult($qryId);
			return $return;

		}
		
		public function getSourceCost($src="", $sTime='', $eTime='')
		{
			$sTime = ($sTime == "") ? date("Y-m-d") : $sTime;
			$eTime = ($eTime == "") ? date("Y-m-d", time()+86400) : $eTime;
			$sql = "select count(*) as cnt, source from incominglog where visittime >= '$sTime' and visittime < '$eTime'";
			if($src)
			{
				$sql .= " and source like '".addslashes($src)."' group by source";
			}
			else
			{
				$sql .= " and source <> '' group by source";
			}
			$qryId = $this->objStatsDB->query($sql);
			$return = array();
			while($arrTmp = $this->objStatsDB->getRow($qryId))
			{
				if(stripos($arrTmp['source'], "pi_mer_") !== false)
					$return[$arrTmp['source']] = intval($arrTmp['cnt'])*0.14;
			}
			$this->objStatsDB->freeResult($qryId);
			return $return;
		}

		public function getSourceRevenue($src="", $sTime='', $eTime='')
		{
			$sTime = ($sTime == "") ? date("Y-m-d") : $sTime;
			$eTime = ($eTime == "") ? date("Y-m-d", time()+86400) : $eTime;
			$sql = "select i.source, 0 as reve from incominglog as i, outgoinglog as o where i.ID = o.SessionID ";
			$sql .= " and i.VisitTime >= '$sTime' and i.VisitTime < '$eTime' ";
			if($src)
			{
				$sql .= " and i.source like '%".addslashes($src)."%' group by i.source";
			}
			else
			{
				$sql .= " and i.source <> '' group by i.source";
			}
			$qryId = $this->objStatsDB->query($sql);
			$return = array();
			while($arrTmp = $this->objStatsDB->getRow($qryId))
			{
				$return[$arrTmp['source']] = floatval($arrTmp['reve']);
			}
			$this->objStatsDB->freeResult($qryId);
			return $return;
		}
	}
}
?>