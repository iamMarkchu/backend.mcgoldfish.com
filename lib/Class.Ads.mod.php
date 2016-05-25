<?php
#onlinetools test
#onlinetools test2
if(!defined("__MOD_CLASS_ADS__"))
{
define("__MOD_CLASS_ADS__",1);
require_once(INCLUDE_ROOT .'lib/Class.TemplateSmarty.php');
class Ads
{
	var $objMysql;
	var $arr_ads_pos = array(
		'top-left' => "top_left",
		'top-center' => "top_center",
		'top-right' => "top_right",
		'middle-left' => "middle_left",
		'middle-center' => "middle_center",
		'middle-center-inactive-merchant' => "middle_center_inactive_merchant",
		'middle-center-active-merchant' => "middle_center_active_merchant",
		'middle-right' => "middle_right",
		'bottom-left' => "bottom_left",
		'bottom-center' => "bottom_center",
		'bottom-right' => "bottom_right",
		'page-right' => "page_right",
	);

	function Ads($objMysql)
	{
		$this->objMysql = $objMysql;
	}
	
	function getActiveAds($_page_name,$_page_value=0)
	{
		$arr_return = array();
		$_page_value = trim(strtolower($_page_value));
		if(empty($_page_value) || $_page_value == "0") $_page_value = 0;
		
		if(empty($_page_value))
		{
			$whereString = "PageValue in ('0','')";
		}
		else
		{
			//$whereString = "($whereString or PageValue like '%[". addslashes($_page_value) ."]%')";
			$whereString = "PageValue in ('0','','" . addslashes($_page_value) . "') or PageValue like '%[". addslashes($_page_value) ."]%'";
			if(defined("PAGE_VALUE") && $_page_name == "frame")
			{
				$whereString .= " or PageValue = '". addslashes($_page_value . ":" . PAGE_VALUE) ."'";
				$whereString .= " or PageValue like '%[". addslashes($_page_value . ":" . PAGE_VALUE) ."]%'";
			}
			$whereString = "($whereString)";
		}
		
		$sql = "select * from ads_config where Status = 'active' and now() >= StartDate and now() <= EndDate and PageName = '" . addslashes($_page_name) . "' and $whereString order by PageValue desc,PosType,PosIndex";
		$arrAds = $this->objMysql->getRows($sql);
		foreach($arrAds as &$row)
		{
			if($row["AdsType"] == "image" && defined("CDN_LINK_ROOT") && DEBUG_MODE == false) $row["Image"] = rtrim(CDN_LINK_ROOT,"/") .  $row["Image"];
			$page_value = trim(strtolower($row["PageValue"]));
			if(empty($page_value) || $_page_value == "0") $page_value = 0;
			elseif($_page_name == "frame" && ($page_value == $_page_value || strpos($page_value,"[".$_page_value."]") !== false)) $page_value = -1; //second level default
			else $page_value = $_page_value;
			$arr_return[$page_value][$row["PosType"]][] = $row;
		}
		
		if(empty($_page_value))
		{
			if(! isset($arr_return[0])) return array();
			return $arr_return[0];
		}
		else
		{
			if(! isset($arr_return[$_page_value])) $arr_return[$_page_value] = array();
			$arr_default = array(-1,0);
			foreach($arr_default as $default_key)
			{
				if(!isset($arr_return[$default_key])) continue;
				foreach($arr_return[$default_key] as $_pos_type => $_default_ad_list)
				{
					if(!isset($arr_return[$_page_value][$_pos_type]))
					{
						//fill by default
						$arr_return[$_page_value][$_pos_type] = $_default_ad_list;
					}
				}
			}
			return $arr_return[$_page_value];
		}
	}//end fun
	
	function getActiveAdsHtml($_page_name,$_page_value=0)
	{
		if(isset($this->cache_adshtml[$_page_name][$_page_value])) return $this->cache_adshtml[$_page_name][$_page_value];
		$arr_return = array();
		$arrAds = $this->getActiveAds($_page_name,$_page_value);
		$oTpl = new TemplateSmarty();
		foreach($arrAds as $_pos_type => $_ad_list)
		{
			$tpl_name = "ads_config/" . $_page_name . "_" . $this->arr_ads_pos[$_pos_type] . ".html";
			if(!$oTpl->template_exists($tpl_name)) continue;
			$oTpl->assign_by_ref("ads",$_ad_list);
			$arr_return[$_pos_type] = $oTpl->fetch($tpl_name);
		}
		foreach($this->arr_ads_pos as $_pos_type => $_page_var)
		{
			if(!isset($arr_return[$_pos_type])) $arr_return[$_pos_type] = "";
		}
		$this->cache_adshtml[$_page_name][$_page_value] = $arr_return;
		return $arr_return;
	}//end fun

	function fillPageAds($_oTpl,$_page_name,$_page_value=0)
	{
		$arrAdsHtml = $this->getActiveAdsHtml($_page_name,$_page_value);
		foreach($this->arr_ads_pos as $_pos_type => $_page_var)
		{
			$_page_name = str_replace("-","_",$_page_name);
			$_oTpl->assign("{$_page_name}_ads_config_" . $_page_var,$arrAdsHtml[$_pos_type]);
		}
	}
	
	//google afs 
	function getPageAfs($_page_name,$_page_value=0)
	{
		$arr_return = array();
		$sql = "select * from ads_config where Status = 'active' and now() >= StartDate and now() <= EndDate and PageName = '" . addslashes($_page_name) . "' and (PageValue = '" . addslashes($_page_value) . "' or PageValue like '%[". addslashes($_page_value) ."]%') and adsType = 'afs'";
		$arrAds = $this->objMysql->getRows($sql);
		foreach($arrAds as &$row)
		{			
			$arr_return[$_page_value] = $row;
		}
		return $arr_return;
	}
}//end class
}//end if defined
?>
