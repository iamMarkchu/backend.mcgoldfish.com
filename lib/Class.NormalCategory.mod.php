<?php
/*
 * FileName: Class.NormalCategory.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_NORMAL_CATEGORY__"))
{
   define("__MOD_CLASS_NORMAL_CATEGORY__",1);
   
   class NormalCategory
   {
   		var $objMysql;
   		
   		function NormalCategory($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getCategoryCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from normalcategory $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
   		function getCategoryListByLimitStr($limitStr="", $whereStr="", $orderStr="")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Name, UrlName, Navigation from normalcategory $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$arr[$i]['urlname'] = trim($arrTmp['UrlName']);
   				$arr[$i]['navigation'] = trim($arrTmp['Navigation']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
		function getCategoryById($categoryId)
		{
			$categoryId = intval($categoryId);
   			$arr = $this->getCategoryListByLimitStr("", " ID = $categoryId");
			return $arr[0];
		}
		
		function getCategoryNewById($categoryId)
		{
			$categoryId = intval($categoryId);
   			$arr = $this->getCategoryNewListByLimitStr("", " ID = $categoryId");
			return $arr[0];
		}
		

		function getCategoryNewListByLimitStr($limitStr="", $whereStr="", $orderStr="",$keycoloumindex="")
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select * from normalcategory_new $whereStr $orderStr $limitStr";
			$arr = $this->objMysql->getRows($sql,$keycoloumindex);
			return $arr;
		}
		
		function getCategoryTreeByCountry($countryName) {
			if (empty( $countryName )) {
				return array();
			}
			$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = 0 AND Country = '$countryName' ORDER BY `Name`";
			$topCategories = $this->objMysql->getRows($sql,"ID");
			foreach ($topCategories as $ktc => $vtc) {
				$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = $ktc AND Country = '$countryName' ORDER BY `Name`";
				$subCategories = $this->objMysql->getRows($sql,"ID");
				foreach ($subCategories as $ksc => $vsc) {
					$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = $ksc AND Country = '$countryName' ORDER BY `Name`";
					$subSubCategories = $this->objMysql->getRows($sql,"ID");;
					$subCategories[$ksc]["SubCategories"] = $subSubCategories;
				}
				$topCategories[$ktc]["SubCategories"] = $subCategories;
			}
			return $topCategories;
		}
		
		function getParentCategoryId($id) {
			$sql = "SELECT ParentCategoryId FROM `normalcategory_new` WHERE ID = $id";
			$res = $this->objMysql->getFirstRowColumn($sql);
			return $res;
		}
		
		function getCategoryNewParentTreeById($categoryId) {
			static $treeArr;
			static $count = 0;
			$parentCategoryId = $this->getParentCategoryId($categoryId);
			$parentCategoryInfo = $this->getCategoryNewById($parentCategoryId);
			$treeArr[] = $parentCategoryInfo;
			$count++;
			if ($parentCategoryInfo["ParentCategoryId"] == 0 ) {
				return $treeArr;//如果第一次递归就是ParentCategoryId = 0，那么直接返回 treearr；这里返回这个结果只为第一次
			}else{
				$this->getCategoryNewParentTreeById($parentCategoryId);
			}
			return $treeArr;
		}
		
		function getChildrenCategoryNewIdsById(array $categoryIds) {
			if (is_array($categoryIds) && !empty($categoryIds)) {
				$sql = "SELECT ID FROM normalcategory_new WHERE ParentCategoryId IN (".implode(',', $categoryIds).")";
				$res=$this->objMysql->query($sql);
				$data=array();
				while($row=$this->objMysql->getRow($res)){
					$data[] = $row["ID"];
				}
				$this->objMysql->freeResult($res);
				if (!empty($data)) {
					$data = array_merge($data,$this->getChildrenCategoryNewIdsById($data));
					return $data;
				}else {
					return array();
				}
			}else{
				return array();
			};
		}
		
		function updateCategoryNewR_TermCount($cid, $termCount, $needMemFresh) {
			if (!$termCount) {
				$termCount = 0;
			}
			$sql = "UPDATE normalcategory_new SET r_TermCount = $termCount,NeedMemRefresh='$needMemFresh' WHERE ID = $cid";
			$this->objMysql->query($sql);
		}
		
		function updateCategoryNewNeedMemFresh($cid, $needMemFresh) {
			$sql = "UPDATE normalcategory_new SET r_TermCount = r_TermCount + 1,NeedMemRefresh='$needMemFresh' WHERE ID = $cid";
			$this->objMysql->query($sql);;
		}
		
		function setupNewCategoryCountry($country){
			$sql = 'SELECT * FROM normalcategory_new WHERE Country = "'.$country.'"';
			$row = $this->objMysql->getRows($sql);
			
			if(!empty($row)){
				return false;
			}
			
			$sql = 'SELECT ID,`Name`,Navigation,UrlName,ParentCategoryId FROM normalcategory_new WHERE Country = "CN"';
			$row = $this->objMysql->getRows($sql);
			
			$category_tree = array();
			$i = 0;
			$tmp = array();
			foreach($row as $k=>$v){
				$tmp[$v['ID']] = $v;
			}
			
			$map = array();
			foreach($tmp as $k=>$v){
				if(isset($map[$v['ID']]) || !empty($map[$v['ID']]))continue;
				if($v['ParentCategoryId'] == '0'){
					$sql = 'INSERT INTO normalcategory_new SET 
							`Name` = "'.addslashes($v['Name']).'",
							`Navigation` = "'.addslashes($v['Navigation']).'",
							`UrlName` = "'.addslashes($v['UrlName']).'",
							`ParentCategoryId` = 0,
							`LastChangeTime` = "'.date('Y-m-d H:i:s').'",
							`Country` = "DE",
							`r_TermCount` = 0,
							`NeedMemRefresh` = "NO"';
					$this->objMysql->query($sql);
					$id = $this->objMysql->getLastInsertId();
					$map[$k] = $id;
				}else{
					$parentSysId = $this->getParentSysId($v['ParentCategoryId'],$map,$tmp);
					if(!$parentSysId){
						continue;
					}
					$sql = 'INSERT INTO normalcategory_new SET
							`Name` = "'.addslashes($v['Name']).'",
							`Navigation` = "'.addslashes($v['Navigation']).'",
							`UrlName` = "'.addslashes($v['UrlName']).'",
							`LastChangeTime` = "'.date('Y-m-d H:i:s').'",
							`Country` = "DE",
							`r_TermCount` = 0,
							`ParentCategoryId` = '.$parentSysId.',
							`NeedMemRefresh` = "NO"';
					$this->objMysql->query($sql);
					$id = $this->objMysql->getLastInsertId();
					$map[$k] = $id;
				}
			}
			
		}
		
		function getParentSysId($ParentCategoryId,&$map,$tmp){
			if(isset($map[$ParentCategoryId]) && $map[$ParentCategoryId]){
				return $map[$ParentCategoryId];
			}else{
				if(isset($tmp[$ParentCategoryId]) && $tmp[$ParentCategoryId]){
					$data = $tmp[$ParentCategoryId];
				}else{
					return false;
				}
				
				if($data['ParentCategoryId'] == '0'){
					$sql = 'INSERT INTO normalcategory_new SET
							`Name` = "'.addslashes($data['Name']).'",
							`Navigation` = "'.addslashes($data['Navigation']).'",
							`UrlName` = "'.addslashes($data['UrlName']).'",
							`ParentCategoryId` = 0,
							`LastChangeTime` = "'.date('Y-m-d H:i:s').'",
							`Country` = "DE",
							`r_TermCount` = 0,
							`NeedMemRefresh` = "NO"';
					$this->objMysql->query($sql);
					$id = $this->objMysql->getLastInsertId();
					$map[$data['ID']] = $id;
					return $id;
				}else{
					//$parentSysId = $this->getParentSysId($data['ParentCategoryId'],&$map,$tmp);
					$parentSysId = $this->getParentSysId($data['ParentCategoryId'],$map,$tmp); //aaron 2014-05-22
					if(!$parentSysId){
						return false;
					}
					$sql = 'INSERT INTO normalcategory_new SET
							`Name` = "'.addslashes($data['Name']).'",
							`Navigation` = "'.addslashes($data['Navigation']).'",
							`UrlName` = "'.addslashes($data['UrlName']).'",
							`LastChangeTime` = "'.date('Y-m-d H:i:s').'",
							`Country` = "DE",
							`r_TermCount` = 0,
							`ParentCategoryId` = '.$parentSysId.',
							`NeedMemRefresh` = "NO"';
					$this->objMysql->query($sql);
					$id = $this->objMysql->getLastInsertId();
					$map[$data['ID']] = $id;
					return $id;
				}
			}
		}
		
		//aaron category manager 2014-05-22
		function getCountrysByCate(){
			$sql = 'SELECT Country FROM normalcategory_new GROUP BY Country';
			$res = $this->objMysql->query($sql);
			$data=array();
			while($row=$this->objMysql->getRow($res)){
				$data[] = $row["Country"];
			}
			return $data;
		}
		function getCategoryTreeByCountryAndTerms($countryName) {
			if (empty( $countryName )) {
				return array();
			}
			$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = 0 AND Country = '$countryName' ORDER BY `Name`";
			$topCategories = $this->objMysql->getRows($sql,"ID");
			
			foreach ($topCategories as $ktc => $vtc) {
				$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = $ktc AND Country = '$countryName' ORDER BY `Name`";
				$subCategories = $this->objMysql->getRows($sql,"ID");
				foreach ($subCategories as $ksc => $vsc) {
					$sql = "SELECT * FROM `normalcategory_new` WHERE ParentCategoryId = $ksc AND Country = '$countryName' ORDER BY `Name`";
					$subSubCategories = $this->objMysql->getRows($sql,"ID");;
					$subCategories[$ksc]["SubCategories"] = $subSubCategories;
				}
				$topCategories[$ktc]["SubCategories"] = $subCategories;
			}
			return $topCategories;
		}
		function getCateInfoById($id){
			$sql = "SELECT * FROM `normalcategory_new` WHERE id = ".$id." limit 1";
			$res = $this->objMysql->getFirstRow($sql);
			return $res;
		}
		function updateinfo($data,$id){
			$sql = "UPDATE normalcategory_new SET Name = '".$data['name']."',UrlName='".$data['urlname']."',NeedMemRefresh='$needMemFresh' WHERE ID = $id";
			$this->objMysql->query($sql);
		}
		
		
	
   }
   
}
?>
