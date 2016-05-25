<?php
include_once(INCLUDE_ROOT . "func/string.func.php");
include_once(INCLUDE_ROOT . "func/admin.func.php");
include_once(INCLUDE_ROOT . "func/front.func.php");
include_once(INCLUDE_ROOT . "func/gpc.func.php");
include_once(INCLUDE_ROOT."lib/Image.php");
include_once(INCLUDE_ROOT."lib/Class.NormalCategory.mod.php");
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

	class Term
	{
		public $objMysql;
		public $batchTotal = 0;
		public $keywords = array();
		public $statistics = array();

		function Term($objMysql=NULL)
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

		public function getTermInfo( $where, $having, $start=0, $limit=50, $orderby='', $fiteronlineeq0, $filterCountry='', $filterCategories ='' ,$filterIsSubTerm = 'All'){
			if ($fiteronlineeq0) {
				$sql = "CREATE TEMPORARY TABLE auto_c (
							TermId INT,
							PRIMARY KEY tid(TermId)
							)ENGINE=MYISAM DEFAULT CHARSET=latin1";
				$this->objMysql->query($sql);
				$sql = "Insert IGNORE into auto_c 
							SELECT 
						  t.id
						FROM
						  term t INNER JOIN 
						  termcoupon_relationship tr ON tr.termid = t.id INNER JOIN 
						  normalcoupon n ON  tr.normalcouponid = n.id 
						WHERE tr.status = 'Online' 
						  AND n.isactive = 'yes' 
						  AND  (n.ExpireTime = '0000-00-00 00:00:00' OR n.ExpireTime >= NOW())
						  AND  n.StartTime <= NOW()";
				$this->objMysql->query($sql);
				$sql = "Insert IGNORE into auto_c 
							SELECT 
						  t.id
						FROM
						  term t INNER JOIN 
						  termmanualpromotion_relationship tr ON tr.termid = t.id INNER JOIN  
						  manual_promotion n ON  tr.manualpromotionid = n.id 
						WHERE n.isactive = 'yes' 
						  AND  (n.ExpireTime = '0000-00-00 00:00:00' OR n.ExpireTime >= NOW())
						  AND  n.StartTime <= NOW()";
				$this->objMysql->query($sql);
				$where .= " AND ID NOT IN (SELECT TermId FROM auto_c)";
			}
			if ( !empty( $filterCountry ) ) {
				
				$sql = "SELECT DISTINCT TermId FROM `term_mapping` WHERE Country = '$filterCountry' ";
				$hasCountrytermIds = $this->objMysql->getRows($sql, "TermId");
				$hasCountrytermIds = array_keys($hasCountrytermIds);
			}
			if ( !empty( $filterCategories ) ) {
				$sql = "SELECT DISTINCT TermId FROM `term_category`";
				$hasCategorytermIds = $this->objMysql->getRows($sql, "TermId");
				$hasCategorytermIds = array_keys($hasCategorytermIds);
			}
			
			if ( strtoupper( $filterCategories ) == 'YES' ) {
				if (!empty($hasCountrytermIds)) {
					$termIds = array_intersect($hasCategorytermIds, $hasCountrytermIds);
					$termIds = empty($termIds)?$hasCountrytermIds:$termIds;
				}else{
					$termIds = $hasCategorytermIds;
				}
				$where .= " AND term.ID IN (".implode(',', $termIds).") ";
			}else if( strtoupper( $filterCategories ) == 'NO' ){
				if (!empty($hasCountrytermIds)) {
					$where .= " AND term.ID IN (".implode(',', $hasCountrytermIds).") ";
				}
				$where .= " AND term.ID NOT IN (".implode(',', $hasCategorytermIds).") ";
			}else {
				if (!empty($hasCountrytermIds)) {
					$where .= " AND term.ID IN (".implode(',', $hasCountrytermIds).") ";
				}
			}
			if($filterIsSubTerm == 'YES'){
				$where .= 'AND ID IN (SELECT TermId FROM `super_term_mapping`)';
			}elseif($filterIsSubTerm == 'NO'){
				$where .= 'AND ID NOT IN (SELECT TermId FROM `super_term_mapping`)';
			}
			
			$sql="SELECT SQL_CALC_FOUND_ROWS `ID` , `Name`,`Image`,`UrlName`,`Type`,`Status`,`EditorTip`,`AssignedEditor`,`Description`,`MaintainOrder`,`AddTime` ,HasMapping,HasPending,HasAlert,VisitsChange,VisitsAvg,IsPromoCntBiggerMinCnt,VoteRatingAlert,Ctr FROM `term`  LEFT JOIN term_search_filter  ON  term.ID = term_search_filter.TermId $where $having $orderby LIMIT $start,$limit";
//			echo $sql;
			$res=$this->objMysql->getRows($sql, "ID");
			$this->batchTotal = $this->objMysql->getFoundRows();
			return $res;
		}
		
		public function getMapping($termid){
			$sql="SELECT * FROM `term_mapping` WHERE `TermId` IN ($termid) ORDER BY `Order` ASC";
			$res=$this->objMysql->getRows($sql);
			return $res;
		}
		
		public function getCouponCount($termid){
			if (!$termid) {
				return array();
			}else {
				$sql="SELECT `TermId` ,Status, COUNT(*) `count` FROM `termcoupon_relationship` tr,`normalcoupon` nc WHERE tr.`Status` IN ('Online', 'Pending') AND tr.`TermId` IN ($termid) AND tr.`NormalCouponId` = nc.`ID` AND nc.IsActive='YES' AND (nc.ExpireTimeInServer = '0000-00-00 00:00:00' OR nc.ExpireTimeInServer > '".date('Y-m-d H:i:s')."' )  GROUP BY tr.Status, tr.`TermId`";
			}
// 			echo $sql;
			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				if($row["Status"]=='Online'){
					$data[$row["TermId"]]["Online"] = $row["count"];
				} elseif($row["Status"]=='Pending') {
					$data[$row["TermId"]]["Pending"] = $row["count"];
				}
			}
			$this->objMysql->freeResult($res);
			return $data;
		}
		
		public function getAllCouponCount(){
			$sql="SELECT tr.`TermId` ,Status, COUNT(*) `count` FROM `termcoupon_relationship` tr,`coupon` nc WHERE tr.`Status` IN ('online', 'pending') AND tr.`CouponId` = nc.`ID` AND nc.CsgIsActive='YES' AND (nc.CsgExpireTime = '0000-00-00 00:00:00' OR nc.CsgExpireTime > '".date('Y-m-d H:i:s')."' )  GROUP BY tr.Status, tr.`TermId`";

			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				if($row["Status"]=='online'){
					$data[$row["TermId"]]["Online"] = $row["count"];
				} elseif($row["Status"]=='pending') {
					$data[$row["TermId"]]["Pending"] = $row["count"];
				}
			}
			$this->objMysql->freeResult($res);
			return $data;
		}
		
		public function getManualPromotionCount($termid) {
			
			if (!$termid) {
					return array();
			}else {
					$sql = "SELECT 
					  `TermId`,
					   COUNT(*) `count` 
					FROM
					  `termmanualpromotion_relationship` tr,
					  `manual_promotion` mp 
					WHERE tr.`TermId` IN ($termid) 
					  AND tr.`manualpromotionid` = mp.`ID` 
					  AND mp.IsActive = 'YES' 
					  AND (
					    mp.ExpireTime = '0000-00-00 00:00:00' 
					    OR mp.ExpireTime > '".date('Y-m-d H:i:s')."'
					  ) 
					GROUP BY
					  tr.`TermId` ";
			}
			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				$data[$row["TermId"]] = $row["count"];
			}
			$this->objMysql->freeResult($res);
			return $data;
			
		}
		
		public function getAllManualPromotionCount() {
			$sql = "SELECT 
					  `TermId`,
					   COUNT(*) `count` 
					FROM
					  `termmanualpromotion_relationship` tr,
					  `manual_promotion` mp 
					WHERE tr.`manualpromotionid` = mp.`ID` 
					  AND mp.IsActive = 'YES' 
					  AND (
					    mp.ExpireTime = '0000-00-00 00:00:00' 
					    OR mp.ExpireTime > '".date('Y-m-d H:i:s')."'
					  ) 
					GROUP BY
					  tr.`TermId` ";
			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				$data[$row["TermId"]] = $row["count"];
			}
			$this->objMysql->freeResult($res);
			return $data;
			
		}
		
		public function getAllVoteCount() {
			$sql = "SELECT TermId,COUNT(*) count FROM `termcoupon_relationship` tr LEFT JOIN normalcoupon n ON tr.normalcouponid = n.id LEFT JOIN (SELECT couponid cid, MAX(IF(statsname = 'avgiswork', StatsValue, 0)) avgiswork , MAX(IF(statsname = 'isworkcount', StatsValue, 0)) isworkcount
					FROM normalcoupon_stats
					GROUP BY cid) ns ON ns.cid = tr.normalcouponid WHERE avgiswork <0.6 AND isworkcount>=5 AND n.IsActive='YES' AND n.StartTime < '".date('Y-m-d H:i:s')."' AND (n.ExpireTime = '0000-00-00 00:00:00' OR n.ExpireTime > '".date('Y-m-d H:i:s')."') AND tr.`Status` = 'Online' GROUP BY TermId";
			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				$data[$row["TermId"]] = $row["count"];
			}
			$this->objMysql->freeResult($res);
			return $data;
		}
		
		public function getVoteCountByTermId($termid) {
			$sql = "SELECT TermId,COUNT(*) count FROM `termcoupon_relationship` tr LEFT JOIN normalcoupon n ON tr.normalcouponid = n.id LEFT JOIN (SELECT couponid cid, MAX(IF(statsname = 'avgiswork', StatsValue, 0)) avgiswork , MAX(IF(statsname = 'isworkcount', StatsValue, 0)) isworkcount
					FROM normalcoupon_stats
					GROUP BY cid) ns ON ns.cid = tr.normalcouponid WHERE avgiswork <0.6 AND isworkcount>=5 AND n.IsActive='YES' AND n.StartTime < '".date('Y-m-d H:i:s')."' AND (n.ExpireTime = '0000-00-00 00:00:00' OR n.ExpireTime > '".date('Y-m-d H:i:s')."') AND tr.TermId = $termid AND tr.`Status` = 'Online' GROUP BY TermId";
			$res=$this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				$data[$row["TermId"]] = $row["count"];
			}
			$this->objMysql->freeResult($res);
			return $data;
		}
		public function getMinCouponCount($termid){
			$sql="SELECT `TermId` ,`MinCouponCount` FROM `term_extend` WHERE TermId IN ($termid)";
			$res=$this->objMysql->getRows($sql,"TermId");
			return $res;
		}
		
		public function getMappingCountByTermID($strTermId) {
			if (!$strTermId) {
				return array();
			}
			
			$sql = "SELECT TermId,COUNT(*) as count FROM term_mapping WHERE TermId IN ($strTermId) GROUP BY TermId";
			$res=$this->objMysql->getRows($sql,"TermId");
			return $res;
		}
		
		public function getDepartmentMappingbyTermids($termIds){
			if (is_array($termIds)) {
				$termIds = implode(",", $termIds);
			}
			
			if (!$termIds) {
				return  array();
			}
			
			$sql  = "SELECT * FROM term_department_mapping WHERE TermId IN ($termIds)" ; //  AND DepartmentStoreId <> 1
			$res = $this->objMysql->getRows($sql);
			return $res;
		}
		
		public function getTermById($term_id){
			$sql="SELECT * FROM `term` WHERE `ID` = $term_id";
			$res=$this->objMysql->getFirstRow($sql);
			//get extend
			$sql = "SELECT * FROM term_extend WHERE TermId = $term_id";
			$res_extend = $this->objMysql->getFirstRow($sql);
			$res = array_merge($res, $res_extend);
			return $res;
		}
		
		//ͨ得到coupon(content)查询出对应的TermId
		function getTermcouponTermId($coupon) {
			if (!$coupon) {
				return array();
			}
			$sql = "select * from termcoupon_relationship where NormalCouponId = $coupon";
			$rs =array();
			$rows = $this->objMysql->getRows($sql);
			foreach ($rows as $v) {
				$rs[] = $v["TermId"];
			}
			return $rs;
		}
		//Home Page�в�ѯ���е�ContentType��Term��content
		function getHomePageCouponTermContent()
		{
			$sql = "select Content from placement_data where Placement = 'recommend' and ContentType = 'term'";
			$rs = array();
			$rows = $this->objMysql->getRows($sql);
			foreach ($rows as $v) {
				$rs[] = $v["Content"];
			}
			return $rs;
		}
		
		//过滤条件得到content
		function getContentByPage($page) {
			$sql = "select Content from placement_data where Page = '$page' and Placement = 'recommend' and ContentType = 'term'";
			$rs = array();
			$rows = $this->objMysql->getRows($sql);
			foreach ($rows as $v) {
				$rs[] = $v["Content"];
			}
			return $rs;
		}
		//ͨ得到匹配好的ID查询出对应的name
		function getTermName($termid){
			foreach($termid as $k=>$v){
				if(empty($v)){
					unset($termid[$k]);
				}
			}
			if(empty($termid)){
				return array();
			}
			if (is_array($termid)) {
				$termid = implode(',', $termid);
			}
			if(!$termid)
			{
				return array();
			}
			$sql = "select ID,Name from term where ID in ($termid)";
			$rs = array();
			$rows = $this->objMysql->getRows($sql);
			foreach($rows as $v) {
				$rs[$v['ID']] = $v["Name"];
			}
			return $rs;
		}
		//获取权限
		function getPermissions($user,$placementType,$promotion,$page) {
			$sql = "SELECT * FROM placement_permission WHERE Editor = '$user' AND  ( page ='all'  OR ( page ='$page' AND PlacementType = '$placementType' AND Promotion = '$promotion' ) ) ";
// 			echo $sql;
			$rows = $this->objMysql->getRows($sql);
			return $rows;
		}
		//获取page权限
		function getPagePermissions($user) {
			
			$sql = "select distinct Page from placement_permission where Editor = '$user'";
			$rows = $this->objMysql->getRows($sql);
			return $rows;
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
		
		function getCSMerchantHasAffiliate($site_name, $m_id) {
			global $databaseInfo;
			$hasAffiliate = 'NO';
			if ($m_id) {
				$objCSMysql = new Mysql($databaseInfo["INFO_{$site_name}_DB_NAME"],$databaseInfo["INFO_{$site_name}_DB_HOST"],$databaseInfo["INFO_{$site_name}_DB_USER"],$databaseInfo["INFO_{$site_name}_DB_PASS"]);
				$sql = "SELECT HasAffiliate FROM `normalmerchant` WHERE ID = {$m_id}";
				$csHasAff = $objCSMysql -> getFirstRowColumn($sql);
				if ($csHasAff == 'YES') {
					$hasAffiliate = 'YES';
				}
			}
			
			return $hasAffiliate;
		}
		
		
		public function addTerm($data) {
			$data["name"] = trim($data["name"]);
			$UrlName = addslashes(trim(filter_rewrite_tags($data["name"])));
//			$imageUrl=$data["image"];
			if($_FILES["image"]['error'] == 4) //no file uploaded
			{
				if ($data["type"]=="STORE") {
					$imageSync="YES";
				}else {
					$imageSync="NO";
				}
				
				$dstFile='';
				$filename='';
			}else {
				$imageSync="NO";
				$dstFile=uploadImage('image', '', false, TERM_THUMB_PATH, "deal");
	   			$dstFile=trim($dstFile, "/");
				$img=new ImageExt();
				$img->param(TERM_THUMB_PATH.$dstFile);
				$img->thumb ( TERM_THUMB_PATH . $dstFile, TERM_IMAGE_BIG_MAX_WIDTH, TERM_IMAGE_BIG_MAX_HEIGHT, 0, 0 );
				$filename=time().rand(0, 1000).'.jpg';
				$imagesmall = $img->thumb(TERM_THUMB_PATH.$filename, TERM_IMAGE_SMALL_MAX_WIDTH, TERM_IMAGE_SMALL_MAX_HEIGHT, 0, 0);
			}
			
			
			$date=date("Y-m-d H:i:s");
			//insert database table term
			$data["ManualUploadImage"] = isset($data["ManualUploadImage"]) ? 'YES' : 'NO';
			if ($data["term_country_self"] != 'NULL' && $data["type"] == 'STORE SINGLE COUNTRY') {
				$data["term_country_self"] = "'{$data['term_country_self']}'";
			}else{
				$data["term_country_self"] = 'NULL';
			}
			
			if (!isset($data["ManualUpdateAdultStatus"]) ) {
				$data['AdultStatus'] = "NO";
			}
			$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "";
			if(!$user && substr($_SERVER["REMOTE_ADDR"],0,8) == "192.168.") $user = "couponsn";
			$sql="INSERT INTO `term` (
					  `Name`,
					  UrlName,
					  `Type`,
					  Image,
					  ImageSmall,
					  ImageSync,
					  DefaultUrl,
					  Alias,
					  Description,
					  EditorTip,
					  `Status`,
					  `AddTime`,
					  LastUpdateTime,
					  `Manual`,
					  `MaintainOrder`,
					  `OptimizeOrder`,
					  `ShowVisitLink`,
					  `ShowAFS`,
					  `ManualUploadImage`,
					  `Country`,
					  `AdultStatus`,
					  `ManualUpdateAdultStatus`,
						`AddEditor`,
						`Editor`
					) 
					VALUES
					  ('".$data["name"]."','$UrlName','".$data["type"]."','$dstFile','$filename','$imageSync','".$data["defaultUrl"]."','".$data["alias"]."','".$data["description"]."','".$data["editorTip"]."','Active','$date','$date','".$data["Manual"]."','".$data["MaintainOrder"]."','".$data["OptimizeOrder"]."','".$data["ShowVisitLink"]."','".$data["ShowAFS"]."','".$data["ManualUploadImage"]."',{$data["term_country_self"]},'{$data['AdultStatus']}','{$data['ManualUpdateAdultStatus']}','$user','$user')";
			$this->objMysql->query($sql);
			$termid=$this->objMysql->getLastInsertId();
			//insert database table term_extend mincouponcount
			$sql="INSERT INTO `term_extend` (
					TermId,
					MinCouponCount) 
					VALUES 
					($termid,'".intval($data["MinCouponCount"])."')";
			$this->objMysql->query($sql);
			$term_image_big = $dstFile;
			$term_image_small = $filename;
			$term_country_selected = $data["country"] ? $data["country"] : array();
//				$sql="INSERT INTO `term_extend` (
//						TermId,
//						MinCouponCount) 
//						VALUES 
//						($termid,0)";
//				$this->objMysql->query($sql);
			
			//insert database table term mapping
			if (isset($data["site"])) {
				foreach ($data["site"] as $k=>$v) {
					if ($data["objectType"][$k] == "Keyword") {
						$data["objectId"][$k]= "";
						$data["displayname"][$k]= "";
					}
				   	if ($data["country"][$k] != 'NULL') {
						$data["country"][$k] = "'{$data['country'][$k]}'";
					}
					if (empty($data['relatedterm'][$k])) {
						$data['relatedtermid'][$k] = 0;
					}
					if ($data["objectType"][$k] == "Merchant" && !empty($data["objectId"][$k])) {
						$data["hasAffiliate"][$k] = $this->getCSMerchantHasAffiliate($data["site"][$k], $data["objectId"][$k]);
					}else{
						$data["hasAffiliate"][$k] = 'NO';
					}
					$sql="INSERT INTO `term_mapping` (
							  TermId,
							  Site,
							  ObjectType,
							  ObjectId,
							  Keyword,
							  DisplayName,
							  Country,
							  RelatedTermId,
							  RelatedTerm,
							  SyncPolicy,
							  `Order`,
							  `HasAffiliate`,
							  `AddTime`
							) 
							VALUES
							  (".$termid.",'".$data["site"][$k]."','".$data["objectType"][$k]."','".$data["objectId"][$k]."','".$data["keyword"][$k]."','".$data["displayname"][$k]."',".$data["country"][$k].",'".$data["relatedtermid"][$k]."','".$data["relatedterm"][$k]."','".$data["syncPolice"][$k]."','".$data["order"][$k]."','".$data["hasAffiliate"][$k]."','$date')";
					$this->objMysql->query($sql);
				}
			}
			//set term HasAffiliate
			$sql = "SELECT COUNT(*) FROM term_mapping WHERE HasAffiliate = 'yes' AND TermId = $termid";
			$hasAffiliate = $this->objMysql->getFirstRowColumn($sql);
			if ($hasAffiliate) {
				$sql = "UPDATE term SET HasAffiliate = 'YES' WHERE id = $termid";
				$this->objMysql->query($sql);
			}

			//Update term IsSubAffiliate
			$IsSubAffiliate = $this->checkIsSubAffiliate($termid);
			$IsSubAffiliate_str = $IsSubAffiliate?'YES':'NO';
			$sql = 'UPDATE term set IsSubAffiliate = "'.$IsSubAffiliate_str.'" WHERE id = '.$termid;
			$this->objMysql->query($sql);

			//update term realurl 
			$this->updateRealUrlByTermId($termid);
			
			//Insert term_department_mapping
   			$maxnumber = $data["departmentmax"];
   			for ($i = 0; $i < $maxnumber; $i++) {
   				if (!isset($data["dmp_id_$i"])) {
   					continue;
   				}
   				try {
   					if ($data["dmp_country_$i"] != 'NULL') {
   						$data["dmp_country_$i"] = "'".$data["dmp_country_$i"]."'";
   					}
   					$sql = "INSERT INTO `term_department_mapping` VALUES (NULL,$termid,'".$data["dmp_store_$i"]."','".$data["dmp_url_$i"]."','".$data["dmp_amount_$i"]."',".$data["dmp_country_$i"].",'".$data["dmp_position_$i"]."','$date')";
   					$this->objMysql->query($sql);
   				} catch (Exception $e) {
   					print_r($e);
   				}
   			}
			
   			//Replace term_country record
   			$countries = getCountryList();
   			unset( $countries["NULL"] );
   			$nonexistCountries = array();
//    			echo "<pre>";print_r($data);exit;
   			foreach ($countries as $k => $v) {
   				$term_country = get_post_var( "term_country_{$k}" );
   				if ( empty( $term_country ) ) {
   					$nonexistCountries[] = "'$k'";
   					continue;
   				}
   				
   				$oldimage_big =  '';
   				$oldimage_small =  '';
   				$dstFile = uploadImage ( "term_country_image_{$k}", '', false, TERM_THUMB_PATH, "deal" );
   				$dstFile=trim($dstFile, "/");
   				if ( ! $dstFile ) {// no image upload
   					$imagesbig = $oldimage_big;
   					$imagesmall = $oldimage_small;
   			
   				}else{
   					//get small image
   					$img = new ImageExt ();
   					$img->param ( TERM_THUMB_PATH . $dstFile );
   					$imagesbig = time () . rand ( 1000, 2000 ) . '.jpg';
   					$img->thumb ( TERM_THUMB_PATH . $imagesbig, TERM_IMAGE_BIG_MAX_WIDTH, TERM_IMAGE_BIG_MAX_HEIGHT, 0, 0 );
   					$imagesmall = time () . rand ( 0, 1000 ) . '.jpg';
   					$img->thumb ( TERM_THUMB_PATH . $imagesmall, TERM_IMAGE_SMALL_MAX_WIDTH, TERM_IMAGE_SMALL_MAX_HEIGHT, 0, 0 );
   					//delete old image
   					unlink( TERM_THUMB_PATH . $dstFile );
   				}
   				$term_country_defaulturl= get_post_var( "term_country_defaulturl_{$k}" );
   				$term_country_defaulturl = addslashes( $term_country_defaulturl );
   				$term_country_description= get_post_var( "term_country_description_{$k}" );
   				$term_country_description = addslashes( $term_country_description );
   				$term_country_showvisitlink = get_post_var( "term_country_showvisitlink_{$k}" );
   				$sql = "REPLACE INTO `term_country` (TermId,CountryName,Image,ImageSmall,DefaultUrl,Description,ShowVisitLink,ADDTIME)
   				VALUES ($termid,'{$k}','$imagesbig','$imagesmall','$term_country_defaulturl','$term_country_description','$term_country_showvisitlink',NOW()) ";
   				$this->objMysql->query($sql);
   				
   				if (  empty( $term_image_big  ) && !empty( $dstFile ) )  {
   					$term_image_big = $imagesbig;
   					$term_image_small = $imagesmall;
   					$sql = "UPDATE term SET Image = '$term_image_big' ,ImageSmall = '$term_image_small' WHERE ID = $termid";
   					$this->objMysql->query($sql);
   				}
   			}
   			
			//update `organic_term`
			if (isset($data["organic_term_id"]) && $data["organic_term_id"] != '') {
				$sql="SELECT * FROM `organic_term` WHERE OrganicTerm ='".$data["name"]."'";
				$queryid=$this->objMysql->query($sql);
				$count=$this->objMysql->getNumRows($queryid);
				$this->objMysql->freeResult($queryid);
				if ($count > 0) {
					$sql="UPDATE 
							  `organic_term` 
							SET
							  TermId = $termid,
							  Term = '".$data["name"]."',
							  Result = 'Term' 
							WHERE OrganicTerm = '".$data["name"]."' ";
					$this->objMysql->query($sql);
				}else {
					$sql="INSERT INTO `organic_term` VALUES ('".$data["name"]."',$termid,'".$data["name"]."','Term','$date')";
					$this->objMysql->query($sql);
				}
			}
			
			//insert global category
			$countries["GLOBAL"] = "GLOBAL";
			$objCategory = new NormalCategory($this->objMysql);
			foreach ($countries as $kshortName => $vLongName) {
				if ($kshortName == "GLOBAL") {
					$category = $data["Category"] ;
				}else {
					if ($data["type"] == 'STORE SINGLE COUNTRY') {
						if (empty($term_country_selected)) {
							if ( trim($data["term_country_self"],"'") != $kshortName ) {
								continue;
							};
						}else{
							if ( ! in_array($kshortName, $term_country_selected ) ){
								continue;
							};
						}
					}else{
						if ( ! in_array($kshortName, $term_country_selected ) ){
							continue;
						}
					}
					$category = $data[$kshortName."_Category"];
					//build term's country for category
					$sql = "INSERT INTO `term_country_mapping` (TermId,Country,`AddTime`) VALUES ($termid,'$kshortName',NOW())";
					$this->objMysql->query($sql);
				}
				if (empty($category)) {
					$category = array();
				}
				
				foreach ($category as $v) {
					$sql  = "INSERT INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ($termid,$v,'NO','YES',NOW())";
					$this->objMysql->query($sql);
					$objCategory -> updateCategoryNewNeedMemFresh($v, "YES" );
					$parentCategory = $objCategory -> getParentCategoryId($v);
					if ( $parentCategory != 0) {
						$sql  = "INSERT IGNORE INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ($termid,$parentCategory,'NO','NO',NOW())";
						$this->objMysql->query($sql);
						$objCategory -> updateCategoryNewNeedMemFresh($parentCategory, "YES" );
						$parentParentCategory = $objCategory -> getParentCategoryId($parentCategory);
						if ($parentParentCategory != 0) {
							$sql  = "INSERT IGNORE  INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ($termid,$parentParentCategory,'NO','NO',NOW())";
							$this->objMysql->query($sql);;
							$objCategory -> updateCategoryNewNeedMemFresh($parentParentCategory, "YES" );
						}
					}
				}
			}
			//set primary category
			if (!empty($data["category_primary"])) {
				$sql = "UPDATE `term_category` SET IsPrimary = 'yes' WHERE TermId = $termid AND CategoryId = {$data["category_primary"]}";
				$this->objMysql->query($sql);
			}
			$date = date("Y-m-d H:i:s");

			$sql = "INSERT INTO `term_split_log` (TermId,FromTermId,`Action`,EditorName,`AddTime`) VALUES ($termid,$termid,'NEW','$user','$date')";
			$this->objMysql->query($sql);
			return $termid;
		}
		
   		public function editTerm($data) {
   			//check if upload new image
   			$date=date("Y-m-d H:i:s");
   			$data["name"] = trim($data["name"]) ;
   			$data["ManualUploadImage"] = isset($data["ManualUploadImage"]) ? 'YES' : 'NO';
   			if ($data["term_country_self"] != 'NULL' && $data["type"] == 'STORE SINGLE COUNTRY') {
   				$data["term_country_self"] = "'{$data['term_country_self']}'";
   			}else{
   				$data["term_country_self"] = 'NULL';
   			}
   			$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "";
   			if(!$user && substr($_SERVER["REMOTE_ADDR"],0,8) == "192.168.") $user = "couponsn";
   			if ($_FILES["image"]['error'] == 4) {//doesn't change old image
	   			$sql="SELECT Image FROM term WHERE id=".$data["term_id"];
	   			$imageold=$this->objMysql->getFirstRowColumn($sql);
//   				$dstFile=$imageold;
   				if($imageold == '') //no file uploaded
				{
					if ($data["type"]=="STORE") {
						$imageSync="YES";
					}else {
						$imageSync="NO";
					}
				}else {
					$imageSync="NO";
				}
				$dstFile='';
				$filename='';
				//Update database table term 
				
				$sql="UPDATE `term` SET 
						   `Name` = '".trim($data["name"])."',
						  `MaintainOrder` = '".$data["MaintainOrder"]."',
						  `Type` = '".$data["type"]."',
						  ImageSync ='$imageSync',
						  `OptimizeOrder` = '".$data["OptimizeOrder"]."',
						  `Manual` = '".$data["Manual"]."',
						  `DefaultUrl` = '".$data["defaultUrl"]."',
						  Alias='".$data["alias"]."',
						  Description='".$data["description"]."',
						  EditorTip='".$data["editorTip"]."',
						  ShowVisitLink = '".$data["ShowVisitLink"]."',
				  		  SubmitCoupon = '".$data["SubmitCoupon"]."',
						  ShowAFS = '".$data["ShowAFS"]."',
						  Editor = '".$user."',
						  ManualUploadImage = '".$data["ManualUploadImage"]."',
						  Country = ".$data["term_country_self"].",";
				if (isset($data["ManualUpdateAdultStatus"]) ) {
					$sql .= "AdultStatus = '{$data['AdultStatus']}',
							 ManualUpdateAdultStatus = 'YES' ,";
				}else{
					$sql .= "AdultStatus = 'NO',
							ManualUpdateAdultStatus = 'NO' ,";
				}
						  		
				$sql .= " LastUpdateTime='$date'
						WHERE id = ".$data["term_id"];
				$this->objMysql->query($sql);
   			}else {
   				$imageSync="NO";
   				$dstFile=uploadImage('image', '', false, TERM_THUMB_PATH, "deal");
   				$dstFile=trim($dstFile, "/");
	//			echo $dstFile;
				$img=new ImageExt();
				$img->param(TERM_THUMB_PATH.$dstFile);
				$img->thumb ( TERM_THUMB_PATH . $dstFile, TERM_IMAGE_BIG_MAX_WIDTH, TERM_IMAGE_BIG_MAX_HEIGHT, 0, 0 );
				$filename=time().rand(0, 1000).'.jpg';
				$imagesmall = $img->thumb(TERM_THUMB_PATH.$filename, TERM_IMAGE_SMALL_MAX_WIDTH, TERM_IMAGE_SMALL_MAX_HEIGHT, 0, 0);
				//Update database table term 
				$sql="UPDATE `term` SET 
						  `Name` = '".trim($data["name"])."',
						  `Type` = '".$data["type"]."',
						  `MaintainOrder` = '".$data["MaintainOrder"]."',
						  `OptimizeOrder` = '".$data["OptimizeOrder"]."',
						  `Manual` = '".$data["Manual"]."',
						  `DefaultUrl` = '".$data["defaultUrl"]."',
						  Image = '$dstFile',
					  	  ImageSmall ='$filename',
						  ImageSync ='$imageSync',
						  Alias='".$data["alias"]."',
						  Description='".$data["description"]."',
						  EditorTip='".$data["editorTip"]."',
						  ShowVisitLink = '".$data["ShowVisitLink"]."',
						  SubmitCoupon = '".$data["SubmitCoupon"]."',
						  ShowAFS = '".$data["ShowAFS"]."',
						  Editor = '".$user."',
						  ManualUploadImage = '".$data["ManualUploadImage"]."',
						 Country = ".$data["term_country_self"].",";
				if (isset($data["ManualUpdateAdultStatus"]) ) {
					$sql .= "AdultStatus = '{$data['AdultStatus']}',
							 ManualUpdateAdultStatus = 'YES' ,";
				}else{
					$sql .= "AdultStatus = 'NO',
							ManualUpdateAdultStatus = 'NO' ,";
				}
						  		
				$sql .= " LastUpdateTime='$date'
						WHERE id = ".$data["term_id"];
				
				$this->objMysql->query($sql);
   			}
   			//update database table term_extend mincouponcount
   			$sql="UPDATE `term_extend` SET MinCouponCount=".intval($data["MinCouponCount"])." WHERE TermId = ".$data["term_id"];
   			$this->objMysql->query($sql);
   			$term_image_big = $dstFile;
   			$term_image_small = $filename;
   			$term_country_selected = $data["country"] ? $data["country"] : array();
   			//update database table term mapping
   			
   			$sql="SELECT ID FROM `term_mapping` WHERE TermId = ".$data["term_id"];
   			$preid=$this->objMysql->getRows($sql,"ID");
   			$preid=array_keys($preid);
   			$newid=!empty($data["mapping_ids"]) ? $data["mapping_ids"] :array();
   			$diffpnkey=array_diff($preid, $newid);
   			if (count($diffpnkey)>0) {
   				$diffpnkey=implode(",", $diffpnkey);
   				$sql="DELETE FROM `term_mapping` WHERE ID IN ($diffpnkey)";
   				$this->objMysql->query($sql);
   			}
   			$intersectkey=array_intersect($preid, $newid);
   			if (count($intersectkey)>0) {
   				foreach ($intersectkey as $value) {
   					$date=date("Y-m-d H:i:s");
   					$k=array_search($value, $data["mapping_ids"]);
   					if ($data["objectType"][$k] == "Keyword") {
						$data["objectId"][$k]= "";
						$data["displayname"][$k]= "";
					}
   					if ($data["country"][$k] != 'NULL') {
						$data["country"][$k] = "'{$data['country'][$k]}'";
					}
   					if (empty($data['relatedterm'][$k])) {
						$data['relatedtermid'][$k] = 0;
					}
					if ($data["objectType"][$k] == "Merchant" && !empty($data["objectId"][$k])) {
						$data["hasAffiliate"][$k] = $this->getCSMerchantHasAffiliate($data["site"][$k], $data["objectId"][$k]);
					}else{
						$data["hasAffiliate"][$k] = 'NO';
					}
   					$sql="UPDATE `term_mapping` SET 
   						  Site='".$data["site"][$k]."',
						  ObjectType='".$data["objectType"][$k]."',
						  ObjectId='".$data["objectId"][$k]."',
						  Keyword='".$data["keyword"][$k]."',
						  SyncPolicy='".$data["syncPolice"][$k]."',
						  DisplayName='".$data["displayname"][$k]."',
						  Country= ".$data["country"][$k]." ,
						  RelatedTerm='".$data["relatedterm"][$k]."',
						  RelatedTermId='".$data["relatedtermid"][$k]."',
						  HasAffiliate='".$data["hasAffiliate"][$k]."',
						  `Order`='".$data["order"][$k]."'
						  WHERE ID = $value";
   					$this->objMysql->query($sql);
   				}
   			}
   			$diffnpkey=array_diff($newid, $preid);
   			if (count($diffnpkey)>0) {
	   			foreach ($diffnpkey as $k=>$v) {
	   				if ($data["objectType"][$k] == "Keyword") {
						$data["objectId"][$k]= "";
						$data["displayname"][$k]= "";
					}
	   			   	if ($data["country"][$k] != 'NULL') {
						$data["country"][$k] = "'{$data['country'][$k]}'";
					}
					if ($data["objectType"][$k] == "Merchant" && !empty($data["objectId"][$k])) {
						$data["hasAffiliate"][$k] = $this->getCSMerchantHasAffiliate($data["site"][$k], $data["objectId"][$k]);
					}else{
						$data["hasAffiliate"][$k] = 'NO';
					}
					$sql="INSERT INTO `term_mapping` (
							  TermId,
							  Site,
							  ObjectType,
							  ObjectId,
							  Keyword,
							  DisplayName,
							  Country,
							  RelatedTermId,
							  RelatedTerm,
							  SyncPolicy,
							  `Order`,
							  `HasAffiliate`,
							  `AddTime`
							) 
							VALUES
							  (".$data["term_id"].",'".$data["site"][$k]."','".$data["objectType"][$k]."','".$data["objectId"][$k]."','".$data["keyword"][$k]."','".$data["displayname"][$k]."',".$data["country"][$k].",'".$data["relatedtermid"][$k]."','".$data["relatedterm"][$k]."','".$data["syncPolice"][$k]."','".$data["order"][$k]."','".$data["hasAffiliate"][$k]."','$date')";
					$this->objMysql->query($sql);
				}
   			}
   			//set term HasAffiliate
			$sql = "SELECT COUNT(*) FROM term_mapping WHERE HasAffiliate = 'yes' AND TermId = {$data["term_id"]}";
			$hasAffiliate = $this->objMysql->getFirstRowColumn($sql);
			if ($hasAffiliate) {
				$sql = "UPDATE term SET HasAffiliate = 'YES' WHERE id = {$data["term_id"]}";
				$this->objMysql->query($sql);
			}else{
				$sql = "UPDATE term SET HasAffiliate = 'NO' WHERE id = {$data["term_id"]}";
				$this->objMysql->query($sql);
			}

			//Update term IsSubAffiliate
			$IsSubAffiliate = $this->checkIsSubAffiliate($data["term_id"]);
			$IsSubAffiliate_str = $IsSubAffiliate?'YES':'NO';
			$sql = 'UPDATE term set IsSubAffiliate = "'.$IsSubAffiliate_str.'" WHERE id = '.$data["term_id"];
			$this->objMysql->query($sql);
			//update term realurl 
			$this->updateRealUrlByTermId($data['term_id']);
			
   			//Update term_department_mapping
   			$maxnumber = $data["departmentmax"];
   			if ($data["departmentdeletedids"]) {
   				try {
	   				$sql = "DELETE FROM `term_department_mapping` WHERE ID IN (".addslashes($data["departmentdeletedids"]).")";
	   				$this->objMysql->query($sql);
   				}catch (Exception $e){
   					print_r($e);
   				}
   			}
   			for ($i = 0; $i < $maxnumber; $i++) {
   				if (!isset($data["dmp_id_$i"])) {
   					continue;
   				}
				if ( $data ["dmp_country_$i"] != 'NULL' ) {
					$data ["dmp_country_$i"] = "'" . $data ["dmp_country_$i"] . "'";
				}
   				if (isset($data["dmp_oldflag_$i"])) {
   					try {
   						$sql = "UPDATE `term_department_mapping` SET DepartmentStoreId = ".$data["dmp_store_$i"]." ,Url = '".$data["dmp_url_$i"]."' ,Amount = '".$data["dmp_amount_$i"]."',Country = ".$data["dmp_country_$i"].",Position = '".$data["dmp_position_$i"]."' WHERE ID = ".$data["dmp_id_$i"];
   						$this->objMysql->query($sql);
   					} catch (Exception $e) {
   						print_r($e);
   					};
   				}else{
   					try {
   						$sql = "INSERT INTO `term_department_mapping` VALUES (NULL,".$data["term_id"].",'".$data["dmp_store_$i"]."','".$data["dmp_url_$i"]."','".$data["dmp_amount_$i"]."',".$data["dmp_country_$i"].",'".$data["dmp_position_$i"]."','$date')";
   						$this->objMysql->query($sql);
   					} catch (Exception $e) {
   						print_r($e);
   					}
   				}
   			}
   			
   			//Replace term_country record
   			$countries = getCountryList();
   			unset( $countries["NULL"] );
   			$nonexistCountries = array();
   			foreach ($countries as $k => $v) {
   				$term_country = get_post_var( "term_country_{$k}" );
   				if ( empty( $term_country ) ) {
   					$nonexistCountries[] = "'$k'";
   					continue;
   				}
   				$sql ="SELECT image,imagesmall FROM term_country WHERE TermId = {$data["term_id"]} AND CountryName = '{$k}'";
   				$oldimage  = $this-> objMysql ->  getFirstRow($sql);
   				$oldimage_big = ( isset( $oldimage["image"] ) ) ? $oldimage["image"] : '';
   				$oldimage_small = ( isset( $oldimage["imagesmall"] ) ) ? $oldimage["imagesmall"] : '';
   				$dstFile = uploadImage ( "term_country_image_{$k}", '', false, TERM_THUMB_PATH, "deal" );
   				$dstFile=trim($dstFile, "/");
   				if ( ! $dstFile ) {// no image upload
					$imagesbig = $oldimage_big;
					$imagesmall = $oldimage_small;
   					
   				}else{
	   				//get small image
	   				$img = new ImageExt ();
	   				$img->param ( TERM_THUMB_PATH . $dstFile );
	   				$imagesbig = time () . rand ( 1000, 2000 ) . '.jpg';
	   				$img->thumb ( TERM_THUMB_PATH . $imagesbig, TERM_IMAGE_BIG_MAX_WIDTH, TERM_IMAGE_BIG_MAX_HEIGHT, 0, 0 );
	   				$imagesmall = time () . rand ( 0, 1000 ) . '.jpg';
	   				$img->thumb ( TERM_THUMB_PATH . $imagesmall, TERM_IMAGE_SMALL_MAX_WIDTH, TERM_IMAGE_SMALL_MAX_HEIGHT, 0, 0 );
	   				//delete old image
	   				if ( $oldimage_big != '' && file_exists( TERM_THUMB_PATH . $oldimage_big ) ) {
	   					unlink ( TERM_THUMB_PATH . $oldimage_big );
	   				}
	   				if ( $oldimage_small != '' && file_exists( TERM_THUMB_PATH . $oldimage_small ) ) {
	   					unlink ( TERM_THUMB_PATH . $oldimage_small );
	   				}
	   				unlink( TERM_THUMB_PATH . $dstFile );
   				}
   				$term_country_defaulturl= get_post_var( "term_country_defaulturl_{$k}" );
   				$term_country_defaulturl = addslashes( $term_country_defaulturl );
   				$term_country_description= get_post_var( "term_country_description_{$k}" );
   				$term_country_description = addslashes( $term_country_description );
   				$term_country_showvisitlink = get_post_var( "term_country_showvisitlink_{$k}" );
   				$sql = "REPLACE INTO `term_country` (TermId,CountryName,Image,ImageSmall,DefaultUrl,Description,ShowVisitLink,ADDTIME) 
   						VALUES ({$data["term_id"]},'{$k}','$imagesbig','$imagesmall','$term_country_defaulturl','$term_country_description','$term_country_showvisitlink',NOW()) ";
   				$this->objMysql->query($sql);
   				
   				//update term image 
   				if ( empty( $term_image_big  ) && !empty( $dstFile ) )  {
   					$term_image_big = $imagesbig;
   					$term_image_small = $imagesmall;
   					$sql="SELECT image,imagesmall FROM term WHERE id=".$data["term_id"];
   					$term_old_image=$this->objMysql->getFirstRow($sql);
   					$term_oldimage_big = ( isset( $term_old_image["image"] ) ) ? $term_old_image["image"] : '';
   					$term_oldimage_small = ( isset( $term_old_image["imagesmall"] ) ) ? $term_old_image["imagesmall"] : '';
   					if( $term_oldimage_big ){
   						$sql = "SELECT * FROM `term_country` WHERE TermId = {$data["term_id"]} AND Image = '$term_oldimage_big'";
   						$row = $this->objMysql->getFirstRow($sql);
   						if ( empty( $row ) ) {
   							//delete old image
			   				if ( $term_oldimage_big != '' && file_exists( TERM_THUMB_PATH . $term_oldimage_big ) ) {
			   					unlink ( TERM_THUMB_PATH . $term_oldimage_big );
			   				}
			   				if ( $term_oldimage_small != '' && file_exists( TERM_THUMB_PATH . $term_oldimage_small ) ) {
			   					unlink ( TERM_THUMB_PATH . $term_oldimage_small );
			   				};
   						}
   					}
   					//   				$dstFile=$imageold;
   					$sql = "UPDATE term SET Image = '$term_image_big' ,ImageSmall = '$term_image_small' WHERE ID = {$data["term_id"]}";
   					$this->objMysql->query($sql);
   				}
   			}
   			//delete nonexist term_country image and record
   			if ($nonexistCountries) {
   				/* commented for term split program that don't need delete old image
   				$sql = "SELECT image,imagesmall FROM term_country WHERE TermId = {$data["term_id"]} AND CountryName IN (" . implode(',', $nonexistCountries) . ")";
   				$oldimages = $this-> objMysql -> getRows($sql);
   				foreach ($oldimages as $v) {
   					if ( $v["image"] != '' && file_exists( TERM_THUMB_PATH . $v["image"] ) ) {
	   					unlink ( TERM_THUMB_PATH . $v["image"] );
	   				}
	   				if ( $v["imagesmall"] != '' && file_exists( TERM_THUMB_PATH . $v["imagesmall"] ) ) {
	   					unlink ( TERM_THUMB_PATH . $v["imagesmall"] );
	   				};
   				}
   				*/
   				$sql = "DELETE FROM term_country WHERE TermId = {$data["term_id"]} AND CountryName IN (" . implode(',', $nonexistCountries) . ")";
   				$this->objMysql->query($sql);
   			}
   			//UPDATE `organic_term`
   			if ($data["oldname"] != $data["name"]) {
   				$sql = "UPDATE `organic_term` SET Term ='".addslashes($data["name"])."' WHERE Term = '".addslashes($data["oldname"])."'";
   				$this->objMysql->query($sql);
   			}
   			//update `organic_term` if from organic_term_list page
			if (isset($data["organic_term_id"]) && $data["organic_term_id"] != '') {
				$sql="SELECT * FROM `organic_term` WHERE OrganicTerm ='".$data["name"]."'";
				$queryid=$this->objMysql->query($sql);
				$count=$this->objMysql->getNumRows($queryid);
				$this->objMysql->freeResult($queryid);
				if ($count > 0) {
					$sql="UPDATE 
							  `organic_term` 
							SET
							  TermId = ".$data["term_id"].",
							  Term = '".$data["name"]."',
							  Result = 'Term' 
							WHERE OrganicTerm = '".$data["name"]."' ";
					$this->objMysql->query($sql);
				}else {
					$sql="INSERT INTO `organic_term` VALUES ('".$data["name"]."',".$data["term_id"].",'".$data["name"]."','Term','$date')";
					$this->objMysql->query($sql);
				}
			}
			//clear global category 
			$sql = "DELETE FROM `term_category` WHERE TermId = {$data["term_id"]}";
			$this->objMysql->query($sql);
			//clear term country for category
			$sql = "DELETE FROM `term_country_mapping` WHERE TermId =  {$data["term_id"]}";
			$this->objMysql->query($sql);
			//insert  category
			$countries["GLOBAL"] = "GLOBAL";
			$objCategory = new NormalCategory($this->objMysql);
			foreach ($countries as $kshortName => $vLongName) {
				if ($kshortName == "GLOBAL") {
					$category = $data["Category"] ;
				}else {
					if ($data["type"] == 'STORE SINGLE COUNTRY') {
						if (empty($term_country_selected)) {
							if ( trim($data["term_country_self"],"'") != $kshortName ) {
								continue;
							};
						}else{
							if ( ! in_array($kshortName, $term_country_selected ) ){
								continue;
							};
						}
					}else{
						if ( ! in_array($kshortName, $term_country_selected ) ){
							continue;
						}
					}
					$category = $data[$kshortName."_Category"];
					//build term's country for category
					$sql = "INSERT INTO `term_country_mapping` (TermId,Country,`AddTime`) VALUES ({$data["term_id"]},'$kshortName',NOW())";
					$this->objMysql->query($sql);
				}
				if (empty($category)) {
					$category = array();
				}
				
				foreach ($category as $v) {
					$sql  = "INSERT INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ({$data["term_id"]},$v,'NO','YES',NOW())";
					$this->objMysql->query($sql);
					$objCategory -> updateCategoryNewNeedMemFresh($v, "YES" );
					$parentCategory = $objCategory -> getParentCategoryId($v);
					if ( $parentCategory != 0) {
						$sql  = "INSERT IGNORE INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ({$data["term_id"]},$parentCategory,'NO','NO',NOW())";
						$this->objMysql->query($sql);
						$objCategory -> updateCategoryNewNeedMemFresh($parentCategory, "YES" );
						$parentParentCategory = $objCategory -> getParentCategoryId($parentCategory);
						if ($parentParentCategory != 0) {
							$sql  = "INSERT IGNORE  INTO `term_category` (TermId , CategoryId,IsPrimary,IsManualSelected,`AddTime`) VALUES ({$data["term_id"]},$parentParentCategory,'NO','NO',NOW())";
							$this->objMysql->query($sql);
							$objCategory -> updateCategoryNewNeedMemFresh($parentParentCategory, "YES" );
						}
					}
				}
				
			}
			//set primary category
			if (!empty($data["category_primary"])) {
				$sql = "UPDATE `term_category` SET IsPrimary = 'yes' WHERE TermId = {$data["term_id"]} AND CategoryId = {$data["category_primary"]}";
				$this->objMysql->query($sql);
			}
			$date = date("Y-m-d H:i:s");
			$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : $_SERVER["REMOTE_USER"];
			$sql = "INSERT INTO `term_split_log` (TermId,FromTermId,`Action`,EditorName,`AddTime`) VALUES ({$data["term_id"]},{$data["term_id"]},'EDIT','$user','$date')";
			$this->objMysql->query($sql);
//   			exit();
   		}
   		
   		public function checkIsSubAffiliate($termid){
   			
   			$sql = 'SELECT Site,ObjectType,ObjectId FROM `term_mapping` WHERE TermId = '.$termid;
   			$mappings = $this->objMysql->getRows($sql);
   			if (count($mappings) == 0) {
   				return false;
   			}
   			
   			$isSubAffiliate = true;
   			
			//判断1 ObjectType==Merchant
   			foreach ($mappings as $mapping){
				if ($mapping['ObjectType'] != 'Merchant') {
					$isSubAffiliate = false;
					break;
				}
			}
			
			if(!$isSubAffiliate)return $isSubAffiliate;

			//获取merchant_mapping中对应原始ID
			//判断2 merchant_mapping中是否有对应关系

			$id = array();
			foreach($mappings as $k=>$v){
				$sql = 'SELECT ID FROM merchant_mapping WHERE M_ID = '.$v['ObjectId'].' AND Site = "'.$v['Site'].'"';
				$merchantID = $this->objMysql->getFirstRowColumn($sql);

				if (empty($merchantID)) {
					$isSubAffiliate = false;
					break;
				}else{
					$id[] = $merchantID;
				}
			}

			if(!$isSubAffiliate)return $isSubAffiliate;

			//获取normalmerchant_addinfo中原始数�?
			//判断3 检查normalmerchant_addinfo中DefaultAffiliate是否属于subAffiliatesIds

			$sql = 'SELECT DefaultAffiliate FROM `normalmerchant_addinfo` WHERE id IN ('.join(',',$id).')';
			$affID = $this->objMysql->getRows($sql);

			//获取子联�?
			global $subAffiliatesIds;
			if (empty($subAffiliatesIds)) {
				$subAffiliatesIds = getSubAffiliateIds($this->objMysql);
			}
			
			foreach($affID as $k=>$v){
				if(!in_array($v['DefaultAffiliate'],$subAffiliatesIds)){
					$isSubAffiliate = false;
					break;
				}
			}

			return $isSubAffiliate;
   		}
   		
   		public function onlinecoupon($termid,$couponid) {
   			$sql="UPDATE `termcoupon_relationship` SET `Status` = 'Online' WHERE TermId = $termid AND NormalCouponId = $couponid";
   			$this->objMysql->query($sql);
   		}

	   	public function getCouponTermRelationshipByTerm($termid,$indexColoum="",$where="") {
   			$sql="SELECT * FROM `termcoupon_relationship` WHERE TermId = $termid $where";
   			$row = $this->objMysql->getRows($sql,$indexColoum);
   			return $row;
   		}

	   	public function getCouponTermRelationshipByCoupon($couponid) {
   			$sql = "SELECT * FROM `termcoupon_relationship` WHERE NormalCouponId = $couponid";
   			$row = $this->objMysql->getRows($sql);
   			return $row;
   		}
   		
		public function getCouponTermRelationshipByCouponIds($couponids,$indexColoum,$where="") {
			if (!$couponids) {
				return  array();
			}
   			$sql = "SELECT * FROM `termcoupon_relationship` WHERE CouponId IN (".implode(',', $couponids).") $where ";
   			$row = $this->objMysql->getRows($sql,$indexColoum);
   			return $row;
   		}
   		
   		public function delete($termid) {
   			$sql="DELETE FROM `organic_term` WHERE TermId =$termid";
   			$this->objMysql->query($sql);
   			$sql="UPDATE `term` SET `Status` ='Deleted' WHERE Id =$termid";
   			$this->objMysql->query($sql);
   		}
   		
   		public function active($termid) {
   			$sql="UPDATE `term` SET `Status` ='Active' WHERE Id =$termid";
   			$this->objMysql->query($sql);
   		}
   		
   		public function offlinecoupon($termid,$couponid) {
   			$sql="UPDATE `termcoupon_relationship` SET `Status` = 'Offline' WHERE TermId = $termid AND NormalCouponId = $couponid";
   			$this->objMysql->query($sql);
   		}
   		
   		
   		public function onLineAllCoupon($couponid_termid) {
   			if (empty($couponid_termid)) {
   				return ;
   			}
   			$sql = "UPDATE `termcoupon_relationship` SET STATUS = 'Online' WHERE ";
   			foreach ($couponid_termid as $k => $v) {
   				$res[] = "(TermId={$v[1]} AND NormalCouponId ={$v[0]})";
   			}
   			$sql .= implode('OR',  $res);
   			$this->objMysql->query($sql);
   		}
   		
   		public function offLineAllCoupon($couponid_termid) {
   			if (empty($couponid_termid)) {
   				return ;
   			}
   			$sql = "UPDATE `termcoupon_relationship` SET STATUS = 'Offline' WHERE ";
   			foreach ($couponid_termid as $k => $v) {
   				$res[] = "(TermId={$v[1]} AND NormalCouponId ={$v[0]})";
   			}
   			$sql .= implode('OR',  $res);
   			$this->objMysql->query($sql);
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
         
		public function checkDup($termname) {
			$urlname = addslashes(filter_rewrite_tags($termname));
			$sql="SELECT * FROM term WHERE `Name` = '".addslashes($termname)."' OR UrlName = '$urlname'";
			$qryId=$this->objMysql->query($sql);
			$count=$this->objMysql->getNumRows($qryId);
			return $count;
		}

		public function merge($fromid,$toid,$comment,$user) {
			//Clean to_term_mapping if exists in to_term_mapping
			$sql = "SELECT * FROM `term_mapping` WHERE TermId = $fromid";
			$fromMapping = $this->objMysql -> getRows($sql,"ID");
			
			$sql = "SELECT ID,Site,ObjectType,ObjectId,Keyword FROM `term_mapping` WHERE TermId = $toid";
			$toMapping = $this->objMysql -> getRows($sql,"ID");
			$fromMappingCompare =array();
			foreach ($fromMapping as $k => $v) {
				$fromMappingCompare[$k] = $v["Site"]."_".$v["ObjectType"]."_".$v["ObjectId"]."_".$v["Keyword"];
			}
			
			foreach ($toMapping as $k => $v) {
				$toMapping[$k] = $v["Site"]."_".$v["ObjectType"]."_".$v["ObjectId"]."_".$v["Keyword"];
			}
			
			$arrIntersect = array_intersect($fromMappingCompare,$toMapping);
//			unset($fromMapping);
			unset($toMapping);
//			if (!empty($arrIntersect)) {
//				$intersectids=array_keys($arrIntersect);
//				$intersectids=implode(",", $intersectids);
//	//			echo "<pre>";print_r($intersectids);
//				$sql = "DELETE FROM `term_mapping` WHERE ID IN ($intersectids)";
//				$this->objMysql ->query($sql);
//			}
			//End of Mapping Clean.
			
			//Insert `term_mapping` fromMapping 
			if ($fromMapping) {
				$sql = "INSERT INTO `term_mapping` (TermId,Site,ObjectType,ObjectId,Keyword,DisplayName,Country,RelatedTermId,RelatedTerm,SyncPolicy,`Order`,HasAffiliate,`AddTime`) 
							VALUES ";
				$values = array();
				foreach ($fromMapping as $v) {
					if (isset($arrIntersect[$v["ID"]])) {
						continue;
					}
					$values[] = "($toid,'".$v["Site"]."','".$v["ObjectType"]."','".$v["ObjectId"]."','".addslashes($v["Keyword"])."','".addslashes($v["DisplayName"])."','".$v["Country"]."','".$v["RelatedTermId"]."','".$v["RelatedTerm"]."','".$v["SyncPolicy"]."','".$v["Order"]."','".$v["HasAffiliate"]."','".$v["AddTime"]."')";
				}
//				echo "<<pre>";print_r($values);exit;
				if ($values) {
					$values = implode(',', $values);
					$sql .= $values;
	//				$sql="UPDATE `term_mapping` SET TermId = $toid WHERE TermId = $fromid";
					$this->objMysql->query($sql);
				}
			}
			
			//Clean from_termcoupon if exists in to_termcoupon
			$sql = "SELECT NormalCouponId FROM `termcoupon_relationship`  WHERE TermId = $fromid";
			$fromCoupon = $this->objMysql -> getRows($sql);
			
			$sql = "SELECT NormalCouponId FROM `termcoupon_relationship`  WHERE TermId = $toid";
			$toCoupon = $this->objMysql -> getRows($sql);
			
			foreach ($fromCoupon as $k => $v) {
				$fromCoupon[$k] = $v["NormalCouponId"];
			}
			
			foreach ($toCoupon as $k => $v) {
				$toCoupon[$k] = $v["NormalCouponId"];
			}
			$arrIntersect = array_intersect($toCoupon, $fromCoupon);
			unset($fromCoupon);
			unset($toCoupon);
			if (!empty($arrIntersect)) {
				$intersectids=implode(",", $arrIntersect);
	//			echo "<pre>";print_r($intersectids);
				$sql = "DELETE FROM `termcoupon_relationship` WHERE TermId = $fromid AND NormalCouponId IN ($intersectids)";
				$this->objMysql->query($sql);
			}
			//End of Coupon Clean.
			
			//Update termcoupon_relationship
			$sql="UPDATE `termcoupon_relationship` SET TermId = $toid WHERE TermId = $fromid";
			$this->objMysql->query($sql);
			
			//Clean from_term manualpromotion if exists in to_term manualpromotion
			$sql = "SELECT * FROM `termmanualpromotion_relationship`  WHERE TermId = $fromid";
			$fromCoupon = $this->objMysql -> getRows($sql);
			
			$sql = "SELECT ManualPromotionId FROM `termmanualpromotion_relationship`  WHERE TermId = $toid";
			$toCoupon = $this->objMysql -> getRows($sql);
			$fromCouponCompare =array();
			foreach ($fromCoupon as $k => $v) {
				$fromCouponCompare[$k] = $v["ManualPromotionId"];
			}
			
			foreach ($toCoupon as $k => $v) {
				$toCoupon[$k] = $v["ManualPromotionId"];
			}
			$arrIntersect = array_intersect($toCoupon, $fromCouponCompare);
//			unset($fromCoupon);
			unset($toCoupon);
//			if (!empty($arrIntersect)) {
//				$intersectids=implode(",", $arrIntersect);
//	//			echo "<pre>";print_r($intersectids);
//				$sql = "DELETE FROM `termmanualpromotion_relationship` WHERE TermId = $toid AND ManualPromotionId IN ($intersectids)";
//				$this->objMysql->query($sql);
//			}
			//End of manualpromotion Clean.
			
		//Insert `termmanualpromotion_relationship` fromManualPromotion
			if ($fromCoupon) {
				$sql = "INSERT INTO `termmanualpromotion_relationship` (TermId,ManualPromotionId,Country,`AddTime`) 
							VALUES ";
				$values = array();
				foreach ($fromCoupon as $v) {
					if (in_array($v["ManualPromotionId"], $arrIntersect)) {
						continue;
					}
					$values[] = "($toid,'".$v["ManualPromotionId"]."','".$v["Country"]."','".$v["AddTime"]."')";
				}
//				echo "<<pre>";print_r($values);exit;
				if ($values) {
					$values = implode(',', $values);
					$sql .= $values;
	//				$sql="UPDATE `term_mapping` SET TermId = $toid WHERE TermId = $fromid";
					$this->objMysql->query($sql);
				}
			}
			
			//Update from term set status merged
			$sql = "UPDATE term SET `Status` = 'Merged' WHERE ID  = $fromid";
			$this->objMysql->query($sql);
			//Insert a record to term_merge_log
			$sql = "INSERT INTO term_merge_log (`FromTermId`,`ToTermId`,`Comment`,`Operator`,`Addtime`) VALUES ($fromid,$toid,'".addslashes($comment)."','".addslashes($user)."',NOW())";
			$this->objMysql->query($sql);
			
			//UPdate from term for the reason v11.5 duplicated Term URLName
// 			$sql = "UPDATE `term` SET UrlName = CONCAT(`UrlName` , '-old-merged') WHERE ID = $fromid";
// 			$this->objMysql->query($sql);

			//set term HasAffiliate
			$sql = "SELECT COUNT(*) FROM term_mapping WHERE HasAffiliate = 'yes' AND TermId = {$toid}";
			$hasAffiliate = $this->objMysql->getFirstRowColumn($sql);
			if ($hasAffiliate) {
				$sql = "UPDATE term SET HasAffiliate = 'YES' WHERE id = {$toid}";
				$this->objMysql->query($sql);
			}else{
				$sql = "UPDATE term SET HasAffiliate = 'NO' WHERE id = {$toid}";
				$this->objMysql->query($sql);
			}
			//Update term IsSubAffiliate
			$IsSubAffiliate = $this->checkIsSubAffiliate($toid);
			$IsSubAffiliate_str = $IsSubAffiliate?'YES':'NO';
			$sql = 'UPDATE term set IsSubAffiliate = "'.$IsSubAffiliate_str.'" WHERE id = '.$toid;
			$this->objMysql->query($sql);
			
		}
		
		public  function getTermStatus($term_id) {
			$sql = "SELECT STATUS FROM `term` WHERE ID = $term_id";
			$status=$this->objMysql->getFirstRowColumn($sql);
			return $status;
		}
		
		public function merchantExists($merchantid) {
			$sql = "SELECT * FROM normalmerchant WHERE ID = $merchantid";
			$qryId=$this->objMysql->query($sql);
			$count=$this->objMysql->getNumRows($qryId);
			$this->objMysql->freeResult($qryId);
			return $count;
		}
		
		public function getAllEditors() {
			$sql = "SELECT  DISTINCT AssignedEditor FROM `term` ";
			$res = $this->objMysql->getRows($sql);
//			echo "<pre>";print_r($res);
			$data =array();
			foreach ($res as $k => $v) {
				if ($v["AssignedEditor"] != "") {
					$data[$v["AssignedEditor"]] = $v["AssignedEditor"];
				};
			}
			return $data;
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
		
		function getTermByUrlName($name){
		 	$sql = "select * from term WHERE UrlName='".addslashes($name)."'";
//			if($isactive) $sql .= " and Status = 'Active'";
			$sql .= " LIMIT 1";
			$arrTmp = $this->objMysql->getFirstRow($sql);
			return $arrTmp;
		}
		
		function getCaptureTaskRecord($termid) {
			$sql = "SELECT * FROM term_thumb_capture_task WHERE TermId = $termid";
			$res = $this->objMysql->getFirstRow($sql);
			return $res;
		}
		
		function getTermCountryByTermID($termId) {
			if (! $termId) {
				return array();
			}
			$sql ="SELECT * FROM term_country WHERE TermId = $termId ";
			$res = $this->objMysql->getRows($sql, "CountryName");
			return $res;
		}
		
		function getCategoryById($id,$country) {
			$sql = "SELECT tc.*,c.`Name`,c.`ID`,c.ParentCategoryId FROM `term_category` tc LEFT JOIN `normalcategory_new` c ON tc.categoryid = c.id WHERE IsManualSelected ='YES' AND TermId = $id AND c.country = '$country'  ORDER BY `Name`";
			$res = $this->objMysql->getRows($sql, "CategoryId");
			return $res;
		}
		
		function getPrimaryCategoryId($termid) {
			$sql = "SELECT CategoryId FROM `term_category` WHERE TermId = $termid AND IsPrimary ='yes'";
			$res =  $this->objMysql->getFirstRowColumn($sql);
			return $res;
		}
		
		function getSuperTermByIds(array $termIds) {
			if (empty( $termIds )) {
				return array();
			}
			$termIds = implode(',', $termIds);
			$sql = "SELECT st.UrlName,stm.TermId FROM `super_term_mapping` stm , super_term st WHERE stm.superTermid = st.ID AND stm.TermId IN ($termIds)";
			$res = $this->objMysql->getRows($sql, "TermId");
				
			return $res;
		}
		
		function getTermCountryFromTermMappingByTermID($termId, $where='') {
			if (! $termId) {
				return array();
			}
			$sql ="SELECT *, Country as CountryName FROM term_mapping WHERE TermId = $termId $where";
			$res = $this->objMysql->getRows($sql, "CountryName");
			return $res;
		}
		
		public function doeditTermExtendInfo($data,$termid) {
			$date=date("Y-m-d H:i:s");
			$syncCouponTitleKeywords = array("coupon" => $data["coupon"], "deal" => $data["deal"]);
			$serializedSyncCouponTitleKeywords = serialize( $syncCouponTitleKeywords );
			
			$serializedMetaTitle = serialize( $data["title"] );
			$serializedMetaDescription = serialize( $data["description"] );
			$serializedMetaKeywords = serialize( $data["keywords"] );
			$sql="  UPDATE `term_extend` SET 
						`SyncCouponTitleKeywords` ='$serializedSyncCouponTitleKeywords',
						`MetaTitle` = '$serializedMetaTitle',
						`MetaDescription` = '$serializedMetaDescription',
						`MetaKeywords` = '$serializedMetaKeywords'
					WHERE TermId = $termid";
			$this->objMysql->query($sql);
		}
		
		function getTermByCategoryNew(array $categoryIDs) {
			if (empty($categoryIDs)) {
				return array();
			}
			$categoryIDs = implode(',', $categoryIDs);
			$sql = "SELECT * FROM term_category tc  WHERE tc.CategoryId IN ($categoryIDs)";
			$res = $this->objMysql->getRows($sql);
			return $res;
		}
		
		function getTermByCategoryNewAndType($_cateid, $type="STORE", $limit=20, $parentCategoryTree = array())
		{
			$objCategory = new NormalCategory($this->objMysql);
			if (count( $parentCategoryTree ) == 0) {//Top Level Category , get all children category
				$childrenCategories = $objCategory -> getChildrenCategoryNewIdsById(array ($_cateid));
				$cateIds = array_merge(array ($_cateid), $childrenCategories);
				// 				echo print_r($cateIds);exit();
			}elseif (count( $parentCategoryTree ) == 1) {//Medium Category , get all childre category and parent category
				$parentCategoryId = $objCategory -> getParentCategoryId($_cateid);
				$childrenCategories = $objCategory -> getChildrenCategoryNewIdsById(array ($_cateid));
				$cateIds = array_merge(array ($_cateid,$parentCategoryId),$childrenCategories);
			}else{//get self category
				$cateIds = array($_cateid);
			}
			$terms = $this->getTermByCategoryNew($cateIds);
			$termids = array();
			if (empty($terms)) {
				return array();
			}else{
				foreach ($terms as $val) {
					$termids[] = $val["TermId"];
				}
			}
			$_strWhere = " AND " . $this->getActiveCouponCnd("c");
			$_strWhere .= !empty($type) ? " AND t.Type='".$type."' AND t.`Status` = 'Active' " : "";
			$_strWhere .= "AND t.ID in (".implode(',', $termids).")";
			// 			$_strWhere .= "AND tc.`Status` = 'Online'";
			$_strLimit = is_int($limit) ? " LIMIT ".$limit : "";
			$sql = "select tc.TermId,t.*, count(*) as total from normalcoupon as c , termcoupon_relationship as tc , term as t where c.ID=tc.NormalCouponId AND tc.TermId=t.ID " . $_strWhere . " GROUP BY tc.TermId ORDER BY total DESC ".$_strLimit;
			// 			echo $sql;
			$res = $this->objMysql->query($sql);
			$arr = array();
			while($row = $this->objMysql->getRow($res)){
				$arr[$row["TermId"]] = $row;
			}
			return $arr;
		}
		
		function getActiveCouponCnd($table="")
		{
			if($table && substr($table,-1) != ".") $table .= ".";
			$whereStr = " (" . $table . "ExpireTimeInServer = '0000-00-00 00:00:00' or " . $table . "ExpireTimeInServer >= '".date('Y-m-d H:i:s')."') ";
			$whereStr .= " AND " . $table . "StartTimeInServer <= '".date('Y-m-d H:i:s')."' and " . $table . "IsActive = 'YES'";
			return $whereStr;
		}
		
		function getTermidIfSuperTerm($termlist=null){
			if($termlist && !empty($termlist)){
				$termId = array();
				foreach($termlist as $k=>$v){
					$termId[] = $v['ID'];
				}
				$sql = 'SELECT t.id FROM `term` AS t LEFT JOIN `super_term_mapping` AS stm ON t.id = stm.termid LEFT JOIN `super_term`  AS st ON stm.supertermid = st.id WHERE t.UrlName = st.UrlName AND t.id IN ('.join(',',$termId).')';
				$res = $this->objMysql->query($sql);
				$arr = array();
				while($row = $this->objMysql->getRow($res)){
					$arr[ $row['id'] ] = $row['id'];
				}
				return $arr;
			}else{
				return array();
			}
		}
		
		function getTermidIfSubTerm($termlist=null){
			if($termlist && !empty($termlist)){
				$termId = array();
				foreach($termlist as $k=>$v){
					$termId[] = $v['ID'];
				}
				$sql = 'SELECT TermId FROM `super_term_mapping` WHERE TermId IN ('.join(',',$termId).')';
				$res = $this->objMysql->query($sql);
				$arr = array();
				while($row = $this->objMysql->getRow($res)){
					$arr[ $row['TermId'] ] = $row['TermId'];
				}
				return $arr;
			}else{
				return array();
			}
		}
		
		function updateRealUrlByTermId($termId = ''){
			if(empty($termId))return true;
			
			$realUrl = $this->createRealUrlByTermId($termId);
			
			$sql = 'UPDATE `term` SET RealUrl = "'.$realUrl.'" WHERE ID = '.intval($termId);
			$this->objMysql->query($sql);
		}
		
		function createRealUrlByTermId($termId = ''){
			if(empty($termId))return '';
			
			$countries = getCountryList();
			$countries = changeCountryDisplayName( $countries );
			
			$termCountry = $this->getTermCountryNameByTermId($termId);
			$superTerm = $this->getSuperTermByIds(array($termId));
			$term = $this->getTermById($termId);
			
			
			$urlName = $term['UrlName'];
			$type = $term['Type'];
			$country = empty($termCountry)?'':$countries[$termCountry];
			$superUrlName = empty($superTerm)?'':$superTerm[$termId]['UrlName'];
			
			$realUrl = $this->ruleForRealUrl($urlName,$type,$country,$superUrlName);
			
			return $realUrl;
		}
		
		function ruleForRealUrl($urlName='',$type='',$country='',$superUrlName=''){
			
			if ( in_array(ucwords($urlName), array("Dell-outlet","Sears-outlet","Sears-home-services","Sears-partsdirect") ) || empty($superUrlName) ) {
				$name = $urlName;
			}else{
				$name = $superUrlName ;
			}
			$termType = strtolower(getTypeStr($type)).'s';
			$realUrl = '';
			if($type == 'STORE SINGLE COUNTRY' && !empty($country)){
				$realUrl = '/'.urlformatcountry($country).'/'.$termType.'/'.$name.'/';
			}else{
				$realUrl = '/'.$termType.'/'.$name.'/';
			}
			
			$realUrl = strtolower($realUrl);
			return $realUrl;
		}
		
		function getTermCountryNameByTermId($termId = ''){
			if(empty($termId))return '';
			
			$term_mapping = $this->getMapping($termId);
			$term = $this->getTermById($termId);
			
			$country = '';
			if(!empty($term_mapping)){
				foreach($term_mapping as $k=>$v){
					if(!empty($v["Country"])){
						$country = $v["Country"];
						break;
					}
				}
			}
			
			if(empty($country)){
				$country = $term['Country'];
			}
			
			return $country;
		}
		
		function getSimilarTerm($termid) {
			if(empty($termid)) return array();
			$termid = addslashes($termid);
			$sql = "SELECT * FROM `term_similar` WHERE TermId = $termid Order By `order`";
			$res = $this->objMysql->getRows($sql);
			
			return $res;
		}
		
		function deleteRelatedTerm($ids) {
			if (! is_array($ids) || empty($ids)) {
				return ;
			}else{
				$sql = 'DELETE FROM `term_similar` WHERE ID IN ('.addslashes( implode(',', $ids) ). ')';
				$this->objMysql->query($sql);
			}
		}
		
		function insertRelatedTerm($records) {
			if (! is_array($records) || empty($records)) {
				return ;
			}else{
				$sql = 'INSERT INTO `term_similar` (`Order`,`TermId`,`RelatedTermId`,`CouponId`,`AddTime`) VALUES ';
				$insertValues = array();
				foreach ($records as $val) {
					foreach ($val as $key => &$v) {
						$v = addslashes($v);
					}
					unset($v);
					$insertValues[] = "('{$val['order']}','{$val['termid']}','{$val['relatedtermid']}','{$val['couponid']}',NOW())";
				}
				$insertValues = implode(',', $insertValues);
// 				$insertValues = mysql_real_escape_string($insertValues);
				$sql .= $insertValues;
				$this->objMysql->query($sql);
			}
		}
		

		function updateRelatedTerm($records) {
			if (! is_array($records) || empty($records)) {
				return ;
			}else{
				foreach ($records as $val) {
					if (! intval($val['id'])) {
						continue;
					}
					foreach ($val as $key => &$v) {
						$v = addslashes($v);
					}
					unset($v);
					$sql = "UPDATE `term_similar` SET 
							`Order` = '{$val['order']}' , TermId = '{$val['termid']}', RelatedTermId = '{$val['relatedtermid']}' , CouponId = '{$val['couponid']}' WHERE ID = {$val['id']}";
// 					echo $sql.'<br>';
					$this->objMysql->query($sql);
				}
			}
		}
		
		function updateTermBlackWord($termId,$data){
			$sql = 'DELETE FROM term_blackwords WHERE termID = '.intval($termId);
			$this->objMysql->query($sql);
			
			foreach($data as $k=>$v){
				$sql = 'INSERT INTO term_blackwords SET 
						termID = '.intval($termId).',
						words = "'.addslashes($v['black_word']).'",
					    category = "'.addslashes($v['category']).'",
					    `status` = "'.addslashes($v['status']).'",
					    remark = "'.addslashes($v['remark']).'"';
				$this->objMysql->query($sql);
			}
		}
		
		function getTermBlackWord($termId,$status=null){
			$where_str = '';
			if($status){
				$where_str = ' AND `status` = "'.$status.'"';
			}
			$sql = 'SELECT * FROM term_blackwords WHERE termID = '.intval($termId).$where_str;
			$res = $this->objMysql->getRows($sql);
			
			return $res;
		}
		
		function checkTermBlackWord($termid,$checkData){
			$blackwords = $this->getTermBlackWord($termid,'Active');
			$temp = array();
			foreach($blackwords as $v){
				$temp[$v['category']][] = $v['words'];
			}
			
			$type = '';
			$word = '';
			$flag = true;
			//check title
 			if(!empty($checkData['Code'])){
 				if(!empty($temp['Code'])){
 					foreach($temp['Code'] as $v){
 						if(trim($checkData['Code']) == trim($v)){
 							$flag = false;
 							$type = 'Code';
 							$word = $v;
 						}
 					}
 				}
 				if(!empty($temp['All'])){
 					foreach($temp['All'] as $v){
 						if(trim($checkData['Code']) == trim($v)){
 							$flag = false;
 							$type = 'Code';
 							$word = $v;
 						}
 					}
 				}
 			}
 			
 			if(!empty($checkData['Title'])){
 				$title_arr = explode(' ',trim($checkData['Title']));
 				
 				if(!empty($temp['Title'])){
 					foreach($temp['Title'] as $v){
 						if(in_array(trim($v),$title_arr)){
 							$flag = false;
 							$type = 'Title';
 							$word = $v;
 						}
 					}
 				}
 				if(!empty($temp['All'])){
 					foreach($temp['All'] as $v){
 						if(in_array(trim($v),$title_arr)){
 							$flag = false;
 							$type = 'Title';
 							$word = $v;
 						}
 					}
 				}
 			}
 			
 			if(!empty($checkData['Desc'])){
 				$desc_arr = explode(' ',trim($checkData['Desc']));
 				
 				if(!empty($temp['Desc'])){
 					foreach($temp['Desc'] as $v){
 						if(in_array(trim($v),$desc_arr)){
 							$flag = false;
 							$type = 'Desc';
 							$word = $v;
 						}
 					}
 				}
 				if(!empty($temp['All'])){
 					foreach($temp['All'] as $v){
 						if(in_array(trim($v),$desc_arr)){
 							$flag = false;
 							$type = 'Desc';
 							$word = $v;
 						}
 					}
 				}
 			}
 			
 			$res = array();
 			if(!$flag){
 				$msg = 'Sorry! The word \''.$word.'\' can not exist in '.$type.'.';
 			}
 			
 			$res['flag'] = $flag;
 			$res['msg'] = $msg;
 			
			return $res;
		}
		
		function getList($filter = array()){
			$where = array();
			if($filter['id']){
				$where[] = ' ID IN ("'.join('","',$filter['id']).'")';
			}
			$where_str = ' WHERE '.join(' AND ',$where);
			$sql = 'SELECT * FROM term '.$where_str;
			$res = $this->objMysql->query($sql);
			$data = array();
			while($row=$this->objMysql->getRow($res)){
				$data[$row["ID"]] = $row;
			}
			return $data;
		}
		
		
		
		
		
		function get_term_by_id($id, $condition = array(), $has_url = true) {
			if (!(int)$id)
				return;
			
			$sql = "select * from term where `ID` = " . addslashes($id);
			if (!empty($condition)) {
				foreach ($condition as $k => $v) {
					if ($k == 'status') {
						$sql .= " and `Status` = '" . addslashes($v) . "'";
					}
					else {
						continue;
					}
				}
			}
	
			$q = $this->objMysql->query($sql);
			$r = $this->objMysql->getRow($q);
			if (is_array($r) && !empty($r)) {
				$r['ImageUrl'] = $this->get_term_image($r['Image'], 'big');
				$r['ImageUrlSmall'] = $this->get_term_image($r['ImageSmall'], 'big');
				$r['country_name'] = $this->get_country_name_by_code($r['CountryCode']);
				
				$term_mapping = $this->get_mapping_by_term_id($r['ID']);
				$promomerchantid = 0;
	
				foreach ($term_mapping as $key => $value) {
					if ($value["ObjectType"] == 'Merchant') {
						$promomerchantid = $this->getPromoMerchantId($value["Site"],$value["ObjectId"]);
						break;
					}
				}
				if ($promomerchantid) {
					$r['merchant_rd_url'] = getRdUrl('merchant', $promomerchantid, 'manu', $r['DefaultUrl']);
				}
				
				$this->recheck_term_info($r);
				if ($has_url) {
					$term_url_info = $this->get_obj_url_data_final('term', $id);
					if (!empty($term_url_info))
						$r['term_request_uri'] = $term_url_info['RequestPath'];
				}
			}
			
			return $r;
		}

		function recheck_term_info(&$data){
			#图片处理,如果主term没有图片。则用子term的图片
			
			$sub_term_arr = $this->get_sub_term_by_parent_id($data['ID']);
			if(empty($sub_term_arr)){
				return;
			}
	
			foreach($sub_term_arr as $k=>$v){
				if(empty($data['Image']) && !empty($v['Image'])){
					$data['Image'] = $v['Image'];
				}
	
				if(empty($data['ImageSmall']) && !empty($v['ImageSmall'])){
					$data['ImageSmall'] = $v['ImageSmall'];
				}
	
				if($data['ImageUrl'] == '/image/no_image.png' && $v['ImageUrl'] != '/image/no_image.png'){
					$data['ImageUrl'] = $v['ImageUrl'];
				}
	
				if($data['ImageUrlSmall'] == '/image/no_image.png' && $v['ImageUrlSmall'] != '/image/no_image.png'){
					$data['ImageUrlSmall'] = $v['ImageUrlSmall'];
				}
	
				if((!isset($data['merchant_rd_url']) || empty($data['merchant_rd_url'])) && (isset($v['merchant_rd_url']) && !empty($v['merchant_rd_url']))){
					$data['merchant_rd_url'] = $v['merchant_rd_url'];
				}
			}
		}
		
		function get_sub_term_by_parent_id($id, $condition = array(), $sort = 'SubTermDisplayOrder asc') {
			if (!(int)$id)
				return;
			
			$sql = "select * from term where `ParentTermId` = " . addslashes($id);
			if (!empty($condition)) {
				foreach ($condition as $k => $v) {
					if ($k == 'status') {
						$sql .= " and `Status` = '" . addslashes($v) . "'";
					}
					else {
						continue;
					}
				}
			}
			$sql .= " order by " . $sort;
			
			$r = $this->objMysql->getRows($sql);
			if (is_array($r) && !empty($r)) {
				foreach ($r as $k => $v) {
					$r[$k]['ImageUrl'] = $this->get_term_image($v['Image'], 'big');
					$r[$k]['ImageUrlSmall'] = $this->get_term_image($v['ImageSmall'], 'big');
					$r[$k]['country_name'] = $this->get_country_name_by_code($v['CountryCode']);
					
					$term_mapping = $this->get_mapping_by_term_id($v['ID']);
					$promomerchantid = 0;
					foreach ($term_mapping as $key => $value) {
						if ($value["ObjectType"] == 'Merchant') {
							$promomerchantid = $this->getPromoMerchantId($value["Site"],$value["ObjectId"]);
							break;
						}
					}
					if ($promomerchantid) {
						$r[$k]['merchant_rd_url'] = getRdUrl('merchant', $promomerchantid, 'manu', $v['DefaultUrl']);
					}
					
					$r[$k]['country_name'] = $this->get_country_name_by_code($v['CountryCode']);
				}
			}
			
			return $r;
		}
		
		function get_term_image($image_name, $type = 'big', $term_type = 'STORE'){
			$image_path = '';
			if(dirname($image_name) == '.'){
				$image_path = '/image/term/' . $image_name;
			}else{
				if($image_name[0] == '/')
					$image_path = '/image'.$image_name;
				else
					$image_path = '/image/'.$image_name;
			}
	
			if ($term_type != 'STORE') {
				return !empty($image_name) && file_exists(FRONT_ROOT . $image_path) ?  $image_path : '';
			}
			else {
				return !empty($image_name) && file_exists(FRONT_ROOT . $image_path) ?  $image_path : ($type == 'big' ? '/image/no_image.png' : '/image/small_no_image.png');
			}
		}
		
		function get_country_name_by_code($country_code) {
			if (empty($country_code))
				return;
	
			$sql = "select * from data_dictionary where Code = '" . addslashes($country_code) . "'";
			$q = $this->objMysql->query($sql);
			$r = $this->objMysql->getRow($q);
			
			return isset($r['Name']) ? $r['Name'] : '';
			
		}
		
		function get_mapping_by_term_id($termid){
			if (!(int)$termid)
				return;
			
			$sql = "select * from term_mapping where TermId = " . addslashes($termid) . " order by `Order` asc";
			$r = $this->objMysql->getRows($sql);
			
			return $r;
		}
		
		function getPromoMerchantId($site,$merchantid){
			$sql="SELECT ID FROM `merchant_mapping` WHERE `Site` = '$site' AND M_ID = $merchantid ";
			$res = $this->objMysql->getFirstRowColumn($sql);
			return $res;
		}
		
		function get_coupons_by_term_id($termid, $start_time_from, $start_time_to) {
			if (!(int)$termid)
				return;
			
			$sql = "select a.DisplayOrder, b.*, c.Name as MerchantName, c.logo as MerchantLogo 
					from termcoupon_relationship_front as a 
					left join coupon as b on a.CouponId = b.ID 
					left join normalmerchant as c on c.ID = b.MerchantID 
					where a.TermId = " . addslashes($termid) . " 
					and a.`Status` = 'online' 
					and (b.CsgExpireTime = '' or b.CsgExpireTime = '0000-00-00 00:00:00' or b.CsgExpireTime > NOW()) 
					and b.CsgStartTime >= '" . addslashes($start_time_from) . "' and b.CsgStartTime < '" . addslashes($start_time_to) . "' 
					order by b.CsgType asc limit 5";
	
			$r = $this->objMysql->getRows($sql);
			if (is_array($r) && !empty($r)) {
				foreach ($r as $k => $v) {
					if ($v['Source'] == 'csg') {
						$r[$k]['coupon_rd_url'] = base64_encode('coupon') . '|' . base64_encode($v['ID']) . '|' . base64_encode('sync') . '|' . base64_encode('');
					}
					else {
						$r[$k]['coupon_rd_url'] = base64_encode('manualpromotion') . '|' . base64_encode($v['ID']) . '|' . base64_encode('manu') . '|' . base64_encode($v['CsgDstUrl']);
					}
	
					if((strcmp($v['CsgExpireTime'], '0000-00-00 00:00:00') != 0) && strcmp($v['CsgExpireTime'], date("Y-m-d 00:00:00")) < 0){
						$r[$k]["isExpiredCoupon"] = 1;
					}
					$r[$k]['displayTitle'] = get_coupon_display_title($v);
					if (!$r[$k]['displayTitle']) {
						$r[$k]['displayTitle'] = $v['CsgTitle'];
					}
					$r[$k]["coupon_expire_date_simple"] = format_coupon_expire_date1($v['CsgExpireTime'],"");
				}
			}
			return $r;
		}
		
		function get_obj_url_data_final($ModelType,$OptDataId){
			$sql = 'SELECT * FROM rewrite_url WHERE ModelType = "'.$ModelType.'" AND OptDataId = '.intval($OptDataId).' AND isActivation = "yes"';	
			$q = $this->objMysql->query($sql);
			$r = $this->objMysql->getRow($q);
	
			if(empty($r)){
				return false;
			}
			
			$jumpId = $r['ID'];
			do{
				$sql = 'SELECT * FROM rewrite_url WHERE isActivation = "yes" AND ID = '.intval($jumpId);	
				$q = $this->objMysql->query($sql);
				$r = $this->objMysql->getRow($q);
	
				if(!$r){
					break;
				}
				if($r['IsJump'] == '301' || $r['IsJump'] == '302' || $r['IsJump'] == 'HIJACK'){
					$jumpId = $r['JumpRewriteUrlID'];
				}else{
					$jumpId = 0;
				}
			}while($jumpId);
			
	
			if(empty($r)){
				return false;
			}
	
			if($r['IsJump'] == '404'){
				return false;
			}elseif($r['IsJump'] == '404'){
				return false;
			}elseif($r['IsJump'] == 'NO'){
				return $r;
			}
		}
		
	}
	
}
?>