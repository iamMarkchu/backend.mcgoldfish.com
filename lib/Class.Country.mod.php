<?php
/*
 * FileName: Class.Topic.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_COUNTRY__"))
{
	define("__MOD_CLASS_COUNTRY__",1);
	include_once(INCLUDE_ROOT . "func/front.func.php");
	class Country
	{
		private $objMysql;
		private $name;
		private $displayName;
		/**
		 * 
		 * @var 0 for Homepage , >0 for TopicPage ;由于topic表中的name 字段与Country表中的Name字段不一致，因此使用TopicID作为外键关联
		 */
		private $topicID;
		private $addTime;
		
		private $isNull = false;

		function __construct($objMysql)
		{
			$this->objMysql = $objMysql;

		}


		function getCountryListByLimitStr($limitStr="", $whereStr="", $orderStr="")
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select `Code` Name,`Name` DisplayName from data_dictionary $whereStr $orderStr $limitStr";
//			echo $sql;
			$res = $this->objMysql->getRows($sql);
			return $res;
		}
		
		function getAllCountry() {
			$res = $this->getCountryListByLimitStr('','Pid = 16','name');
			return $res; 
		}
		
	}
}
?>
