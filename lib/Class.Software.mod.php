<?php
/*
 * FileName: Class.Software.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_NORMAL_SOFTWARE__"))
{
   define("__MOD_CLASS_NORMAL_SOFTWARE__",1);
   
   class Software
   {
   		var $objMysql;
   		
   		function Software($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getSoftwareCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from software_product $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}

		function getSoftwareListByLimitStr($limitStr="", $whereStr="", $orderStr="")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Affiliate, ExtMID, ExtMerName, ExtPID, ProdName, ShortDesc, LongDesc, ExtKeywords, Currency, MSRP, Price, PriceAfterDiscount,";
   			$sql .= "DiscountType, DiscountValue, Image, OSPlatform, CorpLogo, AffURL, TrialURL, CartURL, ExtCategory, Key1, Key2, Key3, Editor, Status, AddTime ";
   			$sql .= " from software_product $whereStr $orderStr $limitStr";
   			//echo $sql."<br>\n";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['ID'] = intval($arrTmp['ID']);
   				$arr[$i]['Affiliate'] = trim($arrTmp['Affiliate']);
   				$arr[$i]['ExtMID'] = trim($arrTmp['ExtMID']);
   				$arr[$i]['ExtMerName'] = trim($arrTmp['ExtMerName']);
   				$arr[$i]['ExtPID'] = trim($arrTmp['ExtPID']);
   				$arr[$i]['ProdName'] = trim($arrTmp['ProdName']);
   				$arr[$i]['ShortDesc'] = trim($arrTmp['ShortDesc']);
   				$arr[$i]['LongDesc'] = trim($arrTmp['LongDesc']);
   				$arr[$i]['ExtKeywords'] = trim($arrTmp['ExtKeywords']);
   				$arr[$i]['Currency'] = trim($arrTmp['Currency']);
   				$arr[$i]['MSRP'] = floatval($arrTmp['MSRP']);
   				$arr[$i]['Price'] = floatval($arrTmp['Price']);
   				$arr[$i]['PriceAfterDiscount'] = floatval($arrTmp['PriceAfterDiscount']);
   				$arr[$i]['DiscountType'] = trim($arrTmp['DiscountType']);
   				$arr[$i]['DiscountValue'] = floatval($arrTmp['DiscountValue']);
   				$arr[$i]['Image'] = trim($arrTmp['Image']);
   				$arr[$i]['OSPlatform'] = trim($arrTmp['OSPlatform']);
   				$arr[$i]['CorpLogo'] = trim($arrTmp['CorpLogo']);
   				$arr[$i]['AffURL'] = trim($arrTmp['AffURL']);
   				$arr[$i]['TrialURL'] = trim($arrTmp['TrialURL']);
   				$arr[$i]['CartURL'] = trim($arrTmp['CartURL']);
   				$arr[$i]['ExtCategory'] = trim($arrTmp['ExtCategory']);
   				$arr[$i]['Key1'] = trim($arrTmp['Key1']);
   				$arr[$i]['Key2'] = trim($arrTmp['Key2']);
   				$arr[$i]['Key3'] = trim($arrTmp['Key3']);
   				$arr[$i]['Editor'] = trim($arrTmp['Editor']);
   				$arr[$i]['Status'] = trim($arrTmp['Status']);
   				$arr[$i]['AddTime'] = trim($arrTmp['AddTime']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		function addSoftware()
		{
			//TODO
		}
		
		function updateSoftwareById()
		{
			//TODO
		}

			
		function getSoftwareById($pid)
		{
			$pid = intval($pid);
   			$arr = $this->getSoftwareListByLimitStr("", " ID = $pid");
			return $arr[0];
		}

		function getOtherSoftwareUnderSameMer($aff, $mer, $exceptPid)
		{
			$whereStr = "Affiliate = '".addslashes(trim($aff))."' AND ExtMID = '".addslashes(trim($mer))."' AND ID <> ".intval($exceptPid);
			$orderStr = " ID DESC";
			$limitStr = "LIMIT 0, 8";
			$arr = $this->getSoftwareListByLimitStr($limitStr, $whereStr, $orderStr);
			return $arr;
		}
		
		function getRelateSoftware($pid, $arrExceptID)
		{
			$pid = intval($pid);
			$arr = $this->getSoftwareById($pid);
			
			$whereStr = "";
			//check tags
			if(isset($arr['ExtKeywords'])  && trim($arr['ExtKeywords']) )
			{
				$arrKw = explode(",", trim($arr['ExtKeywords']));
				foreach($arrKw as $kw)
				{
					if(!trim($kw)) continue;
					$kw = addslashes(trim($kw));
					
					$orStr = "";
					if($whereStr) $orStr = " OR ";
					$whereStr .= $orStr. "ExtKeywords like '{$kw},%' OR ExtKeywords like '%,{$kw},%' OR ExtKeywords like '%,{$kw}'";
				}
			}
			
			//check categories
			if(isset($arr['ExtCategory']) && trim($arr['ExtCategory']))
			{
				$orStr = "";
				if($whereStr) $orStr = " OR ";
				$whereStr .= $orStr."ExtCategory = '".addslashes(trim($arr['ExtCategory']))."'";
			}
			
			if(!$whereStr)
				return array();
			else
			{	if(count($arrExceptID))
					$whereStr = "( $whereStr ) AND ID not in (".implode(",", $arrExceptID).")";
				$whereStr .= " AND ID <> $pid";
				$orderStr = " ID DESC";
				$limitStr = "LIMIT 0, 8";
				$arr = $this->getSoftwareListByLimitStr($limitStr, $whereStr, $orderStr);
				return $arr;
			}
		}
		
		function getMerCoupon($aff, $mer)
		{
			$mer = addslashes(trim($mer));
			$aff = addslashes(trim($aff));				
			$sql = "select MerID from wf_mer_in_aff where MerIDinAff = '$mer' AND AffID = '$aff' and IsUsing = 1";
			$qryId = $this->objMysql->query($sql);
			$arrMer = array();
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$merid = intval($arrTmp['MerID']);
				$arrMer[] = $merid;
			}
			$this->objMysql->freeResult($qryId);
			
			if(count($arrMer))
			{
				$objCoupon = new NormalCoupon($this->objMysql);
				$whereStr = " MerchantID in (".implode(',', $arrMer).") AND ".$objCoupon->getActiveCouponCnd();
				$arrCoupon = $objCoupon->getCouponListByLimitStr("", $whereStr, ' Code DESC, AddTime DESC');
				return $arrCoupon;
			}
			return array();
		}
		
		function getCouponMerInfo($aff, $mer)
		{
			$mer = addslashes(trim($mer));
			$aff = addslashes(trim($aff));
			$sql = "select MerID from wf_mer_in_aff where MerIDinAff = '$mer' AND AffID = '$aff' and IsUsing = 1"; 
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId); //just get the first one!
			$this->objMysql->freeResult($qryId);
			if(isset($arrTmp['MerID']) && $arrTmp['MerID'])
			{
				$merid = intval($arrTmp['MerID']);
				$objMerchant = new NormalMerchant($this->objMysql);
				$arrMerchant = $objMerchant->getMerchantById($merid);
				return $arrMerchant;
			}
			return array();
		}
	}
}
?>
