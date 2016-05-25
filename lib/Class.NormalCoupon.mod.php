<?php
/*
 * FileName: Class.Coupon.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_NORMAL_COUPON__"))
{
	define("__MOD_CLASS_NORMAL_COUPON__",1);

	include_once(INCLUDE_ROOT . "func/front.func.php");
	
	class NormalCoupon
	{
		var $objMysql;
		
		function NormalCoupon($objMysql)
		{
			$this->objMysql = $objMysql;
		}
		
		function getActiveCouponCnd($table="")
		{
			if($table && substr($table,-1) != ".") $table .= ".";
			$whereStr = " (" . $table . "CsgExpireTimeInServer = '0000-00-00 00:00:00' or " . $table . "CsgExpireTimeInServer >= '".date('Y-m-d H:i:s')."') ";
			$whereStr .= " AND " . $table . "CsgStartTimeInServer <= '".date('Y-m-d H:i:s')."' and " . $table . "CsgIsActive = 'YES'";
			return $whereStr;
		}
		
		function getExpiredCouponCnd()
		{
			$whereStr = "ExpireTimeInServer < '".date('Y-m-d H:i:s')."' and ExpireTimeInServer <> '0000-00-00 00:00:00' and IsActive = 'YES'";
			return $whereStr;
		}
		
		function getCouponCount($whereStr="")
		{
			$total = 0;
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = "select count(*) as cnt from normalcoupon $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return $total;
		}

		function getDealCount($whereStr="")
		{
			$total = 0;
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = "select count(*) as cnt from deal $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return $total;
		}
		
	function getCouponListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false)
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, Title, Code, MerchantID, Remark, DstUrl, CategoryID, " .
					"date_format(AddTime, '%Y-%m-%d') as AddTime, ExpireTimeInServer ExpireTime, AffUrl, " .
					"ImgUrl, `Restrict`, ClickCnt, IsActive, StartTimeInServer StartTime, " .
					"Type, FreeShipping, FreeGift, FreeSample, ClearanceSales," .
					"SiteWide, Tag, Editor, IsExclusive " .
					"from normalcoupon $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
					$arr[] = intval($arrTmp['ID']);
				else
				{
				$arr[$i]['id'] = intval($arrTmp['ID']);
				$arr[$i]['code'] = trim($arrTmp['Code']);
				$arr[$i]['title'] = trim($arrTmp['Title']);
				$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
				$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
				$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
				$arr[$i]['remark'] = trim($arrTmp['Remark']);
				$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
				$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
				$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
				$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
				$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
				$arr[$i]['starttime'] = trim($arrTmp['StartTime']);
				$arr[$i]['isactive'] = trim($arrTmp['IsActive']) == 'YES' ? 1 : 0;
				
				$arr[$i]['type'] = intval($arrTmp['Type']);
				$arr[$i]['freeshipping'] = trim($arrTmp['FreeShipping']) == 'YES' ? 1 : 0;
				$arr[$i]['freegift'] = trim($arrTmp['FreeGift']) == 'YES' ? 1 : 0;
				$arr[$i]['freesample'] = trim($arrTmp['FreeSample']) == 'YES' ? 1 : 0;
				$arr[$i]['clearance'] = trim($arrTmp['ClearanceSales']) == 'YES' ? 1 : 0;
				$arr[$i]['sitewide'] = trim($arrTmp['SiteWide']) == 'YES' ? 1 : 0;
				$arr[$i]['tag'] = trim($arrTmp['Tag']);
				$arr[$i]['editor'] = trim($arrTmp['Editor']);
				$arr[$i]['isexclusive'] = trim($arrTmp['IsExclusive']);
				$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getDealIdByNormalCouponID($couponid){
			$sql = "select ID from deal where NormalCouponID='{$couponid}' limit 1";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			if(empty($arrTmp) || empty($arrTmp["ID"])){
				return false;
			}
			return $arrTmp["ID"];
		}

		function getCouponIdsInDeal(array $arrCouponIds){
			$arrRes = array();
			if(count($arrCouponIds)>0){
				$sql = "select ID, NormalCouponID from deal where NormalCouponID in (".implode(",", $arrCouponIds).")";
				$qryId = $this->objMysql->query($sql);
				while($arrTmp = $this->objMysql->getRow($qryId)){
					$arrRes[$arrTmp["NormalCouponID"]] = $arrTmp["ID"];
				}
			}
			return $arrRes;
		}

		function getDealListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false)
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, NormalCouponID, Title, NewPrice, OldPrice, PromotionDetail, PromotionOff, MerchantID, Description, DstUrl, CategoryID, " .
					"date_format(AddTime, '%Y-%m-%d') as AddTime, ExpireTime, AffUrl, " .
					"ImgUrl, `Restrict`, IsActive, StartTime, " .
					"Type, FreeShipping, FreeGift, FreeSample, ClearanceSales," .
					"SiteWide, Tag, Editor " .
					"from deal $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
					$arr[] = intval($arrTmp['ID']);
				else
				{
				$arr[$i]['id'] = intval($arrTmp['ID']);
				$arr[$i]['normalcouponid'] = intval($arrTmp['NormalCouponID']);
				//$arr[$i]['code'] = trim($arrTmp['Code']);
				$arr[$i]['title'] = trim($arrTmp['Title']);
				$arr[$i]['newprice'] = trim($arrTmp['NewPrice']);
				$arr[$i]['oldprice'] = trim($arrTmp['OldPrice']);
				$arr[$i]['pro_detail'] = trim($arrTmp['PromotionDetail']);
				$arr[$i]['pro_off'] = trim($arrTmp['PromotionOff']);
				$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
				$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
				//$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
				$arr[$i]['remark'] = trim($arrTmp['Description']);
				$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
				$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
				$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
				$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
				$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
				$arr[$i]['starttime'] = trim($arrTmp['StartTime']);
				$arr[$i]['isactive'] = trim($arrTmp['IsActive']) == 'YES' ? 1 : 0;
				
				$arr[$i]['type'] = intval($arrTmp['Type']);
				$arr[$i]['freeshipping'] = trim($arrTmp['FreeShipping']) == 'YES' ? 1 : 0;
				$arr[$i]['freegift'] = trim($arrTmp['FreeGift']) == 'YES' ? 1 : 0;
				$arr[$i]['freesample'] = trim($arrTmp['FreeSample']) == 'YES' ? 1 : 0;
				$arr[$i]['clearance'] = trim($arrTmp['ClearanceSales']) == 'YES' ? 1 : 0;
				$arr[$i]['sitewide'] = trim($arrTmp['SiteWide']) == 'YES' ? 1 : 0;
				$arr[$i]['tag'] = trim($arrTmp['Tag']);
					$arr[$i]['editor'] = trim($arrTmp['Editor']);
				$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getCouponById($couponId)
		{
			$couponId = intval($couponId);
			$arr = $this->getCouponListByLimitStr("", " ID = '".addslashes($couponId)."'");
			if(isset($arr[0])) return $arr[0];
			return array();
		}
		
		function _instance($id,$column='*'){
			$row = array();
			$sql = 'SELECT '.$column.' FROM normalcoupon WHERE id = '.intval($id);
			$row = $this->objMysql->getFirstRow($sql);
			return $row;
		}

		function getDealById($couponId)
		{
			$couponId = intval($couponId);
			$arr = $this->getDealListByLimitStr("", " ID = '".addslashes($couponId)."'");
			if(isset($arr[0])) return $arr[0];
			return array();
		}

		function getActiveCouponByMerchantID($merID, $returnIdOnly = false)
		{
			$merID = intval($merID);
			$arr = $this->getCouponListByLimitStr("", " MerchantID = $merID AND ".$this->getActiveCouponCnd(), ' Code DESC, AddTime DESC', $returnIdOnly);
			return $arr;
		}
		
		function getAcitiveCouponCnt()
		{
			$cnt = $this->getCouponCount($this->getActiveCouponCnd());
			return $cnt;
		}
		
		function getCouponLastUpdateTime()
		{
			$time = '';
			$sql ="select max(AddTime) as t from normalcoupon";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$time = trim($arrTmp['t']);
			return $time;
		}

		function getExpiredCouponByMerchantID($merID, $returnIdOnly=false)
		{
			$merID = intval($merID);
			$arr = $this->getCouponListByLimitStr("limit 0, ".MERPAGE_EXP_COUPON_CNT, " MerchantID = $merID AND ".$this->getExpiredCouponCnd(), 'ExpireTime DESC', $returnIdOnly);
			return $arr;
		}

		function getActiveCouponCntByCateID($cateid, $arrExcludeCouponID="")
		{
			$cateid = intval($cateid);
			$whereStr = " CategoryID = $cateid AND ".$this->getActiveCouponCnd();
			if(is_array($arrExcludeCouponID) && count($arrExcludeCouponID))
			{
				$whereStr .= " AND ID NOT in (".implode(",", $arrExcludeCouponID).")";
			}
			$sql = "select count(*) as cnt from normalcoupon where $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$totalCnt = intval($arrTmp['cnt']);
			return $totalCnt;
		}
		
		function getActiveCouponByCateID($cateid, $limitStr = '', $returnIdOnly=false, $arrExcludeCouponID="")
		{
			$arr = array();
			$cateid = intval($cateid);
			$whereStr = " c.CategoryID = $cateid and (c.ExpireTime = '0000-00-00 00:00:00' " .
						"or c.ExpireTime >= '".date('Y-m-d H:i:s')."') and c.StartTime <= '".date('Y-m-d H:i:s')."' and c.IsActive = 'YES'";
			if(is_array($arrExcludeCouponID) && count($arrExcludeCouponID))
			{
				$whereStr .= " AND c.ID NOT in (".implode(",", $arrExcludeCouponID).")";
			}
			$orderStr = " ORDER BY c.AddTime DESC";
			$sql = "select SQL_CALC_FOUND_ROWS c.ID, c.Title, c.Code, c.MerchantID, c.Remark, c.DstUrl, c.CategoryID, c.Type, " .
					"date_format(c.AddTime, '%Y-%m-%d') as AddTime, c.ExpireTime, c.AffUrl, " .
					"c.ImgUrl, c.`Restrict`, c.ClickCnt, m.Name as MerchantName, m.logo as MerchantLogo, c.tag " .
					"from normalcoupon as c, normalmerchant as m Where c.MerchantID = m.ID AND" .
					"$whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
					$arr[] = intval($arrTmp['ID']);
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Remark']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['merchantname'] = trim($arrTmp['MerchantName']);
					$arr[$i]['merchantlogo'] = trim($arrTmp['MerchantLogo']);
					$arr[$i]['type'] = intval($arrTmp['Type']);
					$arr[$i]['tag'] = trim($arrTmp['tag']);
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getActiveCouponByType($type, $returnIdOnly=false) //new, hot, expire
		{
			$arr = array();
		$sql = "select c.ID, c.Title, c.Code, c.MerchantID, c.Remark, c.DstUrl, c.CategoryID, c.Type, c.tag, " .
					"date_format(c.AddTime, '%Y-%m-%d') as AddTime, c.ExpireTime, c.AffUrl, " .
					"c.ImgUrl, c.`Restrict`, c.ClickCnt, m.Name as MerchantName, m.logo as MerchantLogo " .
					"from normalcoupon as c, normalmerchant as m Where c.MerchantID = m.ID AND" .
					"(c.ExpireTime = '0000-00-00 00:00:00'or c.ExpireTime >= '".date('Y-m-d H:i:s')."') " .
					"and c.StartTime <= '".date('Y-m-d H:i:s')."' and c.IsActive = 'YES'";
			switch($type)
			{
				case 'new':
					$sql .= " ORDER BY ID DESC LIMIT 0, ".CATEPAGE_NEWEST_COUPON_CNT;
					break;
				case 'popular':
					$sql .= " ORDER BY c.ClickCnt DESC LIMIT 0, ".CATEPAGE_POP_COUPON_CNT;
					break;
				default:
					$sql .= " LIMIT 0, 0";
					break;
			}
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
				{
					$arr[] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Remark']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['merchantname'] = trim($arrTmp['MerchantName']);
					$arr[$i]['merchantlogo'] = trim($arrTmp['MerchantLogo']);
					$arr[$i]['type'] = intval($arrTmp['Type']);
					$arr[$i]['tag'] = trim($arrTmp['tag']);
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getActiveCouponByCouponID($arrCouponId, $returnIdOnly=false, $obj='', $objID=0)
		{
			$arr = array();
			if(!is_array($arrCouponId)  || !count($arrCouponId)) return $arr;
			$sql = "select c.ID, c.Title, c.Code, c.MerchantID, c.Remark, c.DstUrl, c.CategoryID, c.Type, " .
					"date_format(c.AddTime, '%Y-%m-%d') as AddTime, c.ExpireTime, c.AffUrl, " .
					"c.ImgUrl, c.`Restrict`, c.ClickCnt, m.Name as MerchantName, m.logo as MerchantLogo, c.Tag " .
					"from normalcoupon as c, normalmerchant as m Where c.MerchantID = m.ID AND" .
					"(c.ExpireTime = '0000-00-00 00:00:00'or c.ExpireTime >= '".date('Y-m-d H:i:s')."') ";
			if($obj == 'm' && $objID)
			{
				$sql .= " AND c.MerchantID = ".intval($objID);
			}
			else if($obj == 'c' && $objID)
			{
				$sql .= " AND c.CategoryID = ".intval($objID);
			}
			$sql .= " AND c.StartTime <= '".date('Y-m-d H:i:s')."' and c.IsActive = 'YES' and c.ID in (".implode(',', $arrCouponId).") order" .
							" by AddTime desc ";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
				{
					$arr[] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Remark']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['merchantname'] = trim($arrTmp['MerchantName']);
					$arr[$i]['merchantlogo'] = trim($arrTmp['MerchantLogo']);
					$arr[$i]['type'] = trim($arrTmp['Type']);
					$arr[$i]['tag'] = trim($arrTmp['Tag']);
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getCouponByCouponID(&$arrCouponId, $sorting="ORDER BY c.ID desc",$_arrWhere=array())
		{
			$arrWhere = array();
			$arrWhere[] = "c.MerchantID = m.ID";
			$arrWhere[] = "c.ID in (" . implode(',', $arrCouponId) . ")";
			foreach($_arrWhere as $v) $arrWhere[] = $v;
			
			$arr = array();
			if(!is_array($arrCouponId)  || !count($arrCouponId)) return $arr;
			
			$sql = "select c.id, c.title, c.`code`, c.merchantid, c.remark, c.dsturl, c.categoryid, c.`type`, " .
					"date_format(c.AddTime, '%Y-%m-%d') as `addtime`, c.ExpireTime as expiration, c.affurl, " .
					"c.imgurl, c.`restrict`, c.clickcnt, m.Name as merchantname, m.logo as merchantlogo, c.tag " .
					"from normalcoupon as c, normalmerchant as m Where " . implode(" and ",$arrWhere) . " $sorting";
			return $this->objMysql->getRows($sql);
		}
		
		function getCouponBySearchCondition($kw, $returnIdOnly = false,$cond=array())
		{
			$arr = array();
			$arrWhere = array();
			$fulltextmatch = "MATCH(midnx.Content) AGAINST ('".addslashes($kw)."')";
			
			if(!isset($cond["pagesize"])) $cond["pagesize"] = 48;
			if(!isset($cond["page"])) $cond["page"] = 1;
			if($cond["page"] <= 0) $cond["page"] = 1;
			$cond["limit"] = "limit " . (($cond["page"] - 1) * $cond["pagesize"]) . "," . $cond["pagesize"];
			if(!isset($cond["orderby"])) $cond["orderby"] = "order by ($fulltextmatch) desc";
			
			$arrWhere[] = "c.MerchantID = m.ID";
			$arrWhere[] = "(c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d H:i:s')."')";
			$arrWhere[] = "c.StartTime <= '".date('Y-m-d H:i:s')."'";
			$arrWhere[] = "c.IsActive = 'YES'";
			$arrWhere[] = "midnx.CouponID = c.ID";
			$arrWhere[] = $fulltextmatch;
			
			$sql = "select c.ID, c.Title, c.Code, c.MerchantID, c.Remark, c.DstUrl, c.CategoryID, c.Type, " .
					"date_format(c.AddTime, '%Y-%m-%d') as AddTime, c.ExpireTime, c.AffUrl, " .
					"c.ImgUrl, c.`Restrict`, c.ClickCnt, m.Name as MerchantName, m.logo as MerchantLogo " .
					"from normalcoupon as c, normalmerchant as m, normalsearchindex as midnx ".
					"Where " . implode(" and ",$arrWhere) . " " . $cond["orderby"] . " " . $cond["limit"];

			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
				{
					$arr[] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Remark']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['merchantname'] = trim($arrTmp['MerchantName']);
					$arr[$i]['merchantlogo'] = trim($arrTmp['MerchantLogo']);
					$arr[$i]['type'] = trim($arrTmp['Type']);
					$arr[$i]['coupon_rd_url'] = get_rewrited_url('redirect', 'coupon', $arrTmp['ID']);
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getCouponSearchCondition($kw)
		{
			
			$arrBlackList = array('%', '_', "\"", "+", "-", "<", ">", '&',
								   ",", "/", "{", "}", "(", ")", "|", ":", "*");
			$kw = trim($kw);
			foreach($arrBlackList as $v)
			{
				$kw = str_replace($v, ' ', $kw);
			}
			$arrTmp = explode(" ", $kw);
			$arrTmp = array_unique($arrTmp);
			$seCondition = array();
			foreach($arrTmp as $v)
			{
				if(!trim($v)) continue;
				$keyword = mysql_escape_string(trim($v));
				$seCondition[] = "((Title REGEXP '".$keyword."[ ,\.\?;:\&\-]+') " .
								" or (Title REGEXP '".$keyword."$')" .
								" or (Remark REGEXP '".$keyword."[ ,\.\?;:\&\-]+')" .
								" or (Remark REGEXP '".$keyword."$'))";
			}
			if(count($seCondition))
			{
				$seConditionStr = " (".implode(" AND ", $seCondition).") ";
				return $seConditionStr;
			}
			return "";
		}
		
		function getCouponByMerchantID($merID)
		{
			$merID = intval($merID);
			$arr = $this->getCouponListByLimitStr("", " MerchantID = $merID");
			return $arr;
		}
		
		function task_review_coupon($couponid)
		{
			global $site;
			if(empty($site)) die("site not defined.".__FILE__);
			$site = str_replace("snap","cs",$site);
			$url = "http://task.megainformationtech.com/ajax/review_coupon.php?site=$site&couponid=$couponid";
			file($url);
		}
		
			function addCoupon($title, $code, $mid, $remark, $dstUrl, $addTime, $expireTime,
							$affUrl, $imageUrl, $restrict, $categoryID, $startTime, $isActive,
							$type, $freeShipping, $freeGift, $freeSample, $clearance,$sitewide, $tags, $isexclusive)
		{
			$title = addslashes(trim($title));
			$code = addslashes(trim($code));
			$mid = intval($mid);
			$isActive = intval($isActive) == 1 ? 'YES' : 'NO';
			$categoryID = intval($categoryID);
			$remark = addslashes(trim($remark));
			$dstUrl = addslashes(trim($dstUrl));
	//			$addTime = addslashes(trim($addTime));
			$addTime = date("Y-m-d H:i:s");//fixed
			$expireTime = addslashes(trim($expireTime));
			$startTime = addslashes(trim($startTime));
			$affUrl = addslashes(trim($affUrl));
			$imageUrl = addslashes(trim($imageUrl));
			$restrict = addslashes(trim($restrict));

			$type = intval($type);
			$freeShipping = intval($freeShipping) == 1 ? 'YES' : 'NO';
			$freeGift = intval($freeGift) == 1 ? 'YES' : 'NO';
			$freeSample = intval($freeSample) == 1 ? 'YES' : 'NO';
			$clearance = intval($clearance) == 1 ? 'YES' : 'NO';
			$sitewide = intval($sitewide) == 1 ? 'YES' : 'NO';
			$tags = addslashes(trim($tags));
			
			global $PHP_AUTH_USER;
			$editorname = $PHP_AUTH_USER ? $PHP_AUTH_USER : $_SERVER["REMOTE_USER"];
			$sql = "INSERT INTO normalcoupon (Title, Code, MerchantID, Remark, DstUrl, " .
					"AddTime, ExpireTime, AffUrl, ImgUrl, `Restrict`, CategoryID, " .
					"StartTime, IsActive, Editor, Type, FreeShipping, FreeGift, FreeSample, ClearanceSales," .
					"SiteWide, Tag, IsExclusive) VALUES(" .
					"'$title','$code',$mid, '$remark', '$dstUrl', '$addTime', '$expireTime'," .
					"'$affUrl', '$imageUrl', '$restrict', $categoryID, '$startTime', '$isActive', " .
					"'".addslashes($editorname)."', $type, '$freeShipping', '$freeGift', '$freeSample', " .
					"'$clearance','$sitewide','$tags', '$isexclusive')";
			$qryId = $this->objMysql->query($sql);
			$new_coupon_id = $this->objMysql->getLastInsertId($qryId);
			//$this->task_review_coupon($new_coupon_id);
			return $new_coupon_id;
		}

			function addDeal($title, $normalcouponid, $newprice, $oldprice, $pro_detail, $pro_off, $mid, $remark, $dstUrl, $addTime, $expireTime,
							$affUrl, $imageUrl, $restrict, $categoryID, $startTime, $isActive,
							$type, $freeShipping, $freeGift, $freeSample, $clearance,$sitewide, $tags)
		{
			$title = addslashes(trim($title));
			//$code = addslashes(trim($code));
			$normalcouponid = intval($normalcouponid);
			$newprice = $newprice!=="" ? addslashes(trim($newprice)) : 'NULL';
			$oldprice = $oldprice!=="" ? addslashes(trim($oldprice)) : 'NULL';
			$pro_detail = addslashes(trim($pro_detail));
			$pro_off = addslashes(trim($pro_off));
			$mid = intval($mid);
			$isActive = intval($isActive) == 1 ? 'YES' : 'NO';
			$categoryID = intval($categoryID);
			$remark = addslashes(trim($remark));
			$dstUrl = addslashes(trim($dstUrl));
	//			$addTime = addslashes(trim($addTime));
			$addTime = date("Y-m-d H:i:s");//fixed
			$expireTime = addslashes(trim($expireTime));
			$startTime = addslashes(trim($startTime));
			$affUrl = addslashes(trim($affUrl));
			$imageUrl = addslashes(trim($imageUrl));
			$restrict = addslashes(trim($restrict));

			$type = intval($type);
			$freeShipping = intval($freeShipping) == 1 ? 'YES' : 'NO';
			$freeGift = intval($freeGift) == 1 ? 'YES' : 'NO';
			$freeSample = intval($freeSample) == 1 ? 'YES' : 'NO';
			$clearance = intval($clearance) == 1 ? 'YES' : 'NO';
			$sitewide = intval($sitewide) == 1 ? 'YES' : 'NO';
			$tags = addslashes(trim($tags));
			
			global $PHP_AUTH_USER;
			$editorname = $PHP_AUTH_USER ? $PHP_AUTH_USER : $_SERVER["REMOTE_USER"];
			$sql = "INSERT INTO deal (Title, NormalCouponID, MerchantID, Description, DstUrl, " .
					"AddTime, ExpireTime, AffUrl, ImgUrl, `Restrict`, CategoryID, " .
					"StartTime, IsActive, Editor, Type, FreeShipping, FreeGift, FreeSample, ClearanceSales," .
					"SiteWide, Tag, NewPrice, OldPrice, PromotionDetail, PromotionOff) VALUES(" .
					"'$title','$normalcouponid',$mid, '$remark', '$dstUrl', '$addTime', '$expireTime'," .
					"'$affUrl', '$imageUrl', '$restrict', $categoryID, '$startTime', '$isActive', " .
					"'".addslashes($editorname)."', $type, '$freeShipping', '$freeGift', '$freeSample', " .
					"'$clearance','$sitewide','$tags',$newprice,$oldprice,'$pro_detail','$pro_off')";
			$qryId = $this->objMysql->query($sql);
			$new_coupon_id = $this->objMysql->getLastInsertId($qryId);
			$this->task_review_coupon($new_coupon_id);
			return $new_coupon_id;
		}

		function updateCouponById($title, $code, $mid, $remark, $dstUrl, $addTime, $expireTime,
							$affUrl, $imageUrl, $restrict, $categoryID, $startTime, $isActive, $type, 
							$freeShipping, $freeGift, $freeSample, $clearance,$sitewide, $tags, $isexclusive, $couponId, $isbundle = 0,$needupdateexpiration = FALSE,$needupdatestarttime = FALSE,$editorname = '')
		{
			$couponId = intval($couponId);
			$title = addslashes(trim($title));
			$code = addslashes(trim($code));
			$mid = intval($mid);
			$categoryID = intval($categoryID);
			$remark = addslashes(trim($remark));
			$dstUrl = addslashes(trim($dstUrl));
			$addTime = addslashes(trim($addTime));
			$expireTime = addslashes(trim($expireTime));
			$affUrl = addslashes(trim($affUrl));
			$imageUrl = addslashes(trim($imageUrl));
			$restrict = addslashes(trim($restrict));
			$isActive = intval($isActive) == 1 ? 'YES' : 'NO';
			$startTime = addslashes(trim($startTime));
			
			$type = intval($type);
			$freeShipping = intval($freeShipping) == 1 ? 'YES' : 'NO';
			$freeGift = intval($freeGift) == 1 ? 'YES' : 'NO';
			$freeSample = intval($freeSample) == 1 ? 'YES' : 'NO';
			$clearance = intval($clearance) == 1 ? 'YES' : 'NO';
			$sitewide = intval($sitewide) == 1 ? 'YES' : 'NO';
			$tags = addslashes(trim($tags));
			$editorname = addslashes(trim($editorname));
			//do not update AffUrl and Tag if bundle  
			$isbundle = intval($isbundle);
			//TODO
			$coupon_site = $this->getSiteByCouponId($couponId);

			$sql = "UPDATE normalcoupon set Title = '$title', Code = '$code', MerchantID = $mid, " .
					"Remark = '$remark',";
			if($needupdateexpiration){
				$ExpireTimeInServer = convert_time_to_us($expireTime,$coupon_site['Site']);
				$sql .= "ExpireTime = '$expireTime',ExpireTimeInServer = '$ExpireTimeInServer',";
			}
			if($needupdatestarttime){
				$StartTimeInServer = convert_time_to_us($startTime,$coupon_site['Site']);
				$sql .= "StartTime = '$startTime',StartTimeInServer = '$StartTimeInServer',";
			}
			$sql .= "CategoryID = $categoryID,  IsActive = '$isActive'," .
					"Type = $type, FreeShipping = '$freeShipping', FreeGift='$freeGift', FreeSample='$freeSample', " .
					"ClearanceSales='$clearance', IsExclusive = '$isexclusive', " .
					"SiteWide='$sitewide'," ."Editor='$editorname'" .
					" where ID = $couponId" ;

			$this->objMysql->query($sql);
			return $this->objMysql->getAffectedRows();
		}
		
		function getSiteByCouponId($couponId){
			$row = array();
			$sql = 'SELECT * FROM `coupon_mapping` WHERE ID = '.intval($couponId);
			$row = $this->objMysql->getFirstRow($sql);
			return $row;
		}
		
		function setCouponManual($coupon_id){
			$sql = "UPDATE normalcoupon set ManualUpdate='YES' WHERE ID='".$coupon_id."' LIMIT 1";
			return $this->objMysql->query($sql);
		}
		
		function setCouponManualOther($coupon_id){
			$sql = "UPDATE normalcoupon set ManualUpdateOther='YES' WHERE ID='".$coupon_id."' LIMIT 1";
			return $this->objMysql->query($sql);
		}

		function updateDealById($title, $newprice, $oldprice, $pro_detail, $pro_off, $mid, $remark, $dstUrl, $addTime, $expireTime,
							$affUrl, $imageUrl, $restrict, $categoryID, $startTime, $isActive, $type, 
							$freeShipping, $freeGift, $freeSample, $clearance,$sitewide, $tags, $couponId, $isbundle = 0)
		{
			$couponId = intval($couponId);
			$title = addslashes(trim($title));
			//$code = addslashes(trim($code));
			$newprice = $newprice!=="" ? addslashes(trim($newprice)) : 'NULL';
			$oldprice = $oldprice!=="" ? addslashes(trim($oldprice)) : 'NULL';
			$pro_detail = addslashes(trim($pro_detail));
			$pro_off = addslashes(trim($pro_off));
			$mid = intval($mid);
			$categoryID = intval($categoryID);
			$remark = addslashes(trim($remark));
			$dstUrl = addslashes(trim($dstUrl));
			$addTime = addslashes(trim($addTime));
			$expireTime = addslashes(trim($expireTime));
			$affUrl = addslashes(trim($affUrl));
			$imageUrl = addslashes(trim($imageUrl));
			$restrict = addslashes(trim($restrict));
			$isActive = intval($isActive) == 1 ? 'YES' : 'NO';
			$startTime = addslashes(trim($startTime));
			
			$type = intval($type);
			$freeShipping = intval($freeShipping) == 1 ? 'YES' : 'NO';
			$freeGift = intval($freeGift) == 1 ? 'YES' : 'NO';
			$freeSample = intval($freeSample) == 1 ? 'YES' : 'NO';
			$clearance = intval($clearance) == 1 ? 'YES' : 'NO';
			$sitewide = intval($sitewide) == 1 ? 'YES' : 'NO';
			$tags = addslashes(trim($tags));
			
			//do not update AffUrl and Tag if bundle  
			$isbundle = intval($isbundle);
			//TODO
			if($isbundle == 1){
				$sql = "UPDATE deal set Title = '$title', MerchantID = $mid, " .
						"Description = '$remark', DstUrl = '$dstUrl',  ExpireTime = '$expireTime'," .
						"ImgUrl = '$imageUrl', `Restrict` = '$restrict', " .
						"CategoryID = $categoryID, StartTime = '$startTime', IsActive = '$isActive'," .
						"Type = $type, FreeShipping = '$freeShipping', FreeGift='$freeGift', FreeSample='$freeSample', " .
						"ClearanceSales='$clearance'," .
						"SiteWide='$sitewide'," .
						"NewPrice=$newprice," .
						"OldPrice=$oldprice," .
						"PromotionDetail='$pro_detail'," .
						"PromotionOff='$pro_off'" .
						" where ID = $couponId" ;
			}else{
				$sql = "UPDATE deal set Title = '$title', MerchantID = $mid, " .
						"Description = '$remark', DstUrl = '$dstUrl',  ExpireTime = '$expireTime'," .
						"AffUrl = '$affUrl', ImgUrl = '$imageUrl', `Restrict` = '$restrict', " .
						"CategoryID = $categoryID, StartTime = '$startTime', IsActive = '$isActive'," .
						"Type = $type, FreeShipping = '$freeShipping', FreeGift='$freeGift', FreeSample='$freeSample', " .
						"ClearanceSales='$clearance'," .
						"SiteWide='$sitewide', Tag ='$tags'," .
						"NewPrice=$newprice," .
						"OldPrice=$oldprice," .
						"PromotionDetail='$pro_detail'," .
						"PromotionOff='$pro_off'" .
						" where ID = $couponId" ;
			}
			$this->objMysql->query($sql);
			$this->task_review_coupon($couponId);
		}

		function updateCouponByCondition($couponId,$condition)
		{
			$couponId = intval($couponId);
			$condition = trim($condition);			
			//TODO
			if(!empty($condition) && !empty($couponId)){		
				$sql = "UPDATE normalcoupon SET $condition WHERE ID = $couponId";
				$this->objMysql->query($sql);					
			}
			return $sql;		
		}
		
		function updateClickCntByCouponId($id)
		{
			$id = intval($id);
			$sql = "update normalcoupon set ClickCnt = ClickCnt + 1 where ID = $id";
			$this->objMysql->query($sql);
			return;
		}
		
		function couponCount(&$arrCouponId, $sorting="ORDER BY c.ID desc",$_arrWhere=array())
		{
			$arrWhere = array();
			$arrWhere[] = "c.MerchantID = m.ID";
			$arrWhere[] = "c.ID in (" . implode(',', $arrCouponId) . ")";
			foreach($_arrWhere as $v) $arrWhere[] = $v;
			
			$arr = array();
			if(!is_array($arrCouponId)  || !count($arrCouponId)) return $arr;
		$sql = "select count(*) as count " .
					"from normalcoupon as c, normalmerchant as m Where " . implode(" and ",$arrWhere) . " $sorting";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			$arrTmp = $this->objMysql->getRow($qryId);
			$count = intval($arrTmp['count']);

			$this->objMysql->freeResult($qryId);
			return $count;
		}
		
		function getIDfromCouponBundle($couponId)
		{
			$sql = "SELECT ID FROM normalcoupon_bundle WHERE MappingID = $couponId LIMIT 1";
			return $this->objMysql->getRows($sql);
		}
		
		function getMappingIDfromCouponBundle($couponId)
		{
			$sql = "SELECT MappingID FROM normalcoupon_bundle WHERE ID = $couponId";
			return $this->objMysql->getRows($sql);
		}
		
		function getCouponBundlebyID($ID)
		{
			$sql = "SELECT nm.ID,n.Title,nm.Name,nb.MappingID FROM normalcoupon_bundle AS nb, normalcoupon AS n, normalmerchant AS nm WHERE nb.ID = $ID AND n.ID=nb.MappingID AND nm.ID=n.MerchantID ORDER BY nm.Name";
			return $this->objMysql->getRows($sql);
		}
		
		function delCouponBundleByID($ID){
			$sql = "DELETE FROM normalcoupon_bundle WHERE ID = $ID";
			$this->objMysql->query($sql);
		}
		
		function unsetCouponBundle($ID){
			$sql = "DELETE FROM normalcoupon_bundle WHERE ID = $ID OR MappingID = $ID";
			$this->objMysql->query($sql);
		}
		
		function addCouponBundle($ID,$couponId){
			$sql = "INSERT INTO normalcoupon_bundle (ID,MappingID) VALUES ($ID,$couponId)";
			$this->objMysql->query($sql);
			return $sql;
		}
		
		function updateCouponAddinfoById($couponId,$pro_detail,$promotionOff){
			$couponId = intval($couponId);
			$pro_detail = addslashes(trim($pro_detail));
			$promotionOff = addslashes(trim($promotionOff));
			$sql = "UPDATE normalcoupon_addinfo set PromotionDetail = '$pro_detail', PromotionOff = '$promotionOff' WHERE ID = $couponId";
			$this->objMysql->query($sql);
			return $this->objMysql->getAffectedRows();
		}
		
		function addCouponAddinfoById($couponId,$pro_detail,$promotionOff){
			$couponId = intval($couponId);
			$pro_detail = addslashes(trim($pro_detail));
			$promotionOff = addslashes(trim($promotionOff));
			$sql = "INSERT INTO normalcoupon_addinfo(ID,PromotionDetail,PromotionOff) VALUES($couponId,'$pro_detail','$promotionOff')";
			$this->objMysql->query($sql);			
		}
		
		function replaceCouponAddinfoById($couponId,$pro_detail,$promotionOff,$ExpireDateType, $RemindDateType, $RemindDate, $urlfromaff, $change_date_info = ""){
			$couponId = intval($couponId);
			$pro_detail = addslashes(trim($pro_detail));
			$promotionOff = addslashes(trim($promotionOff));
			$ExpireDateType = addslashes(trim($ExpireDateType));
			$RemindDateType = addslashes(trim($RemindDateType));
			$RemindDate = addslashes(trim($RemindDate));
			
			if($change_date_info != ""){
				$sql = "REPLACE INTO normalcoupon_addinfo(ID,PromotionDetail,PromotionOff,ExpireDateType, RemindDateType, RemindDate, URLFromAff) VALUE($couponId,'$pro_detail','$promotionOff','$ExpireDateType','$RemindDateType','$RemindDate','$urlfromaff')";
			}else{
				$sql = "REPLACE INTO normalcoupon_addinfo(ID,PromotionDetail,PromotionOff,URLFromAff) VALUE($couponId,'$pro_detail','$promotionOff','$urlfromaff')";
			}
//			echo $sql = "REPLACE INTO normalcoupon_addinfo(ID,PromotionDetail,PromotionOff,ExpireDateType, RemindDateType, RemindDate, URLFromAff) VALUE($couponId,'$pro_detail','$promotionOff','$ExpireDateType','$RemindDateType','$RemindDate','$urlfromaff')";;
//			exit;
			$this->objMysql->query($sql);			
		}
		
		function getCouponAddinfoById($couponId){
			$couponId = intval($couponId);
			$sql = "SELECT * FROM normalcoupon_addinfo WHERE ID = $couponId LIMIT 1";
			return $this->objMysql->getRows($sql);
		}
		
		function getCouponListByLimitStrWithMerchantCategory($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $addcondition="",$needFullSearch=false,$strid='')
		{
			$arr = array();
			if ($needFullSearch) {
				if ($strid) {
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) "; 
					
				}else{
					return array();
				}
			}else {
				$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			//edit by zy |add SQL_CALC_FOUND_ROWS 
			$addcondition = $addcondition ? " SQL_CALC_FOUND_ROWS " : "";
			$sql = "select $addcondition n.ID, n.Title, n.Code, n.MerchantID, n.Remark, n.DstUrl, n.CategoryID, " .
					"date_format(n.AddTime, '%Y-%m-%d') as AddTime, n.ExpireTime, n.ExpireTimeInServer, n.AffUrl, " .
					"n.ImgUrl, n.Restrict, n.ClickCnt, n.IsActive, n.StartTime, n.StartTimeInServer, " .
					"n.Type, n.FreeShipping, n.FreeGift, n.FreeSample, n.ClearanceSales," .
					"n.SiteWide, n.Tag, n.Editor, '' as CanRecommendCoupon, n.ManualUpdate," .
					"na.RemindDate,".
					
					"c.Name as categoryName, c.UrlName as categoryUrlName, " .
					"cm.Site, tc.Status,tc.DisplayOrder, " .
					"t.Name as TermName, t.ID as TermId, t.UrlName as termUrlName " .
					
					"from  termcoupon_relationship AS tc  LEFT JOIN 
					  normalcoupon AS n ON n.ID = tc.NormalCouponId LEFT JOIN 
					  normalcoupon_addinfo AS na ON n.ID = na.ID LEFT JOIN 
					  coupon_mapping AS cm ON cm.ID = tc.NormalCouponId LEFT JOIN 
					  term AS t ON t.ID = tc.TermId LEFT JOIN 
					  normalcategory AS c  ON n.CategoryID = c.ID WHERE 1=1 $whereStr $orderStr $limitStr";
// 			echo $sql;
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
					$arr[] = intval($arrTmp['ID']);
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					
					$arr[$i]['merchantname'] = trim($arrTmp['merchantName']);
					$arr[$i]['categoryname'] = trim($arrTmp['categoryName']);
					$arr[$i]['categoryurlname'] = trim($arrTmp['categoryUrlName']);
					$arr[$i]['canrecommendcoupon'] = trim($arrTmp['CanRecommendCoupon']);
					
					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Remark']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['starttime'] = trim($arrTmp['StartTime']);
					$arr[$i]['status'] = trim($arrTmp['Status']);
					$arr[$i]['site'] = trim($arrTmp['Site']);
					$arr[$i]['termname'] = trim($arrTmp['TermName']);
					$arr[$i]['termid'] = trim($arrTmp['TermId']);
					$arr[$i]['termurlname'] = trim($arrTmp['termUrlName']);
					$arr[$i]['isactive'] = trim($arrTmp['IsActive']) == 'YES' ? 1 : 0;
					
					$arr[$i]['type'] = intval($arrTmp['Type']);
					
					$arr[$i]['freeshipping'] = trim($arrTmp['FreeShipping']) == 'YES' ? 1 : 0;
					$arr[$i]['freegift'] = trim($arrTmp['FreeGift']) == 'YES' ? 1 : 0;
					$arr[$i]['freesample'] = trim($arrTmp['FreeSample']) == 'YES' ? 1 : 0;
					$arr[$i]['clearance'] = trim($arrTmp['ClearanceSales']) == 'YES' ? 1 : 0;
					$arr[$i]['sitewide'] = trim($arrTmp['SiteWide']) == 'YES' ? 1 : 0;
					$arr[$i]['tag'] = trim($arrTmp['Tag']);
					$arr[$i]['editor'] = trim($arrTmp['Editor']);
					$arr[$i]['manualupdate'] = trim($arrTmp['ManualUpdate']);
					$arr[$i]['reminddate'] = trim($arrTmp['RemindDate']);
					$arr[$i]['displayorder'] = trim($arrTmp['DisplayOrder']);
	
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getCouponCountWithMerchantCategory($whereStr="",$needFullSearch=false,$td_kw='')
		{
			$total = 0;
			$strid ='';
			if ($needFullSearch) {
				$sql  = "SELECT CouponID AS id FROM `normalsearchindex` WHERE MATCH (`Content`) AGAINST ('".addslashes($td_kw)."')";
				$couponids = $this->objMysql->getRows($sql);
				if (!count($couponids)) {
					return array('strcouponid' => $strid,'totalcnt' => 0);
				}else {
					foreach ($couponids as &$v) {
						$v = $v['id'];
					}
					$strid = implode(',', $couponids);
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) "; 
				}
			}else {
				$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			//Create temporary table for left join usage
//			$sql = "CREATE TEMPORARY TABLE ns (
//						cid INT, 
//						avgiswork FLOAT, 
//						isworkcount INT,
//						KEY cid(cid),
//						KEY avgiswork(avgiswork),
//						KEY isworkcount(isworkcount)
//						)ENGINE=MYISAM DEFAULT CHARSET=latin1;";
//			$this->objMysql->query($sql);
//			$sql = "INSERT INTO ns
//						SELECT couponid cid, MAX(IF(statsname = 'avgiswork', StatsValue, 0)) avgiswork, MAX(IF(statsname = 'isworkcount', StatsValue, 0)) isworkcount
//												FROM normalcoupon_stats
//												GROUP BY cid";
//			$this->objMysql->query($sql);
			
			$sql = "select count(*) as cnt from normalcoupon as n 
					LEFT JOIN `termcoupon_relationship` AS tc 
					  ON n.`ID`=tc.`NormalCouponId` 
					LEFT JOIN coupon_mapping AS cm 
			          ON cm.ID = tc.NormalCouponId 
			        LEFT JOIN term AS t 
			          ON t.ID = tc.TermId 
					WHERE 1=1 $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return array('strcouponid' => $strid,'totalcnt' => $total);
		}
		
		function getManualPromotionListByLimitStrWithMerchantCategory($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $addcondition="",$needFullSearch=false,$strid='')
		{
			$arr = array();
			if ($needFullSearch) {
				if ($strid) {
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) "; 
					
				}else{
					return array();
				}
			}else {
				$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			//edit by zy |add SQL_CALC_FOUND_ROWS 
			$addcondition = $addcondition ? " SQL_CALC_FOUND_ROWS " : "";
			$sql = "select $addcondition n.ID, n.Title, n.Code,n.Description, n.CategoryID, " .
					"date_format(n.AddTime, '%Y-%m-%d') as AddTime, n.ExpireTime, n.Site," .
					"n.IsActive, n.StartTime,n.Type,n.Editor, '' as CanRecommendCoupon, " .
					"tc.DisplayOrder," . 
					"c.Name as categoryName, c.UrlName as categoryUrlName, " .
					"t.Name as TermName, t.ID as TermId, t.UrlName as termUrlName " .
					"from  `manual_promotion` AS n LEFT JOIN 
					  `termmanualpromotion_relationship` AS tc ON n.ID = tc.ManualPromotionId LEFT JOIN 
					  term AS t ON t.ID = tc.TermId LEFT JOIN 
					  normalcategory AS c  ON n.CategoryID = c.ID 
					  WHERE 1=1 $whereStr $orderStr $limitStr";
//			echo  $sql;
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($returnIdOnly)
					$arr[] = intval($arrTmp['ID']);
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['code'] = trim($arrTmp['Code']);
					$arr[$i]['title'] = trim($arrTmp['Title']);
					$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
					$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
					
//					$arr[$i]['merchantname'] = trim($arrTmp['merchantName']);
					$arr[$i]['categoryname'] = trim($arrTmp['categoryName']);
					$arr[$i]['categoryurlname'] = trim($arrTmp['categoryUrlName']);
					$arr[$i]['canrecommendcoupon'] = trim($arrTmp['CanRecommendCoupon']);
					
//					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
					$arr[$i]['remark'] = trim($arrTmp['Description']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
//					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
//					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
//					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
					$arr[$i]['starttime'] = trim($arrTmp['StartTime']);
//					$arr[$i]['status'] = trim($arrTmp['Status']);
					$arr[$i]['site'] = trim($arrTmp['Site']);
					$arr[$i]['termname'] = trim($arrTmp['TermName']);
					$arr[$i]['termid'] = trim($arrTmp['TermId']);
					$arr[$i]['termurlname'] = trim($arrTmp['termUrlName']);
					$arr[$i]['isactive'] = trim($arrTmp['IsActive']) == 'YES' ? 1 : 0;
					
					$arr[$i]['type'] = intval($arrTmp['Type']);
					
//					$arr[$i]['freeshipping'] = trim($arrTmp['FreeShipping']) == 'YES' ? 1 : 0;
//					$arr[$i]['freegift'] = trim($arrTmp['FreeGift']) == 'YES' ? 1 : 0;
//					$arr[$i]['freesample'] = trim($arrTmp['FreeSample']) == 'YES' ? 1 : 0;
//					$arr[$i]['clearance'] = trim($arrTmp['ClearanceSales']) == 'YES' ? 1 : 0;
//					$arr[$i]['sitewide'] = trim($arrTmp['SiteWide']) == 'YES' ? 1 : 0;
					$arr[$i]['displayorder'] = trim($arrTmp['DisplayOrder']);
					$arr[$i]['editor'] = trim($arrTmp['Editor']);
					$arr[$i]['avgiswork'] = trim($arrTmp['AvgIsWork']);
					$arr[$i]['isworkcount'] = trim($arrTmp['IsWorkCount']);
					$i++;
				}
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getManualPromotionCountWithCategory($whereStr="",$needFullSearch=false,$td_kw='')
		{
			$total = 0;
			$strid ='';
			if ($needFullSearch) {
				$sql  = "SELECT CouponID AS id FROM `normalsearchindex_manual` WHERE MATCH (`Content`) AGAINST ('".addslashes($td_kw)."')";
				$couponids = $this->objMysql->getRows($sql);
				if (!count($couponids)) {
					return array('strcouponid' => $strid,'totalcnt' => 0);
				}else {
					foreach ($couponids as &$v) {
						$v = $v['id'];
					}
					$strid = implode(',', $couponids);
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) "; 
				}
			}else {
				$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			$sql = "select count(*) as cnt from `manual_promotion` as n 
					LEFT JOIN `termmanualpromotion_relationship` AS tc 
					  ON n.`ID`=tc.`ManualPromotionId` 
			        LEFT JOIN term AS t 
			        	ON t.ID = tc.TermId 
			        LEFT JOIN normalcategory AS c 
			        	ON n.CategoryID = c.ID
		        	
					WHERE 1=1 $whereStr";
//			echo $sql;
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return array('strcouponid' => $strid,'totalcnt' => $total);
		}
		
		// twitter
		function getCouponTwitterByCouponId($couponid)
		{
			$couponid = intval($couponid);
			$sql = "SELECT * FROM normalcoupon_twitter WHERE CouponId=$couponid";
			return $this->objMysql->getRows($sql);
		}
		
		function addCouponTwitter($couponid,$twitterid,$twittercontent,$status)
		{
			$couponid = intval($couponid);
			$twitterid = intval($twitterid);
			$twittercontent = addslashes(trim($twittercontent));
			$status = intval($status);
			global $PHP_AUTH_USER;
			$editorname = $PHP_AUTH_USER ? $PHP_AUTH_USER : $_SERVER["REMOTE_USER"];
			$sql = "INSERT INTO normalcoupon_twitter(CouponId,TwitterId,TwitterContent,Operator,LastUpdateTime,AddTime,Status) VALUES($couponid,$twitterid,'$twittercontent','$editorname','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."',$status)";
			$qryId = $this->objMysql->query($sql);
			return $this->objMysql->getLastInsertId($qryId);
		}
		
		function updateCouponTwitterCron($couponid,$twitterid,$status)
		{
			$couponid = intval($couponid);
			$twitterid = intval($twitterid);			
			$status = intval($status);			
			$sql = "UPDATE normalcoupon_twitter SET TwitterId=$twitterid, LastUpdateTime='".date('Y-m-d H:i:s')."', Status=$status WHERE CouponId=$couponid";
			$this->objMysql->query($sql);
		}
		
		function getCouponTwitterCondition($condition)	
		{			
			$condition = trim($condition);
			if(!empty($condition)){				
				$sql = "SELECT * FROM normalcoupon_twitter WHERE $condition";
				return $this->objMysql->getRows($sql);
			}			
		}
		
		function getCouponTwitterCron(){
			$sql = "SELECT t.*, c.StartTime FROM normalcoupon_twitter AS t left Join normalcoupon AS c ON (t.CouponId = c.ID) WHERE t.Status = 2 AND t.TwitterId <1 AND c.StartTime <= '".date('Y-m-d H:i:s')."'";
			return $this->objMysql->getRows($sql);			
		}
		
		function getTwitterTplById($templateid)
		{
			$templateid = intval($templateid);
			$sql = "SELECT * FROM normalcoupon_twitter_template WHERE TwitterTemplateId=$templateid";			
			return $this->objMysql->getRows($sql);
		}
		
		function getTwitterTpl()
		{
			$sql = "SELECT * FROM normalcoupon_twitter_template ORDER BY TwitterTemplateName";			
			return $this->objMysql->getRows($sql);			
		}
		
		function addTwitterTpl($TwitterTemplateName,$TwitterTemplateContent)
		{
			$TwitterTemplateName = addslashes(trim($TwitterTemplateName));
			$TwitterTemplateContent = addslashes(trim($TwitterTemplateContent));
			$sql = "INSERT INTO normalcoupon_twitter_template(TwitterTemplateName,TwitterTemplateContent,LastUpdateTime) VALUES('$TwitterTemplateName','$TwitterTemplateContent','".date('Y-m-d H:i:s')."')";
			$qryId = $this->objMysql->query($sql);
			return $this->objMysql->getLastInsertId($qryId);
		}
		
		function editTwitterTpl($TwitterTemplateId,$TwitterTemplateName,$TwitterTemplateContent)
		{
			$TwitterTemplateId = intval($TwitterTemplateId);
			$TwitterTemplateName = addslashes(trim($TwitterTemplateName));
			$TwitterTemplateContent = addslashes(trim($TwitterTemplateContent));
			$sql = "UPDATE normalcoupon_twitter_template SET TwitterTemplateName='$TwitterTemplateName', TwitterTemplateContent='$TwitterTemplateContent', LastUpdateTime='".date('Y-m-d H:i:s')."' WHERE TwitterTemplateId=$TwitterTemplateId";
			$this->objMysql->query($sql);
		}
		
		function delTwitter($condition)
		{
			$condition = trim($condition);
			$sql = "DELETE FROM normalcoupon_twitter WHERE $condition";
			$this->objMysql->query($sql);
		}
		
		function getRecommendInfo($objMysql, $couponid){
			$sql = "select * from coupon_recommend where CouponID = '$couponid'";
			$queryId = $objMysql->query($sql);
			$arr = mysql_fetch_array($queryId);
			return $arr;
		}

	   	public function getCouponTermRelationshipByCoupon($couponid) {
   			$sql = "SELECT tc.*,t.Name FROM `termcoupon_relationship` as tc, term as t WHERE t.ID=tc.TermId AND tc.NormalCouponId = $couponid";
   			$row = $this->objMysql->getRows($sql);
   			return $row;
   		}
   		
   		public function getActiveCouponVoteRecords($where) {
   			$sql = "SELECT 
					   n.`ID` ,polls_status VoteStatus
					  FROM
					    `polls_iswork` p,
					    coupon n 
					  WHERE p.polls_style_id = n.id 
					    AND ".$this->getActiveCouponCnd('n')." $where "." 
					  ORDER BY polls_style_id,
					    poll_timestamp DESC ";
   			$row = $this->objMysql->getRows($sql);
   			return $row;
   		}
   		
   		public function getLastestCouponVote($lastest_num,$couponids) {
   			$where = "";
   			if ($couponids) {
   				$where = "AND n.id IN (" .implode(',', $couponids).") ";
   			}
   			$voteRecords = $this->getActiveCouponVoteRecords($where);
   			$res = array();
   			foreach ( $voteRecords as $k => $v ) {
   				if (!isset( $res [$v ['ID']] )) {
   					 $res [$v ['ID']] = array();
   				}
				if (count ( $res [$v ['ID']] ) < $lastest_num) {
					$res [$v ['ID']] [] = $v ['VoteStatus'];
				} else {
					continue;
				}
			}
			return $res;
		}
		
		public function getVoteAlertCouponIDs($lastest_vote_num,$couponids = array()) {
			$voteRecords = $this->getLastestCouponVote($lastest_vote_num,$couponids);
			$couponIds = $this->getAlertCouponIds($voteRecords, $lastest_vote_num);
			return $couponIds;
		}
		
		public function getAlertCouponIds($voteRecords,$lastest_vote_num) {
			$alertCoupons = array();
			foreach ($voteRecords as $k => $v) {
				if (count($v) == $lastest_vote_num) {
					$isAlert  =  true;
					foreach ($v as $status) {
						if ($status == '1'){
							$isAlert = false;
							break;
						}
					};
					if ($isAlert) {
						$alertCoupons [] = $k;
					}
				};
			};
			return $alertCoupons;
			
		}
		
		public function checkCouponById($couponId,$term_id=''){
			
			//检查同一个term下的coupon�?是否有相同的code�?
			//如果存在检查是该coupon是否生效�?
			//如果生效则检查该coupon的开始时间及结束时间是否有交�?
			//如果有交集。保存失�?
			if(empty($couponId))return true;
			
			$row_coupon = $this->_instance($couponId);
			$couponCheckData = array();
			$couponCheckData['couponid'] = $couponId;
			$couponCheckData['code'] = $row_coupon['Code'];
			$couponCheckData['eDate'] = $row_coupon['ExpireTime'];
			$couponCheckData['couponStartDate'] = $row_coupon['StartTime'];
			$couponCheckData['term_id'] = $term_id;
			
			return $this->checkCouponByCode($couponCheckData);
		}
		
		
		public function checkCouponByCode($couponCheckData){
			if(!isset($couponCheckData['couponid']) || empty($couponCheckData['couponid']))return false;
			if(!isset($couponCheckData['couponStartDate']) || empty($couponCheckData['couponStartDate']))return false;
			if(!isset($couponCheckData['eDate']) || empty($couponCheckData['eDate']))return false;
			//没有code直接通过
			if(!isset($couponCheckData['code']) || empty($couponCheckData['code']))return true;
			
			//如果有code
			
			//获取于此coupon有关联的所有term
			$arrRelationship = $this->getCouponTermRelationshipByCoupon($couponCheckData['couponid']);
			
			$termId = array();
			foreach($arrRelationship as $k=>$v){
				if($v['Status'] == 'Online')$termId[] = $v['TermId'];
			}
			if($couponCheckData['term_id']){
				$termId[] = $couponCheckData['term_id'];
			}
			
			if(empty($termId))return true;
			//获取term下的promotion
			$row_promotion = array();
			
			$where_arr = array();
			$where_arr[] = 'mp.`Code` = "'.addslashes($couponCheckData['code']).'"';
			$where_arr[] = 'mp.`IsActive` = "YES"';
			$where_arr[] = 'tr.`TermId` IN ('.join(',',$termId).') ';
			$where_str = ' WHERE '.join(' AND ',$where_arr);
			
			$sql = 'SELECT 
					  tr.`TermId`,mp.`Code`,mp.`IsActive`,mp.`ExpireTime`,mp.`StartTime`,mp.`ID`
					FROM
					  `termmanualpromotion_relationship` AS tr 
					  LEFT JOIN manual_promotion AS mp 
					    ON tr.ManualPromotionId = mp.ID '.$where_str;
			
			$row_promotion = $this->objMysql->getRows($sql);

//只与promotion做对比。不与coupon做对比。因为coupon来自于多个站点允许重�?
// 			//获取term下的 coupon
// 			$row_coupon = array();
			
// 			$where_arr = array();
// 			$where_arr[] = 'c.`Code` = "'.addslashes($couponCheckData['code']).'"';
// 			$where_arr[] = 'tr.`status` = "Online"';
// 			$where_arr[] = 'c.`IsActive` = "YES"';
// 			$where_arr[] = 'tr.`TermId` IN ('.join(',',$termId).') ';
// 			$where_arr[] = 'c.ID != '.intval($couponCheckData['couponid']);
// 			$where_str = ' WHERE '.join(' AND ',$where_arr);
			
// 			$sql = 'SELECT 
// 					  tr.`TermId`,tr.status,c.Code,c.IsActive,c.ExpireTime,c.StartTime,c.ID 
// 					FROM 
// 					  `termcoupon_relationship` AS tr 
// 				      LEFT JOIN normalcoupon AS c 
// 					    ON tr.normalCouponid = c.id'.$where_str;
			
// 			$row_coupon = $this->objMysql->getRows($sql);
			
// 			//合并row
// 			$row = array_merge($row_promotion,$row_coupon);
			
			$row = array();
			$row = $row_promotion;
			
			if(empty($row))return true;
			
			$promotionStartInt = strtotime($couponCheckData['couponStartDate']);
			$promotionExpireInt = strtotime($couponCheckData['eDate']);
			
			if($couponCheckData['couponStartDate'] == '0000-00-00 00:00:00' && $couponCheckData['eDate'] == '0000-00-00 00:00:00')return false;
			
			$flag = true;
			foreach($row as $k=>$v){
				$thisStartInt = strtotime($v['StartTime']);
				$thisExpireInt = strtotime($v['ExpireTime']);
				
				if($v['StartTime'] == '0000-00-00 00:00:00' && $v['ExpireTime'] == '0000-00-00 00:00:00'){
					$flag = false;
					break;
				}elseif($v['StartTime'] == '0000-00-00 00:00:00' && $v['ExpireTime'] != '0000-00-00 00:00:00'){
					if($couponCheckData['couponStartDate'] == '0000-00-00 00:00:00' || $promotionStartInt < $thisExpireInt){
						$flag = false;
						break;
					}
				}elseif($v['StartTime'] != '0000-00-00 00:00:00' && $v['ExpireTime'] == '0000-00-00 00:00:00'){
					if($couponCheckData['eDate'] == '0000-00-00 00:00:00' || $promotionExpireInt > $thisStartInt){
						$flag = false;
						break;
					}
				}else{
					if($couponCheckData['couponStartDate'] == '0000-00-00 00:00:00' && $promotionExpireInt > $thisStartInt){
						$flag = false;
						break;
					}elseif($couponCheckData['eDate'] == '0000-00-00 00:00:00' && $promotionStartInt < $thisExpireInt){
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
		
		function getFilterCouponListCountWithCategory($whereStr="",$needFullSearch=false,$td_kw='')
		{
			$total = 0;
			$strid ='';
			if ($needFullSearch) {
				$sql  = "SELECT CouponID AS id FROM `normalsearchindex_manual` WHERE MATCH (`Content`) AGAINST ('".addslashes($td_kw)."')";
				$couponids = $this->objMysql->getRows($sql);
				if (!count($couponids)) {
					return array('strcouponid' => $strid,'totalcnt' => 0);
				}else {
					foreach ($couponids as &$v) {
						$v = $v['id'];
					}
					$strid = implode(',', $couponids);
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) ";
				}
			}else {
			$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			$sql = "select count(*) as cnt from `user_submit_promotion` as n
			LEFT JOIN term AS t
			ON t.ID = n.TermId
			LEFT JOIN normalcategory AS c
			ON n.CategoryID = c.ID
			 
			WHERE 1=1 $whereStr";
			//			echo $sql;
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return array('strcouponid' => $strid,'totalcnt' => $total);
		}
		
		function getFilterCouponListByLimitStrWithMerchantCategory($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $addcondition="",$needFullSearch=false,$strid='')
		{
			$arr = array();
			if ($needFullSearch) {
				if ($strid) {
					$whereStr = $whereStr ? " AND $whereStr " : "";
					$whereStr .= " AND n.id IN ($strid) ";
						
				}else{
				return array();
				}
			}else {
			$whereStr = $whereStr ? " AND $whereStr " : "";
			}
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			//edit by zy |add SQL_CALC_FOUND_ROWS
			$addcondition = $addcondition ? " SQL_CALC_FOUND_ROWS " : "";
			$sql = "select $addcondition n.ID, n.Title, n.Code,n.Description, n.CategoryID, " .
					"date_format(n.AddTime, '%Y-%m-%d') as AddTime, n.ExpireTime, n.Site," .
					"n.IsActive, n.StartTime,n.Type,n.Editor, '' as CanRecommendCoupon, " .
					"c.Name as categoryName, c.UrlName as categoryUrlName,n.status as `status`,n.promotionID as promotionID ," .
					"t.Name as TermName, t.ID as TermId, t.UrlName as termUrlName " .
					"from  `user_submit_promotion` AS n LEFT JOIN
							term AS t ON t.ID = n.TermId LEFT JOIN
							normalcategory AS c  ON n.CategoryID = c.ID
							WHERE 1=1 $whereStr $orderStr $limitStr";
							//			echo  $sql;
							$qryId = $this->objMysql->query($sql);
							$i = 0;
							while($arrTmp = $this->objMysql->getRow($qryId))
							{
							if($returnIdOnly)
								$arr[] = intval($arrTmp['ID']);
								else
								{
			$arr[$i]['id'] = intval($arrTmp['ID']);
			$arr[$i]['code'] = trim($arrTmp['Code']);
			$arr[$i]['title'] = trim($arrTmp['Title']);
			$arr[$i]['merchantid'] = intval($arrTmp['MerchantID']);
			$arr[$i]['categoryid'] = intval($arrTmp['CategoryID']);
			$arr[$i]['status'] = trim($arrTmp['status']);
			$arr[$i]['promotionID'] = intval($arrTmp['promotionID']);
				
			//					$arr[$i]['merchantname'] = trim($arrTmp['merchantName']);
			$arr[$i]['categoryname'] = trim($arrTmp['categoryName']);
					$arr[$i]['categoryurlname'] = trim($arrTmp['categoryUrlName']);
							$arr[$i]['canrecommendcoupon'] = trim($arrTmp['CanRecommendCoupon']);
								
							//					$arr[$i]['clickcnt'] = intval($arrTmp['ClickCnt']);
			$arr[$i]['remark'] = trim($arrTmp['Description']);
					$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['expiration'] = trim($arrTmp['ExpireTime']);
					//					$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
			//					$arr[$i]['imgurl'] = trim($arrTmp['ImgUrl']);
			//					$arr[$i]['restrict'] = trim($arrTmp['Restrict']);
			$arr[$i]['starttime'] = trim($arrTmp['StartTime']);
			//					$arr[$i]['status'] = trim($arrTmp['Status']);
			$arr[$i]['site'] = trim($arrTmp['Site']);
			$arr[$i]['termname'] = trim($arrTmp['TermName']);
			$arr[$i]['termid'] = trim($arrTmp['TermId']);
			$arr[$i]['termurlname'] = trim($arrTmp['termUrlName']);
			$arr[$i]['isactive'] = trim($arrTmp['IsActive']) == 'YES' ? 1 : 0;
			
					$arr[$i]['type'] = intval($arrTmp['Type']);
						
					//					$arr[$i]['freeshipping'] = trim($arrTmp['FreeShipping']) == 'YES' ? 1 : 0;
					//					$arr[$i]['freegift'] = trim($arrTmp['FreeGift']) == 'YES' ? 1 : 0;
					//					$arr[$i]['freesample'] = trim($arrTmp['FreeSample']) == 'YES' ? 1 : 0;
					//					$arr[$i]['clearance'] = trim($arrTmp['ClearanceSales']) == 'YES' ? 1 : 0;
					//					$arr[$i]['sitewide'] = trim($arrTmp['SiteWide']) == 'YES' ? 1 : 0;
					$arr[$i]['displayorder'] = trim($arrTmp['DisplayOrder']);
					$arr[$i]['editor'] = trim($arrTmp['Editor']);
					$arr[$i]['avgiswork'] = trim($arrTmp['AvgIsWork']);
					$arr[$i]['isworkcount'] = trim($arrTmp['IsWorkCount']);
					$i++;
				}
			}
					$this->objMysql->freeResult($qryId);
					return $arr;
		}
	}
}
?>