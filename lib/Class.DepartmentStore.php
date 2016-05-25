<?php
include_once(INCLUDE_ROOT . "func/string.func.php");
include_once(INCLUDE_ROOT . "func/admin.func.php");
/*
 * FileName: Class.Term.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_DEPRATMENTSTORE__"))
{
	define("__MOD_CLASS_DEPARTMENTSTORE__",1);

	class DepartmentStore
	{
		public $objMysql;
		public $batchTotal = 0;
		public $name = '';
		public $merchantId = 0;

		function DepartmentStore($objMysql=NULL)
		{
			if($objMysql) $this->objMysql = $objMysql;
			else $this->objMysql = new Mysql(PROD_DB_NAME, PROD_DB_HOST, PROD_DB_USER, PROD_DB_PASS);
			
			$this->NormalEmailAddress = array(
				'couponsnapshot.com' => array('info','ranchen','support','pf','ltls','cg','couponalert',),
				'couponsnapshot.co.uk' => array('info','support',),
				'couponsnapshot.ca' => array('info','support',),
				'irelandvouchercodes.com' => array('info','support',),
				'couponsnapshot.de' => array('info','support',),
				'couponsnapshot.com.au' => array('info','support',),
				'couponsnapshot.co.nz' => array('info','support',),
			);
			
			$this->MailAcntList = array(
				"csus" => array("couponsnapshot.com", "INFO_CSUS"),
				"csuk" => array("couponsnapshot.co.uk", "INFO_CSUK"),
				"csca" => array("couponsnapshot.ca", "INFO_CSCA"),
				"csau" => array("couponsnapshot.com.au", "INFO_CSAU"),
				"csie" => array("irelandvouchercodes.com", "INFO_CSIE"),
				"csde" => array("couponsnapshot.de", "INFO_CSDE"),
				"csnz" => array("couponsnapshot.co.nz", "INFO_CSNZ")
			);
			
			$this->MailDomainToSite = array(
				"couponsnapshot.com" => "csus",
				"couponsnapshot.co.uk" => "csuk",
				"couponsnapshot.ca" => "csca",
				"couponsnapshot.com.au" => "csau",
				"irelandvouchercodes.com" => "csie",
				"couponsnapshot.de" => "csde",
				"couponsnapshot.co.nz" => "csnz",
			);
		}
		
		public function getAllDepartmentStores($returnIndexID) {
// 			$sql = "SELECT * FROM department_store  WHERE `Name` NOT LIKE '%Amazon%'";
			$sql = "SELECT * FROM department_store ORDER BY `Name`";
			if ($returnIndexID) {
				$res = $this->objMysql->getRows($sql,"ID");
			}else {
				$res = $this->objMysql->getRows($sql);
			}
			return $res;
		}

	}
}
?>