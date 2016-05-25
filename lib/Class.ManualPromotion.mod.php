<?php
/*
 * FileName: Class.ManualPromtion.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_MANUAL_PROMOTION__"))
{
	define("__MOD_CLASS_MANUAL_PROMOTION__",1);
	include_once 'Class.Affiliate.mod.php';
	class ManualPromtion
	{
		var $objMysql;
		
		function ManualPromtion($objMysql)
		{
			$this->objMysql = $objMysql;
		}
		
		function getManualPromotionById($id)
		{
			$manualId = intval($id);
			$sql = "SELECT * FROM `manual_promotion` WHERE ID = $manualId";
			$arr = $this->objMysql->getRows($sql);
			if(isset($arr[0])) return $arr[0];
			return array();
		}
		
		function getFilterCouponById($id)
		{
			$manualId = intval($id);
			$sql = "SELECT * FROM `user_submit_promotion` WHERE ID = $manualId";
			$arr = $this->objMysql->getRows($sql);
			if(isset($arr[0])) return $arr[0];
			return array();
		}
		
		function getActiveManualPromotionCnd($table="")
		{
			if($table && substr($table,-1) != ".") $table .= ".";
			$whereStr = " (" . $table . "ExpireTime = '0000-00-00 00:00:00' or " . $table . "ExpireTime >= '".date('Y-m-d H:i:s')."') ";
			$whereStr .= " AND " . $table . "StartTime <= '".date('Y-m-d H:i:s')."' and " . $table . "IsActive = 'YES'";
			return $whereStr;
		}
		
		function getAffUrlByMerchantId($promoMerchantId, $url) {
			if (!empty( $promoMerchantId )) {
				$objAff = new Affiliate($this->objMysql);
				$MerIDinAff = $objAff -> getMerIDinAffByMerchantId($promoMerchantId);
				if ( !empty($MerIDinAff) ) {
					$du = urlencode( $url );
					$am = urlencode( $MerIDinAff );
					$linkShareRequestUrl = "http://task01.megainformationtech.com/ajax/deepurl_ls.php?du={$du}&am={$am}";
// 			echo $linkShareRequestUrl;exit;
					$returnData = file_get_contents($linkShareRequestUrl);
					$returnData = json_decode($returnData, true);
					if (!empty($returnData) && $returnData["status"] == true) {
						$affUrl = $returnData["url"];
						return $affUrl;
					}
				}
			};
			
			return '';
		}
		
		function addManualPromotion($termid,$type,$cs_site,$code,$title,$url,$desc,$categoryid,$isActive,$editor,$addtime,$couponStartDate,$eDate,$sitewide,$merchantid,$affUrl,$pro_detail,$promotionOff,$country,$isOptionalMerchant) {
			$isActive = ($isActive == 1 ) ? 'YES' :'NO';
			$sitewide = ($sitewide == 1 ) ? 'YES' :'NO';
			$isOptionalMerchant = $isOptionalMerchant ? 'YES' :'NO';
			$requestAffUrl = $this-> getAffUrlByMerchantId($merchantid, $url);
			if (!empty($requestAffUrl)) {
				$affUrl = $requestAffUrl;
			}
			$sql = "INSERT INTO `manual_promotion` (
				  `Title`,
				  `Description`,
				  `Code`,
				  `Type`,
				  `MerchantID`,
				  `OptionalMerchant`,
				  `URL`,
				  `AffUrl`,
				  `Site`,
				  `CategoryID`,
				  `IsActive`,
				  `Editor`,
				  `AddTime`,
				  `StartTime`,
				  `ExpireTime`,
				  `SiteWide`,
				  `PromotionDetail`,
				  `PromotionOff`,
					`AddEditor`
				) 
				VALUES
				  ('".addslashes($title)."',
				   '".addslashes($desc)."',
				   '".addslashes($code)."',
				   ".addslashes($type).",
				   ".addslashes($merchantid).",
				   '".addslashes($isOptionalMerchant)."',
				   '".addslashes($url)."',
				   '".addslashes($affUrl)."',
				  '".addslashes($cs_site)."',
					".addslashes($categoryid).",
				  '".addslashes($isActive)."',
				  '".addslashes($editor)."',
				  '".addslashes($addtime)."',
				  '".addslashes($couponStartDate)."',
				  '".addslashes($eDate)."',
				  '".addslashes($sitewide)."',
				  '".addslashes($pro_detail)."',
				  '".addslashes($promotionOff)."',
				  '".addslashes($editor)."'
				  )";
			$this->objMysql ->query($sql);
			$last_insert_id = mysql_insert_id();
			//insert into `termmanualpromotion_relationship`
			
			$sql = "INSERT INTO `termmanualpromotion_relationship` (TermId,ManualPromotionId,Country,`AddTime`) VALUES ($termid,$last_insert_id,$country,'$addtime')";
			$this->objMysql ->query($sql);
//			$dsturl = LINK_ROOT."front/rd.php?type=manualpromotion&id=$last_insert_id";
//			echo $last_insert_id;
			return $last_insert_id;
//			exit;
		}
		
		function editManualPromotion($termid,$organictermid,$promotionid,$type,$cs_site,$code,$title,$url,$desc,$categoryid,$isActive,$editorname,$couponStartDate,$eDate,$sitewide,$merchantid,$affUrl,$pro_detail,$promotionOff,$country,$isOptionalMerchant){
			$isActive = ($isActive == 1 ) ? 'YES' :'NO';
			$sitewide = ($sitewide == 1 ) ? 'YES' :'NO';
			$isOptionalMerchant = $isOptionalMerchant ? 'YES' :'NO';
			$requestAffUrl = $this-> getAffUrlByMerchantId($merchantid, $url);
			if (!empty($requestAffUrl)) {
				$affUrl = $requestAffUrl;
			}
			$sql = "UPDATE 
					  `manual_promotion` 
					SET
					  Title = '".addslashes($title)."',
					  `Description` = '".addslashes($desc)."',
					  `Code` = '".addslashes($code)."',
					  `Type` = $type,
					  `MerchantID` = $merchantid,
					  `URL` = '".addslashes($url)."',
					  `AffUrl` = '".addslashes($affUrl)."',
					  `Site` = '".addslashes($cs_site)."',
					  `CategoryID` = ".addslashes($categoryid).",
					  `IsActive` = '".addslashes($isActive)."',
					  `Editor` = '".addslashes($editorname)."',
					  `StartTime` = '".addslashes($couponStartDate)."',
					  `ExpireTime` = '".addslashes($eDate)."' ,
					  `SiteWide` = '".addslashes($sitewide)."' ,
					  `PromotionDetail` = '".addslashes($pro_detail)."' ,
					  `PromotionOff` = '".addslashes($promotionOff)."' ,
					  `OptionalMerchant` = '".addslashes($isOptionalMerchant)."' 
					WHERE ID = $promotionid"; 
			$this->objMysql ->query($sql);
			
			//update `termmanualpromotion_relationship`
//			$sql = "UPDATE `termmanualpromotion_relationship` SET TermId = $termid ,ManualPromotionId = $promotionid ,Country = '".addslashes($country)."' WHERE TermId = $organictermid AND ManualPromotionId = $promotionid";
//			$this->objMysql ->query($sql);;
		}
		
		function getPromotionTermRelationshipByPromotion($promotionid) {
			$sql = "SELECT  tr.*,t.`Name` FROM `termmanualpromotion_relationship` tr ,term t WHERE tr.`TermId`= t.`ID` AND ManualPromotionId =  $promotionid";
			$relation = $this->objMysql->getRows($sql);
			return  $relation;
			
		}
		
		function getPromotionTermRelationshipByPromotions($promotionids,$keycoloum) {
			if (!$promotionids) {
				return array();
			}
			$sql = "SELECT  tr.* FROM `termmanualpromotion_relationship` tr  WHERE  ManualPromotionId IN (".implode(',', $promotionids).")";
			$relation = $this->objMysql->getRows($sql,$keycoloum);
			return  $relation;
			
		}
		
		function deleteMapping($termid,$promotionid) {
			$sql = "DELETE FROM `termmanualpromotion_relationship` WHERE TermId = $termid AND ManualPromotionId = $promotionid";
			$this->objMysql->query($sql);
		}
		
		function addTermPromotionRelation($termid,$promotionid,$country) {
			$adddate = date('Y-m-d H:i:s');
			$sql = "INSERT INTO `termmanualpromotion_relationship` (TermId,ManualPromotionId,Country,`AddTime`) VALUES ($termid,$promotionid,$country,'$adddate')";
			$this->objMysql->query($sql);
		}
		
		function editTermPromotionRelation($termid,$promotionid,$country,$organictermid) {
			$sql = "UPDATE `termmanualpromotion_relationship` SET TermId = $termid ,ManualPromotionId = $promotionid,`Country` = $country WHERE TermId = $organictermid AND ManualPromotionId = $promotionid";
			$this->objMysql->query($sql);
		}
		
		function getAllRelatedMerchant($promotionId) {
			if (!$promotionId) {
				return array();
			}
			$sql = "SELECT 
					  tm.Site AS site,
					  ObjectId AS id,
					  m.Name AS name ,
					  m.id	AS `mid`
					FROM
					  termmanualpromotion_relationship tr 
					  JOIN term_mapping tm 
					    ON tr.termid = tm.termid 
					  JOIN merchant_mapping mm ON (ObjectId,tm.site) = (mm.m_id,mm.site)
					  JOIN normalmerchant m 
					    ON mm.id = m.id 
					WHERE ManualPromotionId = $promotionId
					  AND tm.objecttype = 'merchant' ";
//			echo $sql;
			$res = $this->objMysql->getRows($sql,"id");
			return $res;
		}
		
		function getMappingMerchantByTermId($termid) {
			if (!$termid) {
				return array();
			}
			$sql = "SELECT tm.Site AS site, ObjectId AS id ,m.Name AS name,m.id AS mid 
					FROM term_mapping tm 
					JOIN merchant_mapping mm ON (ObjectId,tm.site) = (mm.m_id,mm.site)
					  JOIN normalmerchant m 
					    ON m.id = mm.id 
					WHERE tm.termid = $termid 
					  AND tm.objecttype = 'merchant' ";
//			echo $sql;
			$res = $this->objMysql->getRows($sql,"id");
			return $res;
		}
		
		public function getActiveManualPromotionVoteRecords($where) {
   			$sql = "SELECT 
					   m.`ID` ,polls_status VoteStatus
					  FROM
					    `polls_iswork` p,
					    manual_promotion m 
					  WHERE p.polls_style_id = m.id 
					    AND p.polls_style = 'manualpromotion' 
					    AND ".$this->getActiveManualPromotionCnd('m')." $where "." 
					  ORDER BY polls_style_id,
					    poll_timestamp DESC ";
   			$row = $this->objMysql->getRows($sql);
   			return $row;
   		}
   		
   		public function getLastestManualPromotionVote($lastest_num,$couponids) {
   			$where = "";
   			if ($couponids) {
   				$where = "AND m.id IN (" .implode(',', $couponids).") ";
   			}
   			$voteRecords = $this->getActiveManualPromotionVoteRecords($where);
   			$res = array();
   			foreach ( $voteRecords as $k => $v ) {
				if (count ( $res [$v ['ID']] ) < $lastest_num) {
					$res [$v ['ID']] [] = $v ['VoteStatus'];
				} else {
					continue;
				}
			}
			return $res;
		}
		
		public function getVoteAlertManualPromotionIDs($lastest_vote_num,$couponids = array()) {
			$voteRecords = $this->getLastestManualPromotionVote($lastest_vote_num,$couponids);
			$couponIds = NormalCoupon::getAlertCouponIds($voteRecords,$lastest_vote_num);
			return $couponIds;
		}
		
		public function getManualPromoitonByTermID($termid,$indexColoum = ""){
   			$sql="SELECT * FROM `termmanualpromotion_relationship` WHERE TermId = $termid";
   			$row = $this->objMysql->getRows($sql,$indexColoum);
   			return $row;
   		
		}
		
		public function getMerchantId($cs_merchantid,$cs_site){
   			$sql="SELECT ID FROM `merchant_mapping` WHERE M_ID = $cs_merchantid And Site = '$cs_site'";
   			$id = $this->objMysql->getFirstRowColumn($sql);
   			return $id;
   		
		}
		
		public function getCSMerchantByID($merchantid){
   			$sql="SELECT * FROM `merchant_mapping` WHERE ID = $merchantid";
   			$cs_merchant= $this->objMysql->getFirstRow($sql);
   			return $cs_merchant;
   		
		}
		
		
		public function checkPromotionByTermAndCode($promotionCheckData){
			if(!isset($promotionCheckData['termid']) || empty($promotionCheckData['termid']))return false;
			if(!isset($promotionCheckData['couponStartDate']) || empty($promotionCheckData['couponStartDate']))return false;
			if(!isset($promotionCheckData['eDate']) || empty($promotionCheckData['eDate']))return false;
			//没有code直接通过
			if(!isset($promotionCheckData['code']) || empty($promotionCheckData['code']))return true;
			
			//如果有code
			
			//如果有promotionid则为编辑需要查看预期有关联的所有term。如果没有promotionid则只需要查看当前term
			$termId = array();
			if(isset($promotionCheckData['promotionid']) || !empty($promotionCheckData['promotionid'])){
				$arr_relation = $this->getPromotionTermRelationshipByPromotion($promotionCheckData['promotionid']);
				foreach($arr_relation as $k=>$v){
					$termId[] = $v['TermId'];
				}
			}
			
			
			//获取同term下的promotion
			$row_promotion = array();
			
			$where_arr = array();
			$where_arr[] = 'mp.`Code` = "'.addslashes($promotionCheckData['code']).'"';
			$where_arr[] = 'mp.`IsActive` = "YES"';
			if(isset($promotionCheckData['promotionid']) || !empty($promotionCheckData['promotionid'])){
				$where_arr[] = 'mp.`ID` != '.intval($promotionCheckData['promotionid']);
				$where_arr[] = 'tr.`TermId` IN ('.join(',',$termId).')';
			}else{
				$where_arr[] = 'tr.`TermId` = '.intval($promotionCheckData['termid']);
			}
			$where_str = ' WHERE '.join(' AND ',$where_arr);
			
			$sql = 'SELECT 
					  tr.`TermId`,mp.`Code`,mp.`IsActive`,mp.`ExpireTime`,mp.`StartTime`,mp.`ID`
					FROM
					  `termmanualpromotion_relationship` AS tr 
					  LEFT JOIN manual_promotion AS mp 
					    ON tr.ManualPromotionId = mp.ID '.$where_str;
			$row_promotion = $this->objMysql->getRows($sql);
			
			//获取同term下的 coupon
			$row_coupon = array();
			
			$where_arr = array();
			$where_arr[] = 'c.`Code` = "'.addslashes($promotionCheckData['code']).'"';
			$where_arr[] = 'tr.`status` = "Online"';
			$where_arr[] = 'c.`IsActive` = "YES"';
			if(isset($promotionCheckData['promotionid']) || !empty($promotionCheckData['promotionid'])){
				$where_arr[] = 'tr.`TermId` IN ('.join(',',$termId).')';
			}else{
				$where_arr[] = 'tr.`TermId` = '.intval($promotionCheckData['termid']);
			}
			$where_str = ' WHERE '.join(' AND ',$where_arr);
			
			$sql = 'SELECT 
					  tr.`TermId`,tr.status,c.Code,c.IsActive,c.ExpireTime,c.StartTime,c.ID 
					FROM 
					  `termcoupon_relationship` AS tr 
				      LEFT JOIN normalcoupon AS c 
					    ON tr.normalCouponid = c.id'.$where_str;
			
			$row_coupon = $this->objMysql->getRows($sql);
			
			//合并row
			$row = array_merge($row_promotion,$row_coupon);

			$promotionStartInt = strtotime($promotionCheckData['couponStartDate']);
			$promotionExpireInt = strtotime($promotionCheckData['eDate']);
			
			if($promotionCheckData['couponStartDate'] == '0000-00-00 00:00:00' && $promotionCheckData['eDate'] == '0000-00-00 00:00:00')return false;
			
			$flag = true;
			foreach($row as $k=>$v){
				$thisStartInt = strtotime($v['StartTime']);
				$thisExpireInt = strtotime($v['ExpireTime']);
				
				if($v['StartTime'] == '0000-00-00 00:00:00' && $v['ExpireTime'] == '0000-00-00 00:00:00'){
					$flag = false;
					break;
				}elseif($v['StartTime'] == '0000-00-00 00:00:00' && $v['ExpireTime'] != '0000-00-00 00:00:00'){
					if($promotionCheckData['couponStartDate'] == '0000-00-00 00:00:00' || $promotionStartInt < $thisExpireInt){
						$flag = false;
						break;
					}
				}elseif($v['StartTime'] != '0000-00-00 00:00:00' && $v['ExpireTime'] == '0000-00-00 00:00:00'){
					if($promotionCheckData['eDate'] == '0000-00-00 00:00:00' || $promotionExpireInt > $thisStartInt){
						$flag = false;
						break;
					}
				}else{
					if($promotionCheckData['couponStartDate'] == '0000-00-00 00:00:00' && $promotionExpireInt > $thisStartInt){
						$flag = false;
						break;
					}elseif($promotionCheckData['eDate'] == '0000-00-00 00:00:00' && $promotionStartInt < $thisExpireInt){
						$flag = false;
						break;
					}elseif(
							($promotionStartInt >=$thisStartInt && $promotionStartInt <= $thisExpireInt ) ||
							($promotionExpireInt >=$thisStartInt && $promotionExpireInt <= $thisExpireInt ) || 
							($promotionStartInt <= $thisStartInt && $promotionExpireInt >= $thisExpireInt )
							){
						$flag = false;
						break;
					}
				}
			}
// 			echo '<pre>';print_r($promotionCheckData);exit();
			return $flag;
		}
		
		function updateFilterCoupon($promotionid,$status){
			$sql = 'UPDATE user_submit_promotion SET `status` = "'.$status.'" WHERE ID = '.intval($promotionid);
			$this->objMysql->query($sql);
		}
		
		function addFilterCouponRelationship($filter_coupon_id,$promotionid){
			$sql = 'UPDATE user_submit_promotion SET `promotionID` = '.intval($promotionid).' WHERE ID = '.intval($filter_coupon_id);
			$this->objMysql->query($sql);
		}

	}		
}
?>