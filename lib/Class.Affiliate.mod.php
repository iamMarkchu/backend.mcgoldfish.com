<?php
class Affiliate
{
	var $objMysql;
	
	function Affiliate($objMysql)
	{
		$this->objMysql = $objMysql;
	}
	
	function getAffUrlInfoByUrl($dstUrl)
	{
		$sql = "select AffUrlPattern,AffAliasName,AffId,AffSubIdPattern from wf_aff_url_pattern WHERE '" . addslashes($dstUrl) . "' LIKE CONCAT('%',AffUrlPattern,'%') limit 1";
		return $this->objMysql->getFirstRow($sql);
	}
	
	function getAffDeepUrlParaNameById($affid)
	{
		$sql = "select DeepUrlParaName from wf_aff WHERE ID = '" . addslashes($affid) . "'";
		return $this->objMysql->getFirstRowColumn($sql);
	}
	
	function getAffNameByUrl($dstUrl)
	{
		$aff_url_info = $this->getAffUrlInfoByUrl($dstUrl);
		if(empty($aff_url_info)) return "";
		return $aff_url_info["AffAliasName"];
	}
	
	function getCJPIDFromURL($dstUrl)
	{
		$dstUrl = trim($dstUrl);
		$pattern = "/\\/click-([^-]+)-/i";
		if(preg_match($pattern,$dstUrl,$matches))
		{
			return $matches[1];
		}
		return "";
	}
	
	function getCJProgIdBySite()
	{
		if(!defined("SITE_NAME")) return "";
		$sql = "select ConfigValue from wf_aff_config where ConfigType = 'site_cj_progid' and ConfigName = '" . addslashes(SITE_NAME) . "'";
		return $this->objMysql->getFirstRowColumn($sql);
	}
	
	function getAffUrlWithSID($dstUrl,$incomingId,$outgoingId,$serverid="",$currserverid="")
	{
		$dstUrl = trim($dstUrl);
		$aff_url_info = $this->getAffUrlInfoByUrl($dstUrl);
		if(empty($aff_url_info))
		{
			//add error log here, some destination url may be wrong
			if(defined("LOG_LOCATION"))
			{
				$logfile = LOG_LOCATION . "badaffurl.txt";
				
				$fileds = array();
				$fileds[] = date("Y-m-d H:i:s");
				$fileds[] = $incomingId;
				$fileds[] = $dstUrl;
				$fileds[] = $outgoingId;
				$fileds[] = $serverid;
				$fileds[] = $currserverid;
				
				$line = implode("\t",$fileds) . "\n";
				@error_log($line,3,$logfile);
			}
			return $dstUrl;
		}
		
		//replace CJ PID
		if($aff_url_info["AffAliasName"] == "cj")
		{
			$CJPid = $this->getCJPIDFromURL($dstUrl);
			$newCJPid = $this->getCJProgIdBySite();
			if($CJPid && $newCJPid)
			{
				$pattern = "/\\/click-$CJPid-/i";
				$replace_to = "/click-$newCJPid-";
				$dstUrl = preg_replace($pattern,$replace_to,$dstUrl);
			}
		}
		//end
		
		if(isset($aff_url_info["AffSubIdPattern"]) && $aff_url_info["AffSubIdPattern"])
		{
			$sid = $aff_url_info["AffSubIdPattern"];
			$sid = str_replace('{site}',SID_PREFIX,$sid);
			$sid = str_replace('{incomingid}',$incomingId,$sid);
			$sid = str_replace('{outgoingid}',$outgoingId,$sid);
			if($currserverid)
			{
				$sid = str_replace('{serverid}',$serverid, $sid);
				$sid = str_replace('{currserverid}',$currserverid,$sid);
			}
			else
			{
				//for no couponsnapshot sites
				$sid = preg_replace("/[^\\}]+\\{serverid\\}/","",$sid);
				$sid = preg_replace("/[^\\}]+\\{currserverid\\}/","",$sid);
			}
			
			$aff_url_info["SubId"] = $sid;
			
			$dstUrl = $this->addSubIdToUrl($dstUrl,$aff_url_info);
		}

		return $dstUrl;
	}
	
	function addSubIdToUrl($url,$aff_url_info)
	{
		$url = trim($url);
		$sid = $aff_url_info["SubId"];
		
		if($aff_url_info["AffId"] == 62) //Commission Monster
		{
			//http://members.commissionmonster.com/z/81936/16925/OptionalInfo/http%3a%2f%2fwww.google.com"
			if(preg_match("|^(.*/z/[0-9]+/[0-9]+)(.*)|",$url,$matches))
			{
				//ok it's a good url
				$url_part_path = $matches[1];
				$url_part_query = $matches[2];
				$url_part_query = ltrim($url_part_query,"/");
				$arr_subid_deepurl = explode("/",$url_part_query);
				$url = $url_part_path . "/" . $sid;
				//try to add deep url
				foreach($arr_subid_deepurl as $para)
				{
					if(strtolower(substr($para,0,4)) == "http")
					{
						$url .= "/" . $para;
						break;
					}
				}
			}
			return $url;
		}
		else if(strpos($sid,"=") === false)
		{
			//something wrong here
			return $url;
		}
	
		$url_mark = "?";
		$url_mark_real = "?";
		list($url_part_path,$url_part_query) = explode($url_mark_real,$url,2);
		if(!isset($url_part_query) || empty($url_part_query))
		{
			//try use & to split
			$url_mark_real = "&";
			list($url_part_path,$url_part_query) = explode($url_mark_real,$url,2);
			if(!isset($url_part_query) || empty($url_part_query))
			{
				$url_part_query = "";
			}
			else
			{
				//it's strang,some websites (like onenetworkdirect,couponsnapshot ^_^) use & to join the path and query string
				$url_mark = "&";
			}
		}
		
		if(!$url_part_query && $aff_url_info["AffId"] == 30)
		{
			//onenetworkdirect
			$url_mark = "&";
		}
		
		if(strpos($url_part_query,"=") === false)
		{
			$url_part_path .= $url_mark_real . $url_part_query;
			$url_part_query = "";
			$arr_para = array();
		}
		else
		{
			@parse_str($url_part_query,$arr_para);
		}
		
		//occur an error??
		if(!is_array($arr_para)) $arr_para = array();

		//for match the key
		$arr_para_fix = array();
		foreach($arr_para as $k => $v)
		{
			$arr_para_fix[strtolower($k)] = $k;
		}
		
		list($subAffIDGetVarName,$subAffIDGetVarValue) = split("=",$sid);
		$arr_para[$subAffIDGetVarName] = $subAffIDGetVarValue;
		
		/*
			for some stupid affiliates!!!,
			for these affiliates , deep url must be the last para of the url,
			we just remove the deep url first and append it again at last
		*/
		$deepurl = "";
		$strDeepUrlParaName = $this->getAffDeepUrlParaNameById($aff_url_info["AffId"]);
		if($strDeepUrlParaName)
		{
			$strDeepUrlParaNameLower = strtolower($strDeepUrlParaName);
			if(isset($arr_para_fix[$strDeepUrlParaNameLower]))
			{
				$real_key = $arr_para_fix[$strDeepUrlParaNameLower];
				$deepurl = $arr_para[$real_key];
				unset($arr_para[$real_key]);
				$arr_para[$strDeepUrlParaName] = $deepurl;
			}
		}
		
		$url = $url_part_path . $url_mark . http_build_query($arr_para);
		return $url;
	}//end addSubIdToUrl
	
	function getMerIDinAffByMerchantId($merchantId, $affId = 2) {
		$merchantId = intval($merchantId);
		$affId = intval($affId);
		if ( ($merchantId == 0) || ($affId == 0) ) {
			return array();
		}
		$sql = "SELECT MerIDinAff FROM `wf_mer_in_aff` WHERE AffID = $affId AND MerID = $merchantId";
		return $this->objMysql->getFirstRowColumn($sql);;
	}
}
?>