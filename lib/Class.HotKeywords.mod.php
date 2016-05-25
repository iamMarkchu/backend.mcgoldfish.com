<?php
/*
 * FileName: Class.HotKeywords.mod.php
 * Author: Lee
 * Create Date: 2007-5-21
 * Package: package_name
 * Project: project_name
 * Remark: 
*/
if (!defined("__CLASS_HOTKEYWORDS_MOD__"))
{
	define("__CLASS_HOTKEYWORDS_MOD__", 1);
	
	class HotKeywords
	{
		
		var $objMysql;
		
		function HotKeywords($objMysql)
		{
			$this->objMysql = $objMysql;
		}
		
   		function getKeywordListByLimitStr($limitStr="", $whereStr="", $orderStr="")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Keyword, SearchCount from hotsearchkeyword $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$id = intval($arrTmp['ID']);
   				$keyword = trim($arrTmp['Keyword']);
   				$cnt = intval($arrTmp['SearchCount']);
   				$arr[$id] = array($keyword, $cnt);
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   				
   		function getKeywordCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from hotsearchkeyword $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
	}
}
?>
