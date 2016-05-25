<?php
if (!defined("__CLASS_MERCHANTSTORE__")){
	
	define("__CLASS_MERCHANTSTORE__", 1);
	
	class MerchantStore {
		public $objMysql;
		function __construct($objMysql = null) {
			if ($objMysql){
				$this->objMysql = $objMysql;
			}
			else{
				$this->objMysql = new Mysql ("task",TASK_DB_HOST,TASK_DB_USER,TASK_DB_PASS);
			}
		}
		function getMerchantStoreIds($merchantIds, $site){
			$sql = "";
			if(!is_array($merchantIds)){
				$sql = "SELECT StoreID, MerchantID FROM store_merchant_relationship WHERE MerchantID = '$merchantIds' and SiteName = '$site'";
			}else{
				$inStr = implode("','", $merchantIds);
				$sql = "SELECT StoreID, MerchantID FROM store_merchant_relationship WHERE MerchantID in ('$inStr') and SiteName = '$site'";
			}
			$rows = $this->objMysql->getRows($sql, "MerchantID");
			return $rows;
		}
		function getMerchantCompetitorByMids($midArr, $site){
			$site = strtoupper($site);
			if(count($midArr) == 0){
				return array();
			}
			$sql = "SELECT a.StoreID, a.MerchantID, b.Url, c.Name FROM `store_merchant_relationship` a, `store_competitor_relationship` b, competitor c  
					WHERE a.StoreID = b.StoreID and  b.CompetitorId = c.ID
						AND SiteName = '$site'
						AND a.MerchantID IN  ('". implode("','", $midArr) ."')
						and b.Purpose <> 'ForDeal'";
			$rows = $this->objMysql->getRows($sql);
			if(count($rows) == 0){
				return array();
			}
			$merchantCompetitors = array();
			foreach ($rows as $value){
				$domainArr = parse_url($value["Url"]);
				$tmp = array("Url" => $value["Url"], 
							"url_encode" => urlencode($value["Url"]),
							"domain" => $domainArr["host"],
							"name" => $value["Name"],
						);
				$merchantCompetitors[$value["MerchantID"]][] = $tmp;
			}
			return $merchantCompetitors;
			
		}
		
		function getStoreInfoByMerchantIds($midArr, $site){
			$sql =" select a.MerchantID, b.CouponTitle, b.Domain, b.Url, b.TwitterUrl, b.FacebookUrl, b.BlogUrl, b.SpecialOfferUrl
				FROM
				  `store_merchant_relationship` a,
				  `store` b 
				WHERE a.StoreID = b.ID 
				  AND a.MerchantID IN ('". implode("','", $midArr) ."') 
				  AND SiteName = '$site' ";
			
			return $this->objMysql->getRows($sql, "MerchantID");
		}
		
		

	}
}
?>
