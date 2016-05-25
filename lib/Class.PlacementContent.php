<?php
/*
 * FileName: Class.NormalMerchant.mod.php
 * Author: t
 * Create Date: 2012-03-30
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_PLACEMENT__"))
{
   define("__MOD_CLASS_PLACEMENT__",1);
   
   class Placement
   {
   		public $objMysql;
   
   		function Placement($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function insertPlacementSeasonalSale($dataArr){
   			foreach ($dataArr as $data){
   				try{
	   				if(isset($data["check"])){
	   					$sql = "delete from placement_data where ID = '" . $data["check"] . "'";
	   					$this->objMysql->query($sql);
	   				}else{
	   					if($data["oldflag"] != ""){
	   						$sql = "delete from placement_data where ID = '" . $data["oldflag"] . "'";
	   						$this->objMysql->query($sql);
	   					}
			   			$sql = "insert into placement_data(Placement,Page,Content, ContentLink, ContentOrder, StartTime, EndTime)values(";
			   			$sql .= "'". addslashes($data["placementTagType"]) . "'";
			   			$sql .= ",'". addslashes($data["pageName"]) . "'";
//			   			$sql .= ",'". addslashes($data["objectType"]) . "'";
			   			$sql .= ",'". addslashes($data["Keyword"]) . "'";
//			   			$sql .= ",'". addslashes("url") . "'";
			   			$sql .= ",'". addslashes($data["url"]) . "'";
//			   			$sql .= ",'". addslashes($data["newwindow"]) . "'";
			   			$sql .= ",'". addslashes($data["order"]) . "'";
	   					if(trim($data["startdate"]) == ""){
			   				$sql .= ",'". date("Y-m-d") . "'";
			   			}else{
			   				$sql .= ",'". addslashes($data["startdate"]) . "'";
			   			}
			   			$sql .= ",'". addslashes($data["expiredate"]) . "')";
//			   			echo $sql;
			   			$this->objMysql->query($sql);
	   				}
   				}catch(Exception $e){
   					return false;
   				}
   			}
   		}
   		
  		function insertPlacementHomepageFeaturedStores($dataArr){
   			foreach ($dataArr as $data){
   				try{
	   				if(isset($data["check"])){
	   					$sql = "delete from placement_data where ID = '" . $data["check"] . "'";
	   					$this->objMysql->query($sql);
	   				}else{
	   					if($data["oldflag"] != ""){
	   						$sql = "delete from placement_data where ID = '" . $data["oldflag"] . "'";
	   						$this->objMysql->query($sql);
	   					}
			   			$sql = "insert into placement_data( Placement, Page,  Content, ContentOrder, StartTime, EndTime)values(";
			   			//$sql .= "'". "placement" . "'";
			   			$sql .= "'". addslashes($data["placementTagType"]) . "'";
			   			$sql .= ",'". addslashes($data["pageName"]) . "'";
			   			//$sql .= ",'". addslashes($data["objectType"]) . "'";
			   			//$sql .= ",'". addslashes("merchant") . "'";
			   			$sql .= ",'". addslashes($data["merchantid"]) . "'";
			   			$sql .= ",'". addslashes($data["order"]) . "'";
	   					if(trim($data["startdate"]) == ""){
			   				$sql .= ",'". date("Y-m-d") . "'";
			   			}else{
			   				$sql .= ",'". addslashes($data["startdate"]) . "'";
			   			}
			   			$sql .= ",'". addslashes($data["expiredate"]) . "')";
			   			$this->objMysql->query($sql);
	   				}
   				}catch(Exception $e){
   					return false;
   				}
   			}
   		}
   		
  		function insertPlacementSiteWideBackfillTerm($dataArr){
   			foreach ($dataArr as $data){
   				try{
	   				if(isset($data["check"])){
	   					$sql = "delete from placement_data where ID = '" . $data["check"] . "'";
	   					$this->objMysql->query($sql);
	   				}else{
	   					if($data["oldflag"] != ""){
	   						$sql = "delete from placement_data where ID = '" . $data["oldflag"] . "'";
	   						$this->objMysql->query($sql);
	   					}
			   			$sql = "insert into placement_data( Placement, Page,  ContentType, Content, ContentOrder, StartTime, EndTime)values(";
			   			//$sql .= "'". "placement" . "'";
			   			$sql .= "'". addslashes($data["placementTagType"]) . "'";
			   			$sql .= ",'". addslashes($data["pageName"]) . "'";
			   			$sql .= ",'term'";
			   			//$sql .= ",'". addslashes("merchant") . "'";
			   			$sql .= ",'". addslashes($data["merchantid"]) . "'";
			   			$sql .= ",'". addslashes($data["order"]) . "'";
	   					if(trim($data["startdate"]) == ""){
			   				$sql .= ",'". date("Y-m-d") . "'";
			   			}else{
			   				$sql .= ",'". addslashes($data["startdate"]) . "'";
			   			}
			   			$sql .= ",'". addslashes($data["expiredate"]) . "')";
			   			$this->objMysql->query($sql);
	   				}
   				}catch(Exception $e){
   					return false;
   				}
   			}
   		}
   		
  		function insertPlacementHomepageRecommendsTerm($dataArr){
   			foreach ($dataArr as $data){
   				try{
	   				if(isset($data["check"])){
	   					$sql = "delete from placement_data where ID = '" . $data["check"] . "'";
	   					$this->objMysql->query($sql);
	   				}else{
	   					if($data["oldflag"] != ""){
	   						$sql = "delete from placement_data where ID = '" . $data["oldflag"] . "'";
	   						$this->objMysql->query($sql);
	   					}
			   			$sql = "insert into placement_data( Placement, Page, ContentType, Content, ContentOrder, SubContentAmount,StartTime, EndTime)values(";
			   			//$sql .= "'". "placement" . "'";
			   			$sql .= "'". addslashes($data["placementTagType"]) . "'";
			   			$sql .= ",'". addslashes($data["pageName"]) . "'";
			   			$sql .= ",'". addslashes($data["contentType"]) . "'";
			   			//$sql .= ",'". addslashes("merchant") . "'";
			   			$sql .= ",'". addslashes($data["merchantid"]) . "'";
			   			$sql .= ",'". addslashes($data["order"]) . "'";
			   			$sql .= ",'". addslashes($data["subContentAmount"]) . "'";
//			   			$sql .= ",'". addslashes($data["subContent"]) . "'";
	   					if(trim($data["startdate"]) == ""){
			   				$sql .= ",'". date("Y-m-d") . "'";
			   			}else{
			   				$sql .= ",'". addslashes($data["startdate"]) . "'";
			   			}
			   			$sql .= ",'". addslashes($data["expiredate"]) . "')";
			   			$this->objMysql->query($sql);
	   				}
   				}catch(Exception $e){
   					return false;
   				}
   			}
   		}
   		
  		function insertPlacementHomepageRecommendsCoupon($dataArr){
   			foreach ($dataArr as $data){
   				try{
	   				if(isset($data["check"])){
	   					$sql = "delete from placement_data where ID = '" . $data["check"] . "'";
	   					$this->objMysql->query($sql);
	   				}else{
	   					if($data["oldflag"] != ""){
	   						$sql = "delete from placement_data where ID = '" . $data["oldflag"] . "'";
	   						$this->objMysql->query($sql);
	   					}
			   			$sql = "insert into placement_data( Placement, Page, ContentType, Content, ContentOrder, StartTime, EndTime)values(";
			   			//$sql .= "'". "placement" . "'";
			   			$sql .= "'". addslashes($data["placementTagType"]) . "'";
			   			$sql .= ",'". addslashes($data["pageName"]) . "'";
			   			$sql .= ",'". addslashes($data["contentType"]) . "'";
			   			//$sql .= ",'". addslashes("merchant") . "'";
			   			$sql .= ",'". addslashes($data["merchantid"]) . "'";
			   			$sql .= ",'". addslashes($data["order"]) . "'";
//			   			$sql .= ",'". addslashes($data["subContentAmount"]) . "'";
//			   			$sql .= ",'". addslashes($data["subContent"]) . "'";
	   					if(trim($data["startdate"]) == ""){
			   				$sql .= ",'". date("Y-m-d") . "'";
			   			}else{
			   				$sql .= ",'". addslashes($data["startdate"]) . "'";
			   			}
			   			$sql .= ",'". addslashes($data["expiredate"]) . "')";
			   			$this->objMysql->query($sql);
	   				}
   				}catch(Exception $e){
   					return false;
   				}
   			}
   		}
   		
   		function getPlacementConfigByPage($page){
   			$sql = "SELECT * FROM placement_config WHERE Page = '$page'";
   			$res = array();
   			$rows = $this->objMysql->getRows($sql);
   			foreach ($rows as $v){
   				$res[$v["Placement"]] = $v["PlacementShow"];
   			}
   			return $res;
   		}
   }
}
?>
