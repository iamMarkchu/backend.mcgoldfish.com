<?php
/*
 * FileName: Class.NormalMerchant.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_NORMAL_MERCHANT__"))
{
   define("__MOD_CLASS_NORMAL_MERCHANT__",1);
   
   class NormalMerchant
   {
   		var $objMysql;
   		
   		function NormalMerchant($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getMerchantCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from normalmerchant $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}

		function getMerchantApprovalCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from normalmerchant_approval $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}

		function getMerBaseInfoActiveOnly($limitStr="",$whereStr="",$orderStr="")
		{
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select distinct id,name,logo from normalmerchant as m, wf_mer_in_aff as f where m.ID = f.MerID and f.IsUsing = 1 $whereStr $orderStr $limitStr";
			return $this->objMysql->getRows($sql);
		}
	
		function getMerBaseInfoByLimitStr($limitStr="",$whereStr="",$orderStr="",$fields="")//id,name,logo
		{
			if($fields == "") $fields = "id,name";
			if($fields == "") $fields = "id,name";
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select $fields from normalmerchant $whereStr $orderStr $limitStr";
			return $this->objMysql->getRows($sql);
		}

		function getMerApprovalnfoByLimitStr($limitStr="", $whereStr="", $orderStr="")//id, name, logo
		{
			
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, Name, Logo from normalmerchant_approval $whereStr $orderStr $limitStr";

			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arr[$i]['id'] = intval($arrTmp['ID']);
				$arr[$i]['name'] = trim($arrTmp['Name']);
				$arr[$i]['logo'] = trim($arrTmp['Logo']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getAllMerchants()
		{
			$arrMer = array();
			$sql = "select ID, Name from normalmerchant";
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrMer[intval($arrTmp['ID'])] = trim($arrTmp['Name']);
			}
			$this->objMysql->freeResult($qryId);
			return $arrMer;
		}
		
   function getMerchantByName($name)
		{
			$arrMer = array();
			$sql = "select ID, Name from normalmerchant WHERE Name='".addslashes($name)."' LIMIT 1";
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrMer[intval($arrTmp['ID'])] = trim($arrTmp['Name']);
				$return_id=intval($arrTmp['ID']);
			}
			$this->objMysql->freeResult($qryId);
			return $return_id;
		}
		
		function getMerchantTipsByMerchantId($id)
		{
			$arrMer = array();
			$sql = "select ID,EditorTips from normalmerchant_addinfo WHERE ID='$id' LIMIT 1";
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrMer[intval($arrTmp['ID'])] = trim($arrTmp['EditorTips']);
				$return_tips=trim($arrTmp['EditorTips']);		
			}
			$this->objMysql->freeResult($qryId);
			return $return_tips;
		}
		
		function getBundleNameList()
		{
			$sql="SELECT BundleName, count(BundleName) as num FROM normalmerchant_bundle GROUP BY BundleName";
			$qryId = $this->objMysql->query($sql);
			$i=0;
			while($arrTmp = $this->objMysql->getRow($qryId))
   			{   				
   				$arr[$i]['bundlename'] = trim($arrTmp['BundleName']);
				$arr[$i]['num'] = trim($arrTmp['num']); 
   				$i++;
   			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function delMerchantBundle($merchantid)
		{
			$sql="DELETE FROM normalmerchant_bundle WHERE MerchantID='$merchantid'";
			$this->objMysql->query($sql);
		}
		
		function replaceMerchantBundle($merchantid,$bundlename)
		{
			$sql="REPLACE INTO normalmerchant_bundle(MerchantID,BundleName) VALUES('$merchantid','$bundlename')";
			$this->objMysql->query($sql);
		}
		
		function getBundleByMerchantId($merchant_id)
		{
			$sql="SELECT BundleName FROM normalmerchant_bundle WHERE MerchantID='$merchant_id' LIMIT 1";
			$arr = $this->objMysql->getRows($sql);
			$bundleName=$arr[0]["BundleName"];
			return $bundleName;
		}
		
		function getMerchantBundleByBundleName($bundleName)
		{				
			$sql="SELECT ID,m.Name FROM normalmerchant AS m, normalmerchant_bundle AS mb WHERE mb.MerchantID=m.ID AND mb.BundleName='$bundleName' ORDER BY m.Name";
			$qryId = $this->objMysql->query($sql);
			$i=0;
			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);   				
   				$i++;
   			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getMerchantBundleByMerchantName($name)
		{
			$sql="SELECT BundleName FROM normalmerchant AS m, normalmerchant_bundle AS mb WHERE mb.MerchantID=m.ID AND m.Name='".addslashes($name)."' LIMIT 1";
			$arr = $this->objMysql->getRows($sql);
			$bundleName=$arr[0]["BundleName"];
			$sql="SELECT ID,m.Name FROM normalmerchant AS m, normalmerchant_bundle AS mb WHERE mb.MerchantID=m.ID AND mb.BundleName='".addslashes($bundleName)."' ORDER BY m.Name";
			$qryId = $this->objMysql->query($sql);
			$i=0;
			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);   				
   				$i++;
   			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getMerchantListQuickByLimitStr($limitStr="", $whereStr="", $orderStr="")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Name, LinkSource " .
   					"from normalmerchant $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$arr[$i]['LinkSource'] = trim($arrTmp['LinkSource']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}

		function getMerchantListByLimitStr($limitStr="", $whereStr="", $orderStr="", $id = "")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select SQL_CALC_FOUND_ROWS ID, Name, c_Desc, Logo, Date_format(AddTime,'%Y-%m-%d') as AddTime, Date_format(LastChangeTime,'%Y-%m-%d') as LastChangeTime," .
   					"DstUrl, HotKeywords, AffIdMap, Editor, SEOTitle1, c_SEOTitle, IsActive, OriginalUrl, AllowPPC, PPCRestriction, CustomerServicePhone, LinkSource  ";
   			$sql .= "from normalmerchant $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				if($id == ""){
	   				$arr[$i]['id'] = intval($arrTmp['ID']);
	   				$arr[$i]['name'] = trim($arrTmp['Name']);
	   				$arr[$i]['desc'] = trim($arrTmp['c_Desc']);
	   				$arr[$i]['logo'] = trim($arrTmp['Logo']);
	   				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
	   				$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
	   				$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
	   				$arr[$i]['hotkeywords'] = trim($arrTmp['HotKeywords']);
	   				$arr[$i]['AffIdMap'] = trim($arrTmp['AffIdMap']);
	   				$arr[$i]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$i]['seoh1line'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$i]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
	   				$arr[$i]['IsActive'] = trim($arrTmp['IsActive']);
	   				$arr[$i]['Editor'] = trim($arrTmp['Editor']);
	   				$arr[$i]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
	   				$arr[$i]['AllowPPC'] = trim($arrTmp['AllowPPC']);
	   				$arr[$i]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
	   				$arr[$i]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
	   				$arr[$i]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
	   				$arr[$i]['LinkSource'] = trim($arrTmp['LinkSource']);
	   				$i++;
   				}else{
   					$arr[$arrTmp[$id]]['id'] = intval($arrTmp['ID']);
	   				$arr[$arrTmp[$id]]['name'] = trim($arrTmp['Name']);
	   				$arr[$arrTmp[$id]]['desc'] = trim($arrTmp['c_Desc']);
	   				$arr[$arrTmp[$id]]['logo'] = trim($arrTmp['Logo']);
	   				$arr[$arrTmp[$id]]['addtime'] = trim($arrTmp['AddTime']);
	   				$arr[$arrTmp[$id]]['affurl'] = trim($arrTmp['AffUrl']);
	   				$arr[$arrTmp[$id]]['dsturl'] = trim($arrTmp['DstUrl']);
	   				$arr[$arrTmp[$id]]['hotkeywords'] = trim($arrTmp['HotKeywords']);
	   				$arr[$arrTmp[$id]]['AffIdMap'] = trim($arrTmp['AffIdMap']);
	   				$arr[$arrTmp[$id]]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$arrTmp[$id]]['seoh1line'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$arrTmp[$id]]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
	   				$arr[$arrTmp[$id]]['IsActive'] = trim($arrTmp['IsActive']);
	   				$arr[$arrTmp[$id]]['Editor'] = trim($arrTmp['Editor']);
	   				$arr[$arrTmp[$id]]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
	   				$arr[$arrTmp[$id]]['AllowPPC'] = trim($arrTmp['AllowPPC']);
	   				$arr[$arrTmp[$id]]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
	   				$arr[$arrTmp[$id]]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
	   				$arr[$arrTmp[$id]]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
	   				$arr[$arrTmp[$id]]['LinkSource'] = trim($arrTmp['LinkSource']);
   				}
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
		function getMerchantListByRankStr($limitStr="", $whereStr="", $orderStr="", $id = "", $rankFlag = false)
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select SQL_CALC_FOUND_ROWS a.ID, a.Name, a.c_Desc, a.Logo, Date_format(a.AddTime,'%Y-%m-%d') as AddTime, Date_format(a.LastChangeTime,'%Y-%m-%d') as LastChangeTime," .
   					"a.DstUrl, a.HotKeywords, a.AffIdMap, a.Editor, a.SEOTitle1, a.c_SEOTitle, a.IsActive, a.OriginalUrl, a.AllowPPC, a.PPCRestriction, a.CustomerServicePhone, a.LinkSource, b.Rank, b.Traffic, b.Grade, b.CustomLink  ";
   			if($rankFlag){
   				$sql .=  " ,CASE WHEN b.Priority = '1' THEN 9999 ELSE 0  END AS PPP ";
   			}
   			$sql .= "from normalmerchant a INNER JOIN normalmerchant_addinfo b on a.ID = b.ID $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				if($id == ""){
	   				$arr[$i]['id'] = intval($arrTmp['ID']);
	   				$arr[$i]['name'] = trim($arrTmp['Name']);
	   				$arr[$i]['desc'] = trim($arrTmp['c_Desc']);
	   				$arr[$i]['logo'] = trim($arrTmp['Logo']);
	   				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
	   				$arr[$i]['affurl'] = trim($arrTmp['AffUrl']);
	   				$arr[$i]['dsturl'] = trim($arrTmp['DstUrl']);
	   				$arr[$i]['hotkeywords'] = trim($arrTmp['HotKeywords']);
	   				$arr[$i]['AffIdMap'] = trim($arrTmp['AffIdMap']);
	   				$arr[$i]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$i]['seoh1line'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$i]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
	   				$arr[$i]['IsActive'] = trim($arrTmp['IsActive']);
	   				$arr[$i]['Editor'] = trim($arrTmp['Editor']);
	   				$arr[$i]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
	   				$arr[$i]['AllowPPC'] = trim($arrTmp['AllowPPC']);
	   				$arr[$i]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
	   				$arr[$i]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
	   				$arr[$i]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
	   				$arr[$i]['LinkSource'] = trim($arrTmp['LinkSource']);
	   				$i++;
   				}else{
   					$arr[$arrTmp[$id]]['id'] = intval($arrTmp['ID']);
	   				$arr[$arrTmp[$id]]['name'] = trim($arrTmp['Name']);
	   				$arr[$arrTmp[$id]]['desc'] = trim($arrTmp['c_Desc']);
	   				$arr[$arrTmp[$id]]['logo'] = trim($arrTmp['Logo']);
	   				$arr[$arrTmp[$id]]['addtime'] = trim($arrTmp['AddTime']);
	   				$arr[$arrTmp[$id]]['affurl'] = trim($arrTmp['AffUrl']);
	   				$arr[$arrTmp[$id]]['dsturl'] = trim($arrTmp['DstUrl']);
	   				$arr[$arrTmp[$id]]['hotkeywords'] = trim($arrTmp['HotKeywords']);
	   				$arr[$arrTmp[$id]]['AffIdMap'] = trim($arrTmp['AffIdMap']);
	   				$arr[$arrTmp[$id]]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$arrTmp[$id]]['seoh1line'] = trim($arrTmp['SEOTitle1']);
	   				$arr[$arrTmp[$id]]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
	   				$arr[$arrTmp[$id]]['IsActive'] = trim($arrTmp['IsActive']);
	   				$arr[$arrTmp[$id]]['Editor'] = trim($arrTmp['Editor']);
	   				$arr[$arrTmp[$id]]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
	   				$arr[$arrTmp[$id]]['AllowPPC'] = trim($arrTmp['AllowPPC']);
	   				$arr[$arrTmp[$id]]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
	   				$arr[$arrTmp[$id]]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
	   				$arr[$arrTmp[$id]]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
	   				$arr[$arrTmp[$id]]['LinkSource'] = trim($arrTmp['LinkSource']);
	   				$arr[$arrTmp[$id]]['Rank'] = trim($arrTmp['Rank']);
	   				$arr[$arrTmp[$id]]['Traffic'] = trim($arrTmp['Traffic']);
	   				$arr[$arrTmp[$id]]['Grade'] = trim($arrTmp['Grade']);
	   				$arr[$arrTmp[$id]]['deepurl'] = trim($arrTmp['CustomLink']);
   				}
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
		function getMerchantListByCondisionStr($limitStr="", $whereStr="", $orderStr="", $id = "", $rankFlag = false)
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE 1=1 $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select SQL_CALC_FOUND_ROWS a.ID, a.Name, a.c_Desc, a.Logo, Date_format(a.AddTime,'%Y-%m-%d') as AddTime, Date_format(a.LastChangeTime,'%Y-%m-%d') as LastChangeTime," .
   					"a.DstUrl, a.HotKeywords, a.AffIdMap, a.Editor, a.SEOTitle1,a.c_SEOTitle, a.IsActive, a.OriginalUrl, a.AllowPPC, a.PPCRestriction, a.CustomerServicePhone, a.LinkSource, b.Rank, b.Traffic, b.Grade, b.CustomLink  ";
   			if($rankFlag){
   				$sql .=  " ,CASE WHEN b.Priority = '1' THEN 9999 ELSE 0  END AS PPP ";
   			}
   			$sql .= "from normalmerchant a 
   							INNER JOIN normalmerchant_addinfo b on a.ID = b.ID 
   						$whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
//   			echo $sql;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$arrTmp['ID']]['id'] = intval($arrTmp['ID']);
   				$arr[$arrTmp['ID']]['name'] = trim($arrTmp['Name']);
   				$arr[$arrTmp['ID']]['desc'] = trim($arrTmp['c_Desc']);
   				$arr[$arrTmp['ID']]['logo'] = trim($arrTmp['Logo']);
   				$arr[$arrTmp['ID']]['addtime'] = trim($arrTmp['AddTime']);
   				$arr[$arrTmp['ID']]['affurl'] = trim($arrTmp['AffUrl']);
   				$arr[$arrTmp['ID']]['dsturl'] = trim($arrTmp['DstUrl']);
   				$arr[$arrTmp['ID']]['hotkeywords'] = trim($arrTmp['HotKeywords']);
   				$arr[$arrTmp['ID']]['AffIdMap'] = trim($arrTmp['AffIdMap']);
   				$arr[$arrTmp['ID']]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
   				$arr[$arrTmp['ID']]['seoh1line'] = trim($arrTmp['SEOTitle1']);
   				$arr[$arrTmp['ID']]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
   				$arr[$arrTmp['ID']]['IsActive'] = trim($arrTmp['IsActive']);
   				$arr[$arrTmp['ID']]['Editor'] = trim($arrTmp['Editor']);
   				$arr[$arrTmp['ID']]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
   				$arr[$arrTmp['ID']]['AllowPPC'] = trim($arrTmp['AllowPPC']);
   				$arr[$arrTmp['ID']]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
   				$arr[$arrTmp['ID']]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
   				$arr[$arrTmp['ID']]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
   				$arr[$arrTmp['ID']]['LinkSource'] = trim($arrTmp['LinkSource']);
   				$arr[$arrTmp['ID']]['Rank'] = trim($arrTmp['Rank']);
   				$arr[$arrTmp['ID']]['Traffic'] = trim($arrTmp['Traffic']);
   				$arr[$arrTmp['ID']]['Grade'] = trim($arrTmp['Grade']);
   				$arr[$arrTmp['ID']]['deepurl'] = trim($arrTmp['CustomLink']);
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
		function getMerchantListByAffStr($limitStr="", $whereStr="", $orderStr="", $id = "", $rankFlag = false)
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE 1=1 $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select SQL_CALC_FOUND_ROWS distinct a.ID, a.Name, a.c_Desc, a.Logo, Date_format(a.AddTime,'%Y-%m-%d') as AddTime, Date_format(a.LastChangeTime,'%Y-%m-%d') as LastChangeTime," .
   					"a.DstUrl, a.HotKeywords, a.AffIdMap, a.Editor, a.SEOTitle1,a.c_SEOTitle, a.IsActive, a.OriginalUrl, a.AllowPPC, a.PPCRestriction, a.CustomerServicePhone, a.LinkSource, b.Rank, b.Traffic, b.Grade, b.CustomLink  " ;
   			if($rankFlag){
   				$sql .=  " ,CASE WHEN b.Priority = '1' THEN 9999 ELSE 0  END AS PPP ";
   			}
   			$sql .=	"from normalmerchant a 
   						INNER JOIN normalmerchant_addinfo b on a.ID = b.ID 
   						left join wf_mer_in_aff c on a.ID = c.MerID and c.IsUsing = 1
   						left join wf_aff d on c.AffID = d.ID 
   					$whereStr $orderStr $limitStr";
   			
   			$qryId = $this->objMysql->query($sql);
   			
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$arrTmp['ID']]['id'] = intval($arrTmp['ID']);
   				$arr[$arrTmp['ID']]['name'] = trim($arrTmp['Name']);
   				$arr[$arrTmp['ID']]['desc'] = trim($arrTmp['c_Desc']);
   				$arr[$arrTmp['ID']]['logo'] = trim($arrTmp['Logo']);
   				$arr[$arrTmp['ID']]['addtime'] = trim($arrTmp['AddTime']);
   				$arr[$arrTmp['ID']]['affurl'] = trim($arrTmp['AffUrl']);
   				$arr[$arrTmp['ID']]['dsturl'] = trim($arrTmp['DstUrl']);
   				$arr[$arrTmp['ID']]['hotkeywords'] = trim($arrTmp['HotKeywords']);
   				$arr[$arrTmp['ID']]['AffIdMap'] = trim($arrTmp['AffIdMap']);
   				$arr[$arrTmp['ID']]['SEOTitle1'] = trim($arrTmp['SEOTitle1']);
   				$arr[$arrTmp['ID']]['seoh1line'] = trim($arrTmp['SEOTitle1']);
   				$arr[$arrTmp['ID']]['seoheaderline'] = trim($arrTmp['c_SEOTitle']);
   				$arr[$arrTmp['ID']]['IsActive'] = trim($arrTmp['IsActive']);
   				$arr[$arrTmp['ID']]['Editor'] = trim($arrTmp['Editor']);
   				$arr[$arrTmp['ID']]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
   				$arr[$arrTmp['ID']]['AllowPPC'] = trim($arrTmp['AllowPPC']);
   				$arr[$arrTmp['ID']]['PPCRestriction'] = trim($arrTmp['PPCRestriction']);
   				$arr[$arrTmp['ID']]['CustomerServicePhone'] = trim($arrTmp['CustomerServicePhone']);
   				$arr[$arrTmp['ID']]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
   				$arr[$arrTmp['ID']]['LinkSource'] = trim($arrTmp['LinkSource']);
   				$arr[$arrTmp['ID']]['Rank'] = trim($arrTmp['Rank']);
   				$arr[$arrTmp['ID']]['Traffic'] = trim($arrTmp['Traffic']);
   				$arr[$arrTmp['ID']]['Grade'] = trim($arrTmp['Grade']);
   				$arr[$arrTmp['ID']]['deepurl'] = trim($arrTmp['CustomLink']);
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		
   		function getMerchantInfoList($limitStr="", $whereStr="", $orderStr="", $sortby = "")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE 1=1 $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select SQL_CALC_FOUND_ROWS a.ID, a.Name, a.Logo, a.TaskUpdateCycle, ".
   					"b.AssignedEditor, a.OriginalUrl, b.Rank, b.Traffic, b.Grade, b.LastCheckTime, b.EditorTips, b.CustomLink";
   			$sql .= " from normalmerchant a 
   							INNER JOIN normalmerchant_addinfo b on a.ID = b.ID 
   						$whereStr $orderStr $sortby $limitStr";
   			$qryId = $this->objMysql->query($sql);
//   			echo $sql;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$arrTmp['ID']]['id'] = intval($arrTmp['ID']);
   				$arr[$arrTmp['ID']]['name'] = trim($arrTmp['Name']);
   				$arr[$arrTmp['ID']]['desc'] = trim($arrTmp['c_Desc']);
   				$arr[$arrTmp['ID']]['logo'] = trim($arrTmp['Logo']);
   				$arr[$arrTmp['ID']]['OriginalUrl'] = trim($arrTmp['OriginalUrl']);
   				$arr[$arrTmp['ID']]['AssignedEditor'] = trim($arrTmp['AssignedEditor']);
   				$arr[$arrTmp['ID']]['Rank'] = trim($arrTmp['Rank']);
   				$arr[$arrTmp['ID']]['Grade'] = trim($arrTmp['Grade']);
   				$arr[$arrTmp['ID']]['TaskUpdateCycle'] = trim($arrTmp['TaskUpdateCycle']);
   				$arr[$arrTmp['ID']]['LastCheckTime'] = trim($arrTmp['LastCheckTime']);
   				$arr[$arrTmp['ID']]['EditorTips'] = trim($arrTmp['EditorTips']);
   				$arr[$arrTmp['ID']]['deepurl'] = trim($arrTmp['CustomLink']);
   				
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		function addMerchant($name, $desc, $logo, $addTime, $url, $keywords, $merchant_aff_id, $allowppc, $ppcrestriction, $IsActive, $OriginalUrl, $SEOHeaderLine, $SEOH1Line, $ppcphonenumber, $LinkSource)
		{
			$name = addslashes(trim($name));
			$desc = addslashes(trim($desc));
			$logo = addslashes(trim($logo));
			$dstUrl = addslashes(trim($dstUrl));
			$addTime = addslashes(trim($addTime));
			$url = addslashes(trim($url));
			$keywords = addslashes(trim($keywords));
			$merchant_aff_id = addslashes(trim($merchant_aff_id));
			$OriginalUrl = addslashes(trim($OriginalUrl));
			$SEOHeaderLine = addslashes(trim($SEOHeaderLine));
			$SEOH1Line = addslashes(trim($SEOH1Line));
			$ppcphonenumber = addslashes(trim($ppcphonenumber));
			$LinkSource = addslashes(trim($LinkSource));
			
			global $PHP_AUTH_USER;
			$editorname = $PHP_AUTH_USER ? $PHP_AUTH_USER : $_SERVER["REMOTE_USER"];
			$sql = "INSERT INTO normalmerchant (Name, c_Desc, Logo, AddTime, " .
   					"DstUrl, HotKeywords, AffIdMap, Editor, AllowPPC, PPCRestriction, IsActive, OriginalUrl, c_SEOTitle, SEOTitle1, CustomerServicePhone, LinkSource) VALUES(" .
   					"'$name', '$desc', '$logo', '$addTime', '$url', '$keywords', '$merchant_aff_id', " .
   					"'".addslashes($editorname)."', '$allowppc', '".addslashes($ppcrestriction)."', '$IsActive', '$OriginalUrl','$SEOHeaderLine','$SEOH1Line','$ppcphonenumber','$LinkSource')";
   			$qryId = $this->objMysql->query($sql);
   			return $this->objMysql->getLastInsertId($qryId);
		}
		
		function updateMerchantById($name, $desc, $logo, $url, $keywords, $merchant_aff_id, $ppc, $ppcRestriction, $merchantId, $IsActive, $OriginalUrl, $SEOHeaderLine, $SEOH1Line, $ppcphonenumber, $LinkSource)
		{
			$merchantId = intval($merchantId);
			$name = addslashes(trim($name));
			$desc = addslashes(trim($desc));
			$logo = addslashes(trim($logo));
			$dstUrl = addslashes(trim($url));
			$url = addslashes(trim($url));
			$keywords = addslashes(trim($keywords));
			$merchant_aff_id = addslashes(trim($merchant_aff_id));
			$ppc = addslashes(trim($ppc));
			$ppcRestriction = addslashes(trim($ppcRestriction));
			$OriginalUrl = addslashes(trim($OriginalUrl));
			$SEOHeaderLine = addslashes(trim($SEOHeaderLine));
			$SEOH1Line = addslashes(trim($SEOH1Line));
			$ppcphonenumber = addslashes(trim($ppcphonenumber));
			$LinkSource = addslashes(trim($LinkSource));

			$sql = "UPDATE normalmerchant set Name = '$name', c_Desc = '$desc', Logo = '$logo', " .
					"DstUrl = '$dstUrl', HotKeywords= '$keywords', AffIdMap = '$merchant_aff_id', " .
					"AllowPPC = '$ppc', PPCRestriction = '$ppcRestriction', IsActive = '$IsActive', OriginalUrl = '$OriginalUrl' ,c_SEOTitle = '$SEOHeaderLine', SEOTitle1 = '$SEOH1Line', CustomerServicePhone = '$ppcphonenumber', LinkSource = '$LinkSource' where ID = $merchantId" ;
   			$this->objMysql->query($sql);
		}

		function updateMerchantIDInAffById($merchantId, $arrMerchantAffIDs){
			$merchantId = intval($merchantId);
			//remove exits
			$sql = "DELETE FROM wf_mer_in_aff WHERE MerID = $merchantId and IsUsing = 1";
			$this->objMysql->query($sql);
			
			//add new 
			foreach ($arrMerchantAffIDs as $nAffID => $nAffMerID){
				//TODO delete affid == 11;
				if(trim($nAffID) == "11"){
					continue;
				}
				$sql = "REPLACE INTO wf_mer_in_aff(AffID, MerID, MerIDinAff, Relationship) VALUES ($nAffID, $merchantId, '$nAffMerID', 1)";
				$this->objMysql->query($sql);
			}
		}

		function getMerchantIDsInAffById($merchantId){
			$merchantId = intval($merchantId);
			$sql = "SELECT AffID, MerIDinAff FROM wf_mer_in_aff WHERE MerID = $merchantId and IsUsing = 1";
			$qry = $this->objMysql->query($sql);
			return $qry;
		}

		function isMerchantIn3rdPartyAffNetwork($merchantId){
			$merchantId = intval($merchantId);
			$sql = "SELECT AffID FROM wf_mer_in_aff WHERE MerID = $merchantId AND AffID <> 11 and IsUsing = 1";
			$arr = $this->objMysql->getRows($sql);
			if(empty($arr)) return false;
			foreach($arr as $row)
			{
				if($row['AffID'] == 9) return false;
			}
			return true;
		}

		function getMerchantById($merchantId)
		{
			$merchantId = intval($merchantId);
			if(isset($this->cache_merchant_info[$merchantId])) return $this->cache_merchant_info[$merchantId];
   			$arr = $this->getMerchantListByLimitStr("", " ID = $merchantId");
			if(empty($arr)) return $arr;
			$arr = $arr[0];
			//get add info
			//TODO: 
			$sql = "SELECT `Alias`, `CustomLink`, `AssignedEditor`, `Priority`, `ContactInfo`, `EditorTips`, `MetaTitle`, `MetaKeywords`, `MetaDesc`, `CouponTitle1`, `CouponTitle2`, `CouponTitle3`, `CouponTitle4`, `CouponTitle5`, `AllowExtDeal` FROM normalmerchant_addinfo WHERE ID = $merchantId LIMIT 1";
			$arrAddInfo = $this->objMysql->getFirstRow($sql);
			foreach($arrAddInfo as $k => $v) $arr[$k] = $v;
			$arr["assignededitor"] = $arr["AssignedEditor"];
			$arr["alias"] = $arr["Alias"];
			$arr["customurl"] = $arr["CustomLink"];			
			$this->cache_merchant_info[$merchantId] = $arr;
			return $arr;
		}

		function updateMerchantAddInfoById($merchantId, $merchantalias, $customurl, $assigned_editor, $mer_priority, $contact_info, $editor_tips, $MetaTitle, $MetaKeywords, $MetaDesc, $CouponTitle1, $CouponTitle2, $CouponTitle3, $CouponTitle4, $CouponTitle5, $AllowExtDeal){
			$sql = "REPLACE INTO normalmerchant_addinfo (ID, `Alias`, `CustomLink`, `AssignedEditor`, `Priority`, `ContactInfo`, `EditorTips`, `MetaTitle`, `MetaKeywords`, `MetaDesc`, `CouponTitle1`, `CouponTitle2`, `CouponTitle3`, `CouponTitle4`, `CouponTitle5`, `AllowExtDeal`) VALUES($merchantId, \"".addslashes(trim($merchantalias))."\", \"".addslashes(trim($customurl))."\", \"".addslashes(trim($assigned_editor))."\", \"".addslashes(trim($mer_priority))."\", \"".addslashes(trim($contact_info))."\", \"".addslashes(trim($editor_tips))."\", \"".addslashes(trim($MetaTitle))."\", \"".addslashes(trim($MetaKeywords))."\", \"".addslashes(trim($MetaDesc))."\", \"".addslashes(trim($CouponTitle1))."\", \"".addslashes(trim($CouponTitle2))."\", \"".addslashes(trim($CouponTitle3))."\", \"".addslashes(trim($CouponTitle4))."\", \"".addslashes(trim($CouponTitle5))."\", \"".addslashes(trim($AllowExtDeal))."\")";
			//echo $sql;
			$this->objMysql->query($sql);
			unset($sql);
		}

		//add by ran 2009-05-11 //add deep url into template and return
		function getMerchantUrlwithDeepUrl($merchantId, $strDeepUrl){
			//check if have custom deep url template
			//return -1 if have not template yet.
			$sql = "SELECT CustomLink FROM normalmerchant_addinfo WHERE ID = $merchantId LIMIT 1";
			$qry = $this->objMysql->query($sql);
			if($arrTmp = $this->objMysql->getRow($qry)){
				$result = $arrTmp['CustomLink'];
				unset($arrTmp);
				//add by ran 2009-05-19
				//spec for SAS
				if (stripos($result, 'shareasale.com')){
					$nTmp = stripos($strDeepUrl, 'http://');
					if ($nTmp !== false){
						$strDeepUrl = substr($strDeepUrl, $nTmp + 7);
					}
					else{
						$nTmp = stripos($strDeepUrl, urlencode('http://'));
						if ($nTmp !== false){
							$strDeepUrl = substr($strDeepUrl, strlen(urlencode('http://')));
						}
					}
				}
				//
				//changed by jimmy @ 2010-01-18
				if(stripos($result, '[DEEPURL]') === 0)
					$result = str_replace('[DEEPURL]', $strDeepUrl, $result);
				else
					$result = str_replace('[DEEPURL]', urlencode($strDeepUrl), $result);

			}
			else{
				$result = -1;
			}
			unset($sql);
			$this->objMysql->freeResult($qry);

			return $result;
		}
		//

		function getMerchantQuickLinks($merchantId)
	    {
			$merchantId = intval($merchantId);
			$arr = array();
			$sql = "select ID, Title, URL, Alt from normalmerchant_quick_links where MID = $merchantId order by ID asc";
			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
				$arr[$i]['title'] = trim($arrTmp['Title']);
				$arr[$i]['url'] = trim($arrTmp['URL']);
				$arr[$i]['alt'] = trim($arrTmp['Alt']);
				$arr[$i]['id'] = trim($arrTmp['ID']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
   			return $arr;
	    }

		function getMerchantQuickLinksByID($id)
	    {
			$id = intval($id);
			$arr = array();
			$sql = "select MID, Title, URL, Alt from normalmerchant_quick_links where ID = $id";
			$qryId = $this->objMysql->query($sql);
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
				$arr['title'] = trim($arrTmp['MID']);
				$arr['url'] = trim($arrTmp['URL']);
				$arr['alt'] = trim($arrTmp['Alt']);
				$arr['mid'] = trim($arrTmp['MID']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
   			return $arr;
	    }


		function getMerchantRelatedKw($merchantId)
	    {
			$merchantId = intval($merchantId);
			$arr = array();
			$sql = "select ID, `Keyword` as k from normalmerchant_related_searchkw where MID = $merchantId order by ID asc";
			$qryId = $this->objMysql->query($sql);
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
				$arr[] = trim($arrTmp['k']);
			}
			$this->objMysql->freeResult($qryId);
   			return $arr;
	    }

		function getMerchantBlogUrl($merchantId)
	   {
			$merchantId = intval($merchantId);
			$arr = array();
			$arrRes = array();
			$sql = "select ID, BlogTitle from normalmerchant_article where MID = $merchantId order by ID asc";
			$qryId = $this->objMysql->query($sql);
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
				$arr[] = addslashes(trim($arrTmp['BlogTitle']));
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
	   }
		
		function getMerchantArticle($merchantId)
	    {
			$arrRes = array();
			$arr = $this->getMerchantBlogUrl($merchantId);
			//get blog post from db
			if(count($arr))
			{
				$sql = "select post_title, post_content from blog_posts where post_name in ('".implode("','", $arr)."')";
				$qryId = $this->objMysql->query($sql);
				$i = 0;
				while($arrTmp = $this->objMysql->getRow($qryId))
				{
					$arrRes[$i]['title'] = trim($arrTmp['post_title']);
					$arrRes[$i]['content'] = trim($arrTmp['post_content']);
					$i++;
				}
				$this->objMysql->freeResult($qryId);
			}
   			return $arrRes;
	    }

		function addQuickLinks($arr, $mid)
	   {
			//print_r($arr);
			$mid = intval($mid);
			//remove exits
			$sql = "DELETE FROM normalmerchant_quick_links WHERE MID = $mid";
			$this->objMysql->query($sql);
			
			//add new 
			foreach ($arr as $v)
			{
				if (strtoupper(substr($v['u'], 0, 9)) == '[DEEPURL]'){
					$v['u'] = $this->getMerchantUrlwithDeepUrl($mid, substr($v['u'], 9));
				}
				$sql = "Insert INTO normalmerchant_quick_links(Title, URL, Alt, MID) VALUES ('".addslashes($v['t'])."', '".addslashes($v['u'])."','".addslashes($v['r'])."', $mid)";
				//echo $sql."<br>\n";
				$this->objMysql->query($sql);
			}
			return;
	   }

	   function addRelatedSearchKeywords($arr, $mid)
	   {
		   	$mid = intval($mid);
			//remove exits
			$sql = "DELETE FROM normalmerchant_related_searchkw WHERE MID = $mid";
			$this->objMysql->query($sql);
			
			//add new 
			foreach ($arr as $v)
			{
				$sql = "Insert INTO normalmerchant_related_searchkw(Keyword, MID) VALUES ('".addslashes($v)."', $mid)";
				$this->objMysql->query($sql);
			}
			return;
	   }
	   function addBlogArticles($arr, $mid)
	   {
		    $mid = intval($mid);
			//remove exits
			$sql = "DELETE FROM normalmerchant_article WHERE MID = $mid";
			$this->objMysql->query($sql);
			
			//add new 
			foreach ($arr as $v)
			{
				$sql = "Insert INTO normalmerchant_article(BlogTitle, MID) VALUES ('".addslashes($v)."', $mid)";
				$this->objMysql->query($sql);
			}
			return;
	   }

		function getMerchantPendingLinkCnt($merchantId=0, $type='pending'){
			if($merchantId){
				$sql = "SELECT COUNT(*) as cnt, internal_merid FROM wf_aff_links WHERE internal_merid = $merchantId";
				if($type!=''){
					$sql.= " AND proc_status = '$type'";
				}
			}
			else{
				$sql = "SELECT COUNT(*) as cnt, internal_merid FROM wf_aff_links ";
				if($type!=''){
					$sql.= " WHERE proc_status = '$type'";
				}
				$sql.= " GROUP BY internal_merid ";
			}
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$merchantId = intval($arrTmp['internal_merid']);
				$counponCnt = intval($arrTmp['cnt']);
				$arrRtn[$merchantId] = $counponCnt;
			}
			$this->objMysql->freeResult($qryId);
			return $arrRtn;
		}

		function getMerchantCouponCnt($merchantId=0, $type=1)
		{
			$arrRtn = array();
			if($merchantId)
			{ 	
				if(is_array($merchantId)){
					if($type == 1) //valid coupons
					{
						$sql = "select count(*) as cnt, MerchantID from normalcoupon where MerchantID in ('".implode("','", $merchantId) . "') ";
						$sql .= " AND (ExpireTime = '0000-00-00 00:00:00' or ExpireTime >= '".date('Y-m-d')."') " .
							" AND IsActive = 'YES' and StartTime <= NOW() Group by MerchantID";
					}
					else //all coupons
					{
						$sql = "select count(*) as cnt, MerchantID from normalcoupon where MerchantID in ('".implode("','", $merchantId) . "') Group by MerchantID";
					}
					$qryId = $this->objMysql->query($sql);
	   				while($arrTmp = $this->objMysql->getRow($qryId))
					{
						$merchantId = intval($arrTmp['MerchantID']);
						$counponCnt = intval($arrTmp['cnt']);
						$arrRtn[$merchantId] = $counponCnt;
					}
	   				$this->objMysql->freeResult($qryId);
	   			
				}else{
					if($type == 1) //valid coupons
					{
						$sql = "select count(*) as cnt from normalcoupon where MerchantID = ".intval($merchantId);
						$sql .= " AND (ExpireTime = '0000-00-00 00:00:00' or ExpireTime >= '".date('Y-m-d')."')" .
								" AND IsActive = 'YES' and StartTime <= NOW()";
					}
					else //all coupons
					{
						$sql = "select count(*) as cnt from normalcoupon where MerchantID = ".intval($merchantId);
					}
					$qryId = $this->objMysql->query($sql);
	   				$arrTmp = $this->objMysql->getRow($qryId);
	   				$this->objMysql->freeResult($qryId);
	   				$counponCnt = intval($arrTmp['cnt']);
					$arrRtn[$merchantId] = $counponCnt;
				}
			}
			else
			{
				if($type == 1) //valid coupons
				{
					$sql = "select count(*) as cnt, MerchantID from normalcoupon where ";
					$sql .= " (ExpireTime = '0000-00-00 00:00:00' or ExpireTime >= '".date('Y-m-d')."') " .
							" AND IsActive = 'YES' and StartTime <= NOW() Group by MerchantID";
				}
				else //all coupons
				{
					$sql = "select count(*) as cnt, MerchantID from normalcoupon Group by MerchantID";
				}
				$qryId = $this->objMysql->query($sql);
				while($arrTmp = $this->objMysql->getRow($qryId))
				{
					$merchantId = intval($arrTmp['MerchantID']);
					$counponCnt = intval($arrTmp['cnt']);
					$arrRtn[$merchantId] = $counponCnt;
				}
   				$this->objMysql->freeResult($qryId);
			}
			return $arrRtn;
		}
		
		function getMerchant4indexPage($cnt=8)
		{
			$arrTmp = array();
			
			/*
			 * DELL 2 Coupons, HP 2 Coupons, Lenovo 2 Coupons, Buy.com, shopnbc, Zavier, whitesmoke
			 */
			
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 16 " .
					"order by c.AddTime DESC LIMIT 0, 3";
			$i = 0;
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
			
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 48 " .
					"order by c.AddTime DESC LIMIT 0, 2";
//			echo $sql;
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
			
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 1 " .
					"order by c.AddTime DESC LIMIT 0, 2";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 4 " .
					"order by c.AddTime DESC LIMIT 0, 2";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
			
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 29 " .
					"order by c.AddTime DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//Zavier
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 883 " .
					"order by c.AddTime DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//whitesmoke
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 366 " .
					"order by c.Code DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//Cheryl & Co
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 385 " .
					"order by c.Code DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//bed bath store
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 897 " .
					"order by c.Code DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//hostpapa
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 947 " .
					"order by c.Code DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);

			//NewBiiz
			$sql = "select c.ID, Title, Code, MerchantID, m.Name, m.Logo, " .
					"c.ExpireTime from normalcoupon as c, " .
					"normalmerchant as m where c.MerchantID = m.ID " .
					"and (c.ExpireTime = '0000-00-00 00:00:00' or c.ExpireTime >= '".date('Y-m-d')."') " .
					"and c.StartTime <= NOW() and c.IsActive = 'YES' and c.MerchantID = 1898 " .
					"order by c.Code DESC LIMIT 0, 1";
			$qryId = $this->objMysql->query($sql);
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arrTmp[$i]['merchantname'] = trim($arrRes['Name']);
				$arrTmp[$i]['merchantlogo'] = trim($arrRes['Logo']);
				$arrTmp[$i]['merchantid'] = trim($arrRes['MerchantID']);
				$arrTmp[$i]['coupontitle'] = trim($arrRes['Title']);
				$arrTmp[$i]['couponid'] = trim($arrRes['ID']);
				$arrTmp[$i]['couponexpiration'] = trim($arrRes['ExpireTime']);
				$arrTmp[$i]['couponcode'] = trim($arrRes['Code']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);


			return $arrTmp;
		}
		
		function getMerchantIntlResource($mid)
		{
			$arrRtn = array();
			$mid = intval($mid);
			$sql = "select GroupID from normalmerchant_intl_resource where MerchantID = $mid and SiteID = '".SID_PREFIX."' Limit 1";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			if(isset($arrTmp['GroupID']))
			{
				$gid = intval($arrTmp['GroupID']);
				
				$sql = "select ID, SiteID, MerchantID, CountryCode, Title, URL, AddTime, ShowOnSite from normalmerchant_intl_resource where GroupID = $gid Order By `order` ASC";
				$qryId = $this->objMysql->query($sql);
				$i = 0;
				while($arrRes = $this->objMysql->getRow($qryId))
				{
					if(strcasecmp(trim($arrRes['SiteID']), SID_PREFIX) == 0 && $arrRes['MerchantID'] == $mid)
						continue;
					if(strcasecmp(trim($arrRes['ShowOnSite']), 'all') <> 0 && strcasecmp(trim($arrRes['ShowOnSite']), SID_PREFIX) <> 0)
						continue;
					$arrRtn[$i]['country'] = trim($arrRes['CountryCode']);
					$arrRtn[$i]['title'] = trim($arrRes['Title']);
					$arrRtn[$i]['url'] = trim($arrRes['URL']);
					$arrRtn[$i]['addtime'] = trim($arrRes['AddTime']);
					$i++;
				}
				$this->objMysql->freeResult($qryId);
			}
			
			return $arrRtn;
		}
		//get more stores, for seo purpose
		function getMerchantRandom(){
			$sql   = "select normalmerchant.Name AS Name , normalmerchant.ID AS ID from normalmerchant_random LEFT JOIN normalmerchant ON(normalmerchant_random.MerchantID=normalmerchant.ID) where 1=1 ORDER BY normalmerchant_random.Order ASC,normalmerchant_random.MerchantID DESC";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			$arrRtn= array();
			while($arrRes = $this->objMysql->getRow($qryId))
			{
					$arrRtn[$i] = $arrRes;
					$i++;
			}
			$this->objMysql->freeResult($qryId);
			return $arrRtn;
		}
		
		function loadMerchantConfig($_id)
		{
			if(!isset($this->config[$_id]))
			{
				$sql = "SELECT Name,Value FROM normalmerchant_config where MerchantID = $_id";
				$this->config[$_id] = $this->objMysql->getRows($sql,"Name");
			}
			return $this->config[$_id];
		}
		
		function isPage404($_id)
		{
			$arrConfig = $this->loadMerchantConfig($_id);
			if(isset($arrConfig["404page"])) return true;
			return false;
		}
		
		function getAll404MerchantId()
		{
			$sql = "SHOW TABLES LIKE 'normalmerchant_config'";
			$table = $this->objMysql->getFirstRow($sql);
			if(empty($table)) return array();

			$sql = "SELECT MerchantID FROM normalmerchant_config where Name='404page'";
			return $this->objMysql->getRows($sql,"MerchantID");
		}
		
		function isShowNoProfitAds($_id)
		{
			$arrConfig = $this->loadMerchantConfig($_id);
			if(isset($arrConfig["showads"])) return 'ShowActiveAds';

			$arr_info = $this->getMerchantById($_id);
			if(isset($arr_info["IsActive"]) && $arr_info["IsActive"] == "NO") return 'ShowInactiveAds';
			
			$sql = "SELECT AffID FROM wf_mer_in_aff WHERE MerID = $_id AND AffID = 11 and IsUsing = 1 LIMIT 1";
			$AffID = $this->objMysql->getFirstRowColumn($sql);
			if($AffID) return 'ShowInactiveAds';
			
			return 'NoAds';
		}
		
		function getMerchantByTagIds($_tag_ids,$_limit,$has_logo=true)
		{
			if(!$_tag_ids) return array();
			if(is_array($_tag_ids)) $_tag_ids = implode(",",$_tag_ids);
			$cond_has_logo = $has_logo ? "and MerLogo <> ''" : "";
			$sql = "select *,sum(CouponCnt) as SumCouponCnt from r_tagmerchant where TagID in ($_tag_ids) $cond_has_logo group by MerchantID order by SumCouponCnt desc limit $_limit";
			$arr = $this->objMysql->getRows($sql,"MerchantID");
			$_merchant_ids=array();	
			foreach($arr as $id => $row) {
				$arr[$id]["MerchantPageUrl"] = get_rewrited_url('merchant', $row["MerName"],$id);
				$_merchant_ids += array($id=>$arr[$id]["MerchantID"]);
			}
			$arr_cnt=$this->countMerchantCouponNumByMerchantIds($_merchant_ids);
			foreach($arr as $id => $row) {
				
				$arr[$id]["SumCouponCnt"] =	$arr_cnt[$id]["num"];
			}
			return $arr;
		}
		
		function getCustomizeMerchantByIds($_merchant_ids,$_limit,$has_logo=true)
		{
			if(!$_merchant_ids) return array();
			if(is_array($_merchant_ids)) $_merchant_ids = implode(",",$_merchant_ids);
			$cond_has_logo = $has_logo ? "and Logo <> ''" : "";
			//$sql = "select * from r_tagmerchant where MerchantID in ($_merchant_ids) $cond_has_logo group by MerchantID limit $_limit";
			$sql = "select *,ID as MerchantID,Logo as MerLogo, Name as MerName from normalmerchant where ID in ($_merchant_ids) $cond_has_logo limit $_limit";
			$arr = $this->objMysql->getRows($sql,"MerchantID");
			$arr_cnt=$this->countMerchantCouponNumByMerchantIds($_merchant_ids);
			foreach($arr as $id => $row){				
				$arr[$id]["MerchantPageUrl"] = get_rewrited_url('merchant', $row["MerName"],$id);
				//$num = $this->countMerchantCouponNumById($id);
				$arr[$id]["SumCouponCnt"] =	$arr_cnt[$id]["num"];			
			}
			return $arr;
		}
		
		function countMerchantCouponNumByMerchantIds($_merchant_ids)
		{
			if(!$_merchant_ids) return array();
			if(is_array($_merchant_ids)) $_merchant_ids = implode(",",$_merchant_ids);
			$sql = "select MerchantID, count(MerchantID) as num from normalcoupon where MerchantID in ($_merchant_ids) AND (ExpireTime = '0000-00-00 00:00:00' or ExpireTime >= '".date('Y-m-d')."') AND StartTime <= NOW() and IsActive = 'YES' group by MerchantID";
			$arr = $this->objMysql->getRows($sql,"MerchantID");	
			
			return $arr;
		}
		
  		function getMerchantAffByMerchantId($merchantId)
		{
			$merchantId = intval($merchantId);
			if(!$merchantId) return array();
			$sql="SELECT * FROM wf_aff AS a INNER JOIN wf_mer_in_aff AS b ON (b.MerID=$merchantId AND b.AffID=a.ID) and b.IsUsing = 1";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			$arr= array();
			while($arrRes = $this->objMysql->getRow($qryId))
			{
				$arr[$i] = $arrRes;
				$i++;
			}
			$this->objMysql->freeResult($qryId);		
			return $arr;
		}
		
		function SearchMerchantByName($q)
		{
			if(empty($q)) return array();
		
			$host = "";
			if(preg_match("/^http:/i",$q))
			{
				$host = @parse_url($q,PHP_URL_HOST);
			}
			elseif(preg_match("/\\b[0-9a-z-]{4,20}\\.[a-z]{2,3}\\b/i",$q,$matches))
			{
				$host = $matches[0];
			}

			if($host && substr(strtolower($host),0,4) == "www.") $host = substr($host,4);
					
			//exact match normalmerchant
			$arr_cond = array();
			if(is_numeric($q)) $arr_cond[] = "ID = '$q'";
			$arr_cond[] = "Name like '" . addslashes($q) . "%'";
			if($host) $arr_cond[] = "OriginalUrl regexp '(^|[^0-9a-z])" . addslashes($host) . "([^0-9a-z]|\$)'";
			$sql = "select ID from normalmerchant where " . implode(" or ",$arr_cond) . " limit 1000";
			$arr_ids = $this->objMysql->getRows($sql,"ID");
			if(sizeof($arr_ids) > 0) return array_keys($arr_ids);
			
			//exact match normalmerchant_addinfo
			$arr_cond = array();
			$arr_cond[] = "Alias like '" . addslashes($q) . "%'";
			$sql = "select ID from normalmerchant_addinfo where " . implode(" or ",$arr_cond) . " limit 1000";
			$arr_ids = $this->objMysql->getRows($sql,"ID");
			if(sizeof($arr_ids) > 0) return array_keys($arr_ids);
			
			//Approximate String Matching
			$arr_ids = array();
			$sql = "select ID,Name from normalmerchant";
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				similar_text($arrTmp["Name"],$q,$percentage);
				if($percentage > 70) $arr_ids[] = $arrTmp["ID"];
			}
			$this->objMysql->freeResult($qryId);
			return $arr_ids;
		}
   		function getAllCategoryByTree() {
			$data = array();
			$sql = "select * from `mer_category`";
			$dataSource = $this->objMysql->getRows($sql);
			
			foreach ($dataSource as $k => $v) {
				if ($v['ParentId'] == 0) {
					$data[$v['ID']]['ParentCate'] = array('ID' => $v['ID'], 'Name' => $v['Name']);
				} else {
					$data[$v['ParentId']]['ChildCate'][] = array('ID' => $v['ID'], 'Name' => $v['Name']);
				}
			}
			return $data;
		}
		function getAllEditor(){
			$sql = "SELECT DISTINCT AssignedEditor FROM normalmerchant_addinfo WHERE AssignedEditor <> ''";
			$qryId = $this->objMysql->query($sql);
			$resArr = array();
			while($arrTmp = $this->objMysql->getRow($qryId)){
				$resArr[$arrTmp["AssignedEditor"]] = $arrTmp["AssignedEditor"];
			}
			return $resArr;
		}
   		function IsNoneAffiliateMerchant($_merid)
		{
			/*$sql = "select MerID FROM wf_mer_in_aff WHERE MerID = '$_merid' AND AffID = 11";
			$rows = $this->objMysql->getRows($sql,"MerID");
			return isset($rows[$_merid]);*/
			$sql = "SELECT MerID FROM wf_mer_in_aff WHERE MerID = '$_merid' AND AffID <> 11 and IsUsing = 1";
			$rows = $this->objMysql->getRows($sql,"MerID");
			if(isset($rows[$_merid])){
				return false;
			}
			return true;
		}
		function getAffInfo($merId){
			if($merId == ""){
				return "";
			}
			$sql = "SELECT b.Name, a.MerIDinAff FROM `wf_mer_in_aff` a LEFT JOIN wf_aff b ON a.AffID = b.ID  WHERE a.MerID = '$merId' and a.IsUsing = 1";
			$rows = $this->objMysql->getRows($sql,"MerID");
			return $rows;
		}
		function getLastUpdateTime($mid){
			$time = "";
			$sql = "select LastChangeTime from normalcoupon where MerchantID = '$mid' order by LastChangeTime DESC limit 1";
			$arrTmp = $this->objMysql->getRows($sql);
			if(count($arrTmp) == 0){
				$time = "";
			}
			$time = $arrTmp[0]["LastChangeTime"];
			return $time;
		}
		function updateLastCheckTime($mid){
			$now = date("Y-m-d H:i:s");
			$sql = "UPDATE normalmerchant_addinfo SET  LastCheckTime = '$now' WHERE ID = '$mid'";
//			echo $sql;
			try{
				$res = $this->objMysql->query($sql);
				return $res;
			}catch(Exception $e){
				return false;
			}
		}
		
		function updateTaskUpdateCycle($mid, $taskUpdateCycle){
			$sql = "update normalmerchant set TaskUpdateCycle = '$taskUpdateCycle' where ID = '$mid'";
			try{
				$res = $this->objMysql->query($sql);
				return $res;
			}catch(Exception $e){
				return false;
			}
		}
		
		public function getPromoMerchantId($site,$merchantid){
			$sql="SELECT ID FROM `merchant_mapping` WHERE `Site` = '$site' AND M_ID = $merchantid ";
			$res=$this->objMysql->getFirstRowColumn($sql);
			return $res;
		}
	}
}
?>
