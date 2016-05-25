<?php
/*
 * FileName: Class.Tag.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_PARTNER__"))
{
   define("__MOD_CLASS_PARTNER__",1);
   
   class Partner
   {
   		var $objMysql;
   		
   		function Partner($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getPartnerCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from partner $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
   		function getPartnerListByLimitStr($limitStr="", $whereStr="", $orderStr=" AddTime ASC ")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Domain, Title, URL, Description, Logo, IsActive, AddTime, LastChangeTime  from partner $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['domain'] = trim($arrTmp['Domain']);
   				$arr[$i]['title'] = trim($arrTmp['Title']);
   				$arr[$i]['url'] = trim($arrTmp['URL']);
   				$arr[$i]['desc'] = trim($arrTmp['Description']);
   				$arr[$i]['logo'] = trim($arrTmp['Logo']);
   				$arr[$i]['isactive'] = trim($arrTmp['IsActive']);
   				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
   				$arr[$i]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   }
}
?>