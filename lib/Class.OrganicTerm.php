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
if (!defined("__MOD_CLASS_TERM__"))
{
	define("__MOD_CLASS_TERM__",1);

	class OrganicTerm
	{
		public $objMysql;
		public $batchTotal = 0;
		public $keywords = array();
		public $statistics = array();

		function OrganicTerm($objMysql=NULL)
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
		
		public function setAlias($data) {
			$sql="SELECT * FROM `organic_term` WHERE OrganicTerm ='".$data["organicterm"]."'";
			$queryid=$this->objMysql->query($sql);
			$count=$this->objMysql->getNumRows($queryid);
			$this->objMysql->freeResult($queryid);
			if ($count > 0) {
				$sql="UPDATE 
						  `organic_term` 
						SET
						  TermId = ".$data["termid"].",
						  Term = '".$data["term"]."',
						  Result = 'Alias' 
						WHERE OrganicTerm = '".$data["organicterm"]."' ";
				$this->objMysql->query($sql);
			}else {
				$date=date("Y-m-d H:i:s");
				$sql="INSERT INTO `organic_term` VALUES ('".$data["organicterm"]."',".$data["termid"].",'".$data["term"]."','Alias','$date')";
				$this->objMysql->query($sql);
			}
		}
		
		function getSiteMysqlObj($site)
		{
			global $databaseInfo;
//			print_r($databaseInfo);
			if(!isset($databaseInfo) || !is_array($databaseInfo)) die("databaseInfo not found\n");
			if(!isset($this->MailAcntList[$site])) die("wrong site:$site\n");
	
			list(,$infoname) = $this->MailAcntList[$site];
			if(!isset($databaseInfo[$infoname . "_DB_NAME"])) die("database name not found\n");
			
			$db_name = $databaseInfo[$infoname . "_DB_NAME"];
			$db_host = $databaseInfo[$infoname . "_DB_HOST"];
			$db_user = $databaseInfo[$infoname . "_DB_USER"];
			$db_pass = $databaseInfo[$infoname . "_DB_PASS"];
	
			$objMysql = new Mysql($db_name,$db_host,$db_user,$db_pass);
	
			return $objMysql;
		}
		
		public function getOrganicTermInfo($where,$start=0, $limit=50,$orderby='') {
			$sql="SELECT 
					  SQL_CALC_FOUND_ROWS otb.*,ot.`Result`,ot.`Term`,
					    t.ID AS TermId,
 						t.Name
					FROM
					  `organic_term_batch` otb 
					  LEFT JOIN `organic_term` ot 
					    ON otb.`OrganicTerm` = ot.`OrganicTerm` 
					  LEFT JOIN term t
   						ON  otb.`OrganicTerm` = t.`Name`
					$where $orderby
					LIMIT $start, $limit";
			$res=$this->objMysql->getRows($sql,"ID");
			$this->batchTotal = $this->objMysql->getFoundRows();
			return $res;;
		}
		
		public function getBatch(){
			$sql="SELECT DISTINCT BatchId , BatchName FROM `organic_term_batch` ORDER BY BatchId";
			$batch=$this->objMysql->getRows($sql,"BatchId");
			return $batch;
		}
		public function uploadFile($Files, $UploadPath, $prefix = 'file')  
		{
			$TmpFileName = $prefix;
			$TmpFileName .= "_" . rand(100, 999);
			$NewFileName=$TmpFileName . "_" . $Files['name'];
			$UploadFile=$UploadPath . $NewFileName;
			if (move_uploaded_file($Files['tmp_name'], $UploadFile)) 
			{
				$FileInfo = $Files;
				$FileInfo['newname'] = $NewFileName;
				return $FileInfo;
			}
			else{
				return false;
			}
		}

		public function deleteFile($filename)
		{
			
			$delfile = $filename;
			if (!is_file($delfile)) return false;
			if (!file_exists($delfile)) return false;
			if (is_dir($delfile)) return false;
			$return = unlink($delfile);
			return $return;
		}

		function getTermFromFile($file_name)
		{
			if(!isset($this->statistics)){
				$this->statistics = array("NoMatchedCount"=>0, "MatchedCount"=>0, "TotalLine"=>0);
			}
			if(!empty($file_name) && file_exists(INCLUDE_ROOT . "data/term/" . $file_name)){
				$row = 0;
				$handle = fopen(INCLUDE_ROOT . "data/term/" . $file_name, "r");
				while($data = fgetcsv($handle, 5000)){
					$row++;
					//mb_convert_encoding($str, "UTF-8", "ASCII,UTF-8");
					if($row<=1){
						continue;
					}
					$term = trim($data[0]);
					$visits = intval(trim($data[2]));
					$addtime = trim($data[3]);
					if(!empty($term) && !empty($visits) && !empty($addtime)){
						$this->statistics["MatchedCount"]++;
						$this->keywords[$term] = array("term"=>$term, "visit"=>$visits, "addtime"=>$addtime);
					} else {
						$this->statistics["NoMatchedCount"]++;
					}
				}
			}
		}

		function importKeywordsToDb(){
			$batch_id = $this->getBatchId();
			$insert_sql = "INSERT IGNORE INTO organic_term (`OrganicTerm`, `Result`, `AddTime`) VALUES ";
			$cond = "";
			$i = 0;
			foreach($this->keywords as $key => $val){
				if($i > 100){
					$sql = $insert_sql . trim($cond, ",");
					$this->objMysql->query($sql);
					$cond = '';
				}
				$cond .= "('".addslashes($val["term"])."', 'Unknown', '".addslashes($val["addtime"])."'),";
				$i++;
			}
			if(!empty($cond)){
				$sql = $insert_sql . trim($cond, ",");
				$this->objMysql->query($sql);
				$cond = '';
			}
		}

		function importKeywordsToDb_Batch($batchname){
			$batch_id = $this->getBatchId();
			$insert_sql = "INSERT INTO organic_term_batch (`BatchId`, `BatchName`,`OrganicTerm`, `Visit`, `AddTime`) VALUES ";
			$cond = "";
			$i = 0;
			foreach($this->keywords as $key => $val){
				if($i > 100){
					$sql = $insert_sql . trim($cond, ",");
					$this->objMysql->query($sql);
					$cond = '';
				}
				$cond .= "('".$batch_id."', '".addslashes($batchname)."', '".addslashes($val["term"])."', '".addslashes($val["visit"])."', '".addslashes($val["addtime"])."'),";
				$i++;
			}
			if(!empty($cond)){
				$sql = $insert_sql . trim($cond, ",");
				$this->objMysql->query($sql);
				$cond = '';
			}
		}

		function getBatchId(){
			$sql = "SELECT MAX(BatchId) as batch_id FROM organic_term_batch";
			$res = $this->objMysql->query($sql);
			$row = $this->objMysql->getRow($res);
			return isset($row["batch_id"]) ? $row["batch_id"]+1 : 1; 
		}

		public function checkDup($batchname) {
			$sql = "SELECT * FROM `organic_term_batch` WHERE BatchName = '$batchname'";
			$qryId = $this->objMysql->query($sql);
			$countRow= $this->objMysql->getNumRows($qryId);
			return $countRow;
		}
		
		public function getSqlInsert($aData, $sTableName)
		{  
			$sSqlAction = '';
			$sSqlField	= '';
			$sSqlValue	= '';
	
			$sSqlAction = "INSERT INTO `" . $sTableName . "` ";
			foreach ($aData as $key => $value) 
			{
				$sSqlField .= "`" . addslashes($key) . "`, ";
				$sSqlValue .= "'" . addslashes($value) . "', ";
			}
			$sSqlField = preg_replace("|, $|i", '', $sSqlField);    // wipe the last comma out
			$sSqlValue = preg_replace("|, $|i", '', $sSqlValue);    // wipe the last comma out
			$sSqlQuery = $sSqlAction . '(' . $sSqlField . ') VALUES(' . $sSqlValue . ');';    // merge all to a real sql query
	
			return $sSqlQuery;
		} 


		public function getSqlUpdate($aData, $sTableName, $aConstraint)
		{
			$sSqlQuery = "UPDATE `" . $sTableName . "` SET ";
			if ('' != $aData)
			{
				foreach ($aData as $key => $value) 
				{   
					$sSqlQuery .= "`" . addslashes($key) . "` = '" . addslashes($value) . "', ";
				}
			}
			else
			{
			}
	
			$sSqlQuery = preg_replace("|, $|i", ' ', $sSqlQuery);    // wipe the last comma out
			$sSqlQuery .= "WHERE 1 ";
	
			foreach ($aConstraint as $key => $value) 
			{   
				$sSqlQuery .= "AND `" . $key . "` = '" . $value . "' ";
			}
	
			return $sSqlQuery;
	
		}
	}
}
?>