<?php
/*
 * FileName: Class.Stats.mod.php
 * Author: Lee
 * Create Date: 2006-11-2
 * Package: package_name
 * Project: se
 * Remark: 
*/
if (!defined("__CLASS_STATS_MOD__"))
{
	define("__CLASS_STATS_MOD__", 1);
	
	include_once(INCLUDE_ROOT."lib/Class.ip2c.php");
	
	class Stats
	{
		private $objStatsDB;
		
		public function __Construct()
		{
			$this->objStatsDB = new Mysql(TRACKING_DB_NAME, TRACKING_DB_HOST, TRACKING_DB_USER, TRACKING_DB_PASS);
		}
		
		public function setIncomingLog()
		{
			
			global $g_sessionID;
			
			$ip = addslashes(my_trim($this->getIp()));
			$referer = addslashes(my_trim($this->getReferer()));
			$userAgent = addslashes(my_trim($this->getUserAgent()));
			$requestUri = addslashes(my_trim($this->getRequestUri()));
			$trafficType = intval(my_trim($this->getTrafficType()));
			$retentionUid = addslashes(my_trim($this->getRetentionUserID()));
			$src = addslashes(my_trim($this->getSource()));
			$srcGroup = addslashes(my_trim($this->getSourceGroup()));
			$refKeyword = addslashes(my_trim($this->getRefKeyword($referer)));
				

			if(isset($g_sessionID))
			{
				return $g_sessionID;
			}
			elseif(isset($_COOKIE['U_ID']))
			{
				$g_sessionID = intval($_COOKIE['U_ID']);
				return $g_sessionID;
			}
			else
			{
				if($trafficType == 0)
				{	
					$g_clientCountry = $this->getClientCountry($ip);
					$sql = "insert into incominglog (VisitTime, IP, HttpReferer, HttpUserAgent, RequestUri, ";
					$sql .= " TrafficType, RetentionUserID, Source, SourceGroup, Country, RefKeyword,ServerID,CurrServerID) values (";
					$sql .= "NOW(), '$ip', '$referer', '$userAgent', '$requestUri', $trafficType,";
					$sql .= " '$retentionUid', '$src', '$srcGroup', '".addslashes($g_clientCountry)."', '$refKeyword','".$this->getServerId()."','".$this->getCurrServerId()."')";
					$qryId = $this->objStatsDB->query($sql);
					$g_sessionID = $this->objStatsDB->getLastInsertId();
					$g_sessionID = intval($g_sessionID);
					setcookie("U_ID", $g_sessionID, 0, "/", SITE_DOMAIN);
					setcookie("U_S",$this->getServerId(), 0, "/", SITE_DOMAIN);
					setcookie("U_SRC", $src, 0, "/", SITE_DOMAIN);
					setcookie("U_CNTY", $g_clientCountry, 0, "/", SITE_DOMAIN);
					return $g_sessionID;
				}
				else
				{
					return 0;//incoming id for spider taffic
				}
			}
		}

		
		public function setPageVisitLog()
		{
			global $g_sessionID;
			global $g_trafficType;
			if($g_trafficType == 0)
			{
				$sql = "insert into pagevisitlog (SessionID, Referer, RequestUri, VisitTime,ServerID,CurrServerID)";
				$sql .= " values(".intval($g_sessionID).", '".addslashes($this->getReferer());
				$sql .= "', '".addslashes($this->getRequestUri())."', NOW(),'".$this->getServerId()."','".$this->getCurrServerId()."')";
				$this->objStatsDB->query($sql);
			}
		}

		
		public function setWidgetLog($CustomerKey){ //$CustomerKey = 05d8507b85e58a7e-23-2
			global $g_sessionID;
			global $g_trafficType;
			if($g_trafficType == 0 && !empty($CustomerKey) )
			{
				$sql = " insert into couponwidgetlog (`CustomerKey`, `Referer`,`RequestUri`, `StartTime`, `StartDateTime`,`SessionId`,`IpAddress`,ServerID,CurrServerID) ";
				$sql .= " values('".addslashes($CustomerKey)."','".addslashes($this->getReferer())."','".addslashes($this->getRequestUri())."','".time()."','".date("Y-m-d H:i:s",time())."','".(int)$g_sessionID."','".addslashes($this->getIp())."','".$this->getServerId()."','".$this->getCurrServerId()."') ";
				$this->objStatsDB->query($sql);
			}
		}
		
		public function setOutgoingLog($affid, $destUrl, $merchantID, $couponID, $ca='')
		{
			global $g_sessionID;
			global $g_trafficType;
			$merchantID = intval($merchantID);
			$couponID = intval($couponID);
	
			if($g_trafficType == 0)
			{
				$sql = "insert into outgoinglog (SessionID, VisitTime, MerchantID, CouponId, AffiliateID, DestUrl, IsValid, ClickArea,ServerID,CurrServerID)";
				$sql .= " values(".intval($g_sessionID).", NOW(), $merchantID, $couponID, ".intval($affid).", '".addslashes($destUrl)."', ".intval($g_trafficType).", '".addslashes($ca)."','".$this->getServerId()."','".$this->getCurrServerId()."')";
				$this->objStatsDB->query($sql);
				return $this->objStatsDB->getLastInsertId();
			}
			else
			{
				return 0; //outgoing id for spider traffic
			}
		}
		
		public function setSearchLog($kw, $costTime, $searchType, $pageNum, $totalResNum, $isCache)
		{
			global $g_sessionID;
			global $g_source;
			global $g_trafficType;
			$isMktSearch = $g_source ? 'YES' : 'NO';
			$pageNum = intval($pageNum);
			$totalResNum = intval($totalResNum);
			$isCache = intval($isCache) ? 'YES' : 'NO';
			if($g_trafficType == 0)
			{
				$sql = "insert into searchlog (SessionID, VisitTime, Keyword, IsMktSearch, CostTime, TrafficType, SearchType, PageNum, TotalResNum, isCache,ServerID,CurrServerID)";
				$sql .= " values(".intval($g_sessionID).", NOW(), '".addslashes($kw)."', '$isMktSearch', $costTime, ".intval($g_trafficType).", '";
				$sql .= addslashes($searchType)."', $pageNum, $totalResNum, '$isCache','".$this->getServerId()."','".$this->getCurrServerId()."')";
				$this->objStatsDB->query($sql);
			}
		}

		public function setEmailLog($email,$Status,$Type,$Subject,$Header,$Content,$Key)
		{
			global $g_sessionID,$g_trafficType;

			if($g_trafficType == 0)
			{
				$sql = " INSERT INTO emaillog (`SessionID`,`email`,`Status`,`Type`,`Subject`,`Header`,`Content`,`SendDate`,`Key`,ServerID,CurrServerID) ";
				$sql .=" VALUES('".intval($g_sessionID)."','".addslashes($email)."','".addslashes($Status)."','".
				addslashes($Type)."','".addslashes($Subject)."','".addslashes($Header)."','".addslashes($Content)."','"
				.date("Y-m-d H:i:s")."','".addslashes($Key)."','".$this->getServerId()."','".$this->getCurrServerId()."')";
				$this->objStatsDB->query($sql);
				return $this->objStatsDB->getLastInsertId();
			}
			return false;
		}

		public function setEmailAsViewed($uid)
		{
			if($uid)
			{
				$sql = "update emaillog set `Status` = 'Viewed', ViewCnt = ViewCnt + 1, LastViewTime = Now() where `Key` = '". addslashes($uid)."'";
				$this->objStatsDB->query($sql);
			}
			return;
		}

		public function getIp()
		{
			if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"]) return $_SERVER["HTTP_X_FORWARDED_FOR"];
			return $_SERVER['REMOTE_ADDR'];
//			return "221.220.10.121"; // for test
		}
		
		public function getReferer()
		{
			if(in_array('HTTP_REFERER', array_keys($_SERVER)))
			{
				return $_SERVER['HTTP_REFERER'];
			}
			return '';
		}
		
		public function getUserAgent()
		{
			if(in_array('HTTP_USER_AGENT', array_keys($_SERVER)))
			{
				return $_SERVER['HTTP_USER_AGENT'];
			}
			return '';
		}
		
		public function getRequestUri()
		{
			if(in_array('REQUEST_URI', array_keys($_SERVER)))
			{
				return urldecode($_SERVER['REQUEST_URI']);
			}
			return '';
		}
		
		public function getTrafficType()
		{
			global $g_trafficType;

			if(isset($g_trafficType))
			{
				return $g_trafficType;
			}
			else if (in_array('TRAFFIC_TYPE', array_keys($_COOKIE)))
			{
				$g_trafficType = $_COOKIE['TRAFFIC_TYPE'];
				return $g_trafficType;
			}
			else
			{
				$trafficType = 0;
				if($trafficType == 0)
				{
					if(stripos($this->getUserAgent(), 'googlebot') !== false)
					{
						$trafficType = -1; //google bot
					}
				}
				
				if($trafficType == 0)
				{
					if(stripos($this->getUserAgent(), 'msnbot') !== false)
					{
						$trafficType = -2; //msn bot
					}
				}
				
				if($trafficType == 0)
				{
					if(stripos($this->getUserAgent(), 'Yahoo! Slurp;') !== false)
					{
						$trafficType = -3; //yahoo bot
					}
				}
				
				if($trafficType == 0)
				{
					if(stripos($this->getUserAgent(), 'Baiduspider') !== false)
					{
						$trafficType = -4; //baidu bot
					}
				}
				
				if($trafficType == 0)
				{
					foreach(getRobotsList() as $v)
					{
						if(stripos($this->getUserAgent(), $v) !== false)
						{
							$trafficType = -5; //other robots
							break;
						}
					}
				}

				if($trafficType == 0)
				{
					foreach(getFraudIPList() as $v)
					{
						if($this->getIp() == $v)
						{
							$trafficType = -6; //black IP
							break;
						}
					}
				}
				
				if($trafficType == 0)
				{
					if($this->isPrivateIP($this->getIp()))
					{
						$trafficType = -7; // private ip
					}
				}
				
				if($trafficType == 0)
				{
					if(trim($this->getUserAgent()) == '')
					{
						$trafficType = -8; // empty useragent
					}
				}

				$g_trafficType = $trafficType;
				setcookie("TRAFFIC_TYPE", $g_trafficType, 0, "/", SITE_DOMAIN);
				return $g_trafficType;
			}
		}
		
		public function getRetentionUserID()
		{
			global $g_retentionID;
			if(isset($g_retentionID))
			{
				return $g_retentionID;
			}
			else if(in_array('RETENTION_U_ID', array_keys($_COOKIE)))
			{
				$g_retentionID = trim($_COOKIE['RETENTION_U_ID']);
				return $g_retentionID;
			}
			else
			{
				$g_retentionID = $this->getUniqID();
				setcookie("RETENTION_U_ID", $g_retentionID, time()+3600*24*365*5, "/", SITE_DOMAIN);
				return $g_retentionID;
				
			}
		}
		
		public function getSource()
		{
			global $g_source;
			if(isset($g_source))
			{
				return $g_source;
			}
			else if(in_array('SOURCE', array_keys($_COOKIE)))
			{
				$g_source = trim($_COOKIE['SOURCE']);
				return $g_source;
			}
			else
			{
				$tmpUrl = rtrim(strtolower($this->getRequestUri()));
				if(strpos($tmpUrl, "mktsrc=") === false)
				{
					$source = "";
				}
				else
				{
					$pos = strpos($tmpUrl, "mktsrc=");
					$tmpUrl = substr($tmpUrl, $pos);
					if((strpos($tmpUrl, "&") !== false))
					{
						$position = strpos($tmpUrl, "&");	
						$source = substr($tmpUrl, 7, $position-7);
						if(!$source)
						{
							$source = "none";
						}
					}
					else
					{
						$source = substr($tmpUrl, 7);
						if(!$source)
						{
							$source = "none";
						}
					}
				}
				$arrTmp = explode("?", $source);
				$g_source = $arrTmp[0];
				setcookie("SOURCE", $g_source, 0, "/", SITE_DOMAIN);
				return $g_source;
			}
		}
		
		function getServerId()
		{
			global $g_serverId;
			if(isset($g_serverId)) return $g_serverId;
			if(isset($_COOKIE["U_S"])) return $_COOKIE["U_S"];
			return $this->getCurrServerId();
		}
		
		function getCurrServerId()
		{
			$server_name = php_uname("n");
			list($short_server_name) = explode(".",$server_name);
			if(preg_match("/(web|admin|backup)([0-9]+)/",$short_server_name,$matches))
			{
				$g = $matches[1];
				$s = $matches[2];
				if($g == "web") $short_server_name = intval($s);
				elseif($g == "admin") $short_server_name = intval($s) + 100;
				elseif($g == "backup") $short_server_name = intval($s) + 200;
			}
			return $short_server_name;
		}
	
		public function getSourceGroup()
		{
			global $g_sourceGroup;
			if(isset($g_sourceGroup))
			{
				return $g_sourceGroup;
			}
			else if(in_array('SOURCE_GROUP', array_keys($_COOKIE)))
			{
				$g_sourceGroup = trim($_COOKIE['SOURCE_GROUP']);
				return $g_sourceGroup;
			}
			else
			{
				$source = $this->getSource();
				if(!$source)
				{
					$sourcegroup = "";
				}
				else
				{
					$tmpArr = explode("_", $source);
					$sourcegroup = $tmpArr[0];
				}
				$g_sourceGroup = $sourcegroup;
				setcookie("SOURCE_GROUP", $g_sourceGroup, 0, "/", SITE_DOMAIN);
				return $g_sourceGroup;
			}
		}
		
		public function getClientCountry($ip="")
		{
			global $g_clientCountry;
			if(isset($g_clientCountry))
			{
				return $g_clientCountry;
			}
			else if(in_array('U_CNTY', array_keys($_COOKIE)))
			{
				$g_clientCountry = trim($_COOKIE['U_CNTY']);
				return $g_clientCountry;
			}
			else
			{
				$objIp2c = new ip2country(INCLUDE_ROOT."data/ip-to-country.bin");
				$g_clientCountry = 'UNKNOWN';
				if(!$ip)
					$ip = $this->getIp();
				$arrIPRtn = $objIp2c->get_country($ip);
				if(isset($arrIPRtn['name']))
					$g_clientCountry = $arrIPRtn['name'];
				return $g_clientCountry;
			}
		}
		
		private function isPrivateIP($ip)
		{
			if(ip2long($ip) <> false && ip2long($ip) <> -1)
			{
				$arrIpSeg = explode(".", $ip);
				if(strcmp($ip, '127.0.0.1') == 0)
				{
					return true;
				}
				else if(strcmp($arrIpSeg[0], "10") == 0)	
				{
					return true; 
				}
				else if(strcmp($arrIpSeg[0], "192") == 0 && strcmp($arrIpSeg[1], "168") == 0)
				{
					return true;
				}
				else if(strcmp($arrIpSeg[0], "172") == 0 && intval($arrIpSeg[1]) >= 16 && intval($arrIpSeg[1]) <= 31)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			return false;
		}
		
		private function getUniqID()
		{
			return md5(uniqid(rand(), true));
		}

		public function getRefKeyword($referer)
		{
			$refkw = "";
			$referer = trim($referer);
			if(!$referer) return $refkw;

			$arrSEList = array('google'=>array('google.', '/([&?]+)q=([^&]*)/i'),
								'yahoo'=>array('yahoo.', '/([&?]+)p=([^&]*)/i'),
								'msn'=>array('msn.', '/([&?]+)q=([^&]*)/i'),
								'bing'=>array('bing.', '/([&?]+)q=([^&]*)/i'),
								'ask'=>array('.ask.', '/([&?]+)q=([^&]*)/i'),
								'aol'=>array('.aol.', '/([&?]+)q=([^&]*)/i'),
								'comcast'=>array('.comcast.', '/([&?]+)q=([^&]*)/i'),
								'live'=>array('live.', '/([&?]+)q=([^&]*)/i'));
			foreach($arrSEList as $k=>$v)
			{
				if(stripos($referer, $v[0]) !== false)
				{
					$arrMatches = array();
					if(preg_match($v[1], $referer, $arrMatches))
					{
						$refkw = $k."_".urldecode(trim($arrMatches[2]));
					}
					break;
				}
			}
			return $refkw;
		}

	}
}
?>
