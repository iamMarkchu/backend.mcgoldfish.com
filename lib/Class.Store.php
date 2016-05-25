<?php
class Store
{
	function __construct()
	{
		$this->objMysql = new Mysql(TASK_DB_NAME, TASK_DB_HOST, TASK_DB_USER, TASK_DB_PASS);
	}
	
	function getAllCategoryByTree() {
		$data = array();
		$sql = "select * from `store_category`";
		$dataSource = $this->objMysql->getRows($sql);
		
		foreach ($dataSource as $k => $v) {
			if ($v['ParentID'] == 0) {
				$data[$v['ID']]['ParentCate'] = array('ID' => $v['ID'], 'Name' => $v['Name']);
			} else {
				$data[$v['ParentID']]['ChildCate'][] = array('ID' => $v['ID'], 'Name' => $v['Name']);
			}
		}
		
		return $data;
	}
	
	function getAllCategorySort(){
		$sql = "select * from `store_category` where `ParentID`!=0";
		$tmp = $this->objMysql->getRows($sql);
		foreach ($tmp as $v) {
			$data[$v['ParentID']][] = $v['ID'];
		}
		
		return $data;
	}
	
	function getCategory($condition = '')
	{
		$data = array();
		$sql = "select * from `store_category` ";
		if (!empty($condition)) $sql .= "where {$condition} ";
		$dataTmp = $this->objMysql->getRows($sql);
		foreach ((array)$dataTmp as $v) {
			$data[$v['ID']] = $v['Name'];
		}
		
		return $data;
	}
	
	function getCategoryInfoByID($id) {
		$data = array();
		if (!is_numeric($id)) return $data;
		$sql = "select * from `store_category` where `ID`={$id}";
		
		$query = $this->objMysql->query($sql);
		$data = $this->objMysql->getRow($query);
		return $data;
	}
	
	function getStoreListByCondition($condition = array(), $fields = '*')
	{
		$data = array();
		if (empty($condition)) return $data;
		$sql = "select {$fields} from `store` ";
		
		if (!empty($condition['sql'])) $sql .= "where 1=1 {$condition['sql']} ";
		if (!empty($condition['order'])) $sql .= "order by {$condition['order']} ";
		if (!empty($condition['limit'])) $sql .= "limit {$condition['limit']} ";
		
		$data = $this->objMysql->getRows($sql);
		
		return $data;
	}
	
	function checkUrluique($url, $originalurl = '') {
		$res = 0;
		if (!empty($originalurl) && $url == $originalurl) return $res;
		
		$httpurl  = preg_replace("/https:\/\//is", 'http://', $url);
		$httpsurl = preg_replace("/http:\/\//is", 'https://', $url);
		
		if (substr($url, -1) == '/') {
			$urlno_  = substr($url, 0, -1);
			$urlyes_ = $url;
		} else {
			$urlno_  = $url;
			$urlyes_ = $url . '/';
		}
		$url_arr = array($httpurl, $httpsurl, $urlyes_, $urlno_);
		
		$i = 0;
		$status = false;
		do {
			$sql = "select count(*) as cnt from `store` where `Url`='{$url_arr[$i]}'";
			$query = $this->objMysql->query($sql);
			$row = $this->objMysql->getRow($query);
			if ($row['cnt'] > 0) $status = ($status OR true);
			else $status = ($status OR false);
			$i++;
		} while (!$status && $i < count($url_arr));
		
		return $status;
	}
	
	function checkDomain($domain = '') {
		$res = '';
		if (empty($domain)) return $res;
		$sql = "select `Url` from `store` where Domain='{$domain}'";
		$res = $this->objMysql->getRows($sql);
		
		return $res;
	}
	
	function tidyInsertData($data = array()) {
		if (isset($data['Url']) && !empty($data['Url'])) {
			$data['Url'] = preg_replace("/\s+/", "", trim($data['Url']));
		}
		if (isset($data['SupportedShippingCountry']) && !empty($data['SupportedShippingCountry'])) {
			$data['SupportedShippingCountry'] = implode(',', (array)$data['SupportedShippingCountry']);
		}
	    if (isset($data['Category']) && !empty($data['Category'])) {
	    	
	    	$allcateinfo = $this->getAllCategorySort();
	    	foreach ($data['Category'] as $k => $v) {
	    		foreach ($allcateinfo as $k1 => $v1) {
	    			if (isset($parent_id[$k1])) continue;
	    			if (in_array($v, $v1)) {
	    				$parent_id[$k1] = $k1;
	    				break;
	    			}
	    		}
	    	}
	    	
			$data['Category'] = implode(',', (array)$data['Category']) . "," . implode(',', (array)$parent_id);
		}
		if (isset($data['CouponTitle']) && $data['CouponTitle'] == 'Other') {
			$data['CouponTitle'] = $data['CouponTitleOther'];
		}
		unset($data['CouponTitleOther']);
		unset($data['action']);
		unset($data['ID']);
		
		return $data;
	}
	
	function insertSingleData($row) {
		$return = array('msg' => true);
		if (empty($row)) return $return;
		$storeRelationRow['MerchantID'] = $row['MerchantID'];
		
		$comment['Content'] = trim($row['Comment']);
		$comment['AddUser'] = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
		$comment['AddDate'] = date('Y-m-d H:i:s');
		
		unset($row['MerchantID']);
		unset($row['Comment']);
		
		$fields = '';
		$values = '';
		$storeRelFields = '';
		$storeRelValues = '';
		$commentFields = '';
		$commentValues = '';
		$commentRelFields = '';
		$commentRelValues = '';
		$sql = "insert into `store` ";
		$sqlStoreRelationship = "insert into `store_merchant_relationship` ";
		$sqlComment = "insert into `comment` ";
		$sqlCommentRelationship = "insert into `comment_relationship` ";
		
		foreach ($row as $k => $v) {
			$fields .= "`" . $k . "`, ";
			$values .= "'" . addslashes($v) . "', ";
		}
		$fields = preg_replace("|, $|i", '', $fields);
		$values = preg_replace("|, $|i", '', $values);
		$sqlQuery = $sql . '(' . $fields . ') values (' . $values . ');';
		
	    if (!$res = $this->objMysql->query($sqlQuery))
		{
			$return['msg'] = false;
			return $return;
		}
		else
		{
			$storeRelationRow['StoreID'] = $this->objMysql->getLastInsertId();
			if (!empty($storeRelationRow['MerchantID'])) {
				$storeRelFields .= " `StoreID`, `SiteName`, `MerchantID` ";
				foreach ((array)$storeRelationRow['MerchantID'] as $k1 => $v1) {
					$vtmp = explode('-', $v1);
					$csql = "select count(*) as cnt from `store_merchant_relationship` where `SiteName`='{$vtmp[1]}' and `MerchantID`={$vtmp[0]}";
					$cquery = $this->objMysql->query($csql);
					$count = $this->objMysql->getRow($cquery);
					if (isset($count['cnt']) && $count['cnt'] > 0) continue;
				    $storeRelValues .= "('" . addslashes($storeRelationRow['StoreID']) . "', '" . addslashes($vtmp[1]) . "', '" . addslashes($vtmp[0]) . "'), ";
				}
				$storeRelValues = preg_replace("|, $|i", '', $storeRelValues);
		        $sqlStoreRelationshipQuery = $sqlStoreRelationship . '(' . $storeRelFields . ') values ' . $storeRelValues;
		        
		        if (!$res1 = $this->objMysql->query($sqlStoreRelationshipQuery)) {
		        	$return['msg'] = false;
		        	return $return;
		        }
			}
			
			if (!empty($comment['Content'])) {
				$commentFields .= " `AddUser`, `AddDate`, `Content` ";
				$commentValues .= "('" . addslashes($comment['AddUser']) . "', '" . addslashes($comment['AddDate']) . "', '" . addslashes($comment['Content']) . "')";
				$sqlCommentQuery = $sqlComment . '(' . $commentFields . ') values ' . $commentValues;
		        
		        if (!$res2 = $this->objMysql->query($sqlCommentQuery)) {
		        	$return['msg'] = false;
		        	return $return;
		        } else {
		        	$commentRel['CommentID'] = $this->objMysql->getLastInsertId();
		        	$commentRelFields .= " `CommentID`, `ObjectType`, `ObjectID` ";
		        	$commentRelValues .= "('" . addslashes($commentRel['CommentID']) . "', 'store', '" . addslashes($storeRelationRow['StoreID']) . "')";
		        	$sqlCommentRelationshipQuery = $sqlCommentRelationship . '(' . $commentRelFields . ') values ' . $commentRelValues;
		        	
		        	if (!$res3 = $this->objMysql->query($sqlCommentRelationshipQuery)) {
		        		$return['msg'] = false;
		        	    return $return;
		        	}
		        }
			}
		}
		$return = array('msg' => true, 'storeid' => $storeRelationRow['StoreID']);
		return $return;
	}
	
	function updateData($row, $id) {
		if (empty($row)) return false;
		$storeRelationRow['MerchantID'] = $row['MerchantID'];
		
		$comment['Content'] = trim($row['Comment']);
		$comment['AddUser'] = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
		$comment['AddDate'] = date('Y-m-d H:i:s');
		
		unset($row['MerchantID']);
		unset($row['Comment']);
		
		$sqlQuery = "update `store` set ";
		$sqlStoreRelationship = "insert into `store_merchant_relationship` ";
		$sqlComment = "insert into `comment` ";
		$sqlCommentRelationship = "insert into `comment_relationship` ";
		
		$storeRelFields = '';
		$storeRelValues = '';
		$commentFields = '';
		$commentValues = '';
		$commentRelFields = '';
		$commentRelValues = '';
		
		foreach ($row as $k => $v) 
		{
			$sqlQuery .= "`" . $k . "` = '" . addslashes($v) . "', ";
		}
		
		$sqlQuery = preg_replace("|, $|i", ' ', $sqlQuery);
		$sqlQuery .= " WHERE `ID`={$id}";
		
		if (!$res = $this->objMysql->query($sqlQuery))
		{
			return false;
		}
		else
		{
			if (!empty($storeRelationRow['MerchantID'])) {
				$storeRelFields .= " `StoreID`, `SiteName`, `MerchantID` ";
				foreach ((array)$storeRelationRow['MerchantID'] as $k1 => $v1) {
					$vtmp = explode('-', $v1);
					$csql = "select count(*) as cnt from `store_merchant_relationship` where `SiteName`='{$vtmp[1]}' and `MerchantID`={$vtmp[0]}";
					$cquery = $this->objMysql->query($csql);
					$count = $this->objMysql->getRow($cquery);
					if (isset($count['cnt']) && $count['cnt'] > 0) continue;
					
				    $storeRelValues .= "('" . addslashes($id) . "', '" . addslashes($vtmp[1]) . "', '" . addslashes($vtmp[0]) . "'), ";
				}
				$storeRelValues = preg_replace("|, $|i", '', $storeRelValues);
				if (empty($storeRelValues)) return true;
		        $sqlStoreRelationshipQuery = $sqlStoreRelationship . '(' . $storeRelFields . ') values ' . $storeRelValues;
		        
		        if (!$res1 = $this->objMysql->query($sqlStoreRelationshipQuery)) return false;
			}
			
		    if (!empty($comment['Content'])) {
				$commentFields .= " `AddUser`, `AddDate`, `Content` ";
				$commentValues .= "('" . addslashes($comment['AddUser']) . "', '" . addslashes($comment['AddDate']) . "', '" . addslashes($comment['Content']) . "')";
				$sqlCommentQuery = $sqlComment . '(' . $commentFields . ') values ' . $commentValues;
		        
		        if (!$res2 = $this->objMysql->query($sqlCommentQuery)) {
		        	return false;
		        } else {
		        	$commentRel['CommentID'] = $this->objMysql->getLastInsertId();
		        	$commentRelFields .= " `CommentID`, `ObjectType`, `ObjectID` ";
		        	$commentRelValues .= "('" . addslashes($commentRel['CommentID']) . "', 'store', '" . addslashes($id) . "')";
		        	$sqlCommentRelationshipQuery = $sqlCommentRelationship . '(' . $commentRelFields . ') values ' . $commentRelValues;
		        	
		        	if (!$res3 = $this->objMysql->query($sqlCommentRelationshipQuery)) return false;
		        }
			}
		}
		
		return true;
	}
	
	function getStoreByID($id) {
		$data = array();
		if (!is_numeric($id)) return $data;
		$sql = "select * from `store` where `ID`={$id}";
		if ($query = $this->objMysql->query($sql)) {
			$data = $this->objMysql->getRow($query);
		}
		
		return $data;
	}
	
	function getMerchantsByKw($kw, $site) {
		global $databaseInfo;
		$data = array();
		$sql = '';
		if (trim($kw) == '') return $data;
		$siteModel = new Mysql($databaseInfo["INFO_" . trim($site) . "_DB_NAME"], $databaseInfo["INFO_" . trim($site) . "_DB_HOST"], $databaseInfo["INFO_" . trim($site) . "_DB_USER"], $databaseInfo["INFO_" . trim($site) . "_DB_PASS"]);
	    
		preg_match('/[^\d]+/', trim($kw), $matches);
		if (!empty($matches)) {
			$sql = "select `ID`, `Name` from `normalmerchant` where `Name` like '%". trim($kw) ."%' and IsActive='YES' ";
		} else {
			$sql = "select `ID`, `Name` from `normalmerchant` where (`ID` like '%". trim($kw) ."%' or `Name` like '%". trim($kw) ."%') and IsActive='YES' ";
		}
		
		$data = $siteModel->getRows($sql);
		foreach ((array)$data as $k => $v) {
			$data[$k]['Site'] = $site;
		}
		
		return $data;
		
	}
	
	function getStoreRelationshipByStoreID($storeid) {
		global $databaseInfo;
		$data = array();
		if (!is_numeric($storeid)) return $data;
		$sql = "select * from `store_merchant_relationship` where `StoreID`={$storeid}";
		
		$data = $this->objMysql->getRows($sql);
		
		foreach ($data as $k => $v) {
			$siteModel = new Mysql($databaseInfo["INFO_" . $v['SiteName'] . "_DB_NAME"], $databaseInfo["INFO_" . $v['SiteName'] . "_DB_HOST"], $databaseInfo["INFO_" . $v['SiteName'] . "_DB_USER"], $databaseInfo["INFO_" . $v['SiteName'] . "_DB_PASS"]);
		    $sql = "select `Name` from `normalmerchant` where `ID`={$v['MerchantID']} ";
		    $query = $siteModel->query($sql);
		    $row = $siteModel->getRow($query);
		    $data[$k]['MerchantName'] = $row['Name'];
		}
		
		return $data;
	}
	
	function deleteStoreRel($condition = array()) {
		if (empty($condition)) return true;
		$sql = "delete from `store_merchant_relationship` where `StoreID`={$condition['StoreID']} and `SiteName`='{$condition['SiteName']}' and `MerchantID`={$condition['MerchantID']}";
		$res = $this->objMysql->query($sql);
		if ($res) return true;
		else return false;
	}
	
	function getDistinctStoreID() {
		$data = array();
		$sql = "select distinct(`StoreID`) from `store_merchant_relationship`";
		$tmp = $this->objMysql->getRows($sql);
		foreach ((array)$tmp as $v) {
			$data[] = $v['StoreID'];
		}
		
		return $data;
	}
	
	function getCommentsByStoreID($id = '') {
		$data = array();
		if (empty($id)) return $data;
		$sql = "select `CommentID` from `comment_relationship` where `ObjectType`='store' and `ObjectID`={$id} order by `CommentID` desc";
		$tmp = $this->objMysql->getRows($sql);
		foreach ($tmp as $k => $v) {
			$sql1 = "select * from `comment` where `ID`={$v['CommentID']}";
			$query = $this->objMysql->query($sql1);
			$row = $this->objMysql->getRow($query);
			$data[$k] = $row;
		}
		
		return $data;
	}
	
	function checkStoreRel($condition = array()) {
		if (empty($condition)) return true;
		$sql = "select count(*) as cnt from `store_merchant_relationship` where `SiteName`='{$condition['SiteName']}' and `MerchantID`={$condition['MerchantID']}";
		$query = $this->objMysql->query($sql);
		$res = $this->objMysql->getRow($query);
		if ($res['cnt'] > 0) return true;
		else return false;
	}
	
	function getStoreByKw($kw) {
		$data = array();
		if (trim($kw) == '') return $data;
		
		preg_match('/[^\d]+/', trim($kw), $matches);
		if (!empty($matches)) {
			$sql = "select `ID`, `Name`, `Url` from `store` where `Name` like '%". trim($kw) ."%'";
		} else {
			$sql = "select `ID`, `Name`, `Url` from `store` where `ID` like '%". trim($kw) ."%' or `Name` like '%". trim($kw) ."%'";
		}
		
		$data = $this->objMysql->getRows($sql);
		
		return $data;
	}
	
	function deleteStoreAndRel($storeid = '') {
		if (empty($storeid)) return true;
		$store_sql = "delete from `store` where `ID`={$storeid}";
		$storemer_sql = "delete from `store_merchant_relationship` where `StoreID`={$storeid}";
		$storecompetitor_sql = "delete from `store_competitor_relationship` where `StoreID`={$storeid}";
		
		if (!$this->objMysql->query($storecompetitor_sql)) return false;
		if (!$this->objMysql->query($storemer_sql)) return false;
		if (!$this->objMysql->query($store_sql)) return false;
		
		return true;
	}
	
	function removeStoreAndRelToLog($row) {
		if (!$this->deleteStoreAndRel($row['FromStoreID'])) return false;
		$sql = "insert into `store_remove_log` (`Type`, `FromStoreID`, `FromStoreUrl`, `Operator`, `Reason`, `AddTime`) values (
		       'DELETE', {$row['FromStoreID']}, '" . addslashes($row['FromStoreUrl']) . "', '" . addslashes($row['Operator']) . "', '" . addslashes($row['Reason']) . "', '" . date('Y-m-d H:i:s') . "'
		       )";
		if (!$this->objMysql->query($sql)) return false;
		
		return true;
	}
	
	function mergeStoreAndRelToLog($row) {
		$store_mer_rel_update_sql = "update `store_merchant_relationship` set `StoreID`={$row['ToStoreID']} where `StoreID`={$row['FromStoreID']}";
		$store_coment_rel_update_sql = "update `comment_relationship` set `ObjectID`={$row['ToStoreID']} where `ObjectType`='store' and `ObjectID`={$row['FromStoreID']}";
		$store_delete_sql = "delete from `store` where `ID`={$row['FromStoreID']}";
		$sql = "insert into `store_remove_log` (`Type`, `FromStoreID`, `FromStoreUrl`, `ToStoreID`, `Operator`, `Reason`, `AddTime`) values (
		       'MERGE', {$row['FromStoreID']}, '" . addslashes($row['FromStoreUrl']) . "', {$row['ToStoreID']}, '" . addslashes($row['Operator']) . "', '" . addslashes($row['Reason']) . "', '" . date('Y-m-d H:i:s') . "'
		       )";
		
		$from_store_competitor_rel_sql = "select `CompetitorId`, `Url` from `store_competitor_relationship` where `StoreID`={$row['FromStoreID']}";
		$from_store_competitor_rel_data = $this->objMysql->getRows($from_store_competitor_rel_sql);
		
		$from_store_competitor_rel_delete_sql = "delete from `store_competitor_relationship` where `StoreID`={$row['FromStoreID']}";
		
		foreach ((array)$from_store_competitor_rel_data as $fv) {
			$to_sql = "replace into `store_competitor_relationship` (`StoreID`, `CompetitorId`, `Url`) values ({$row['ToStoreID']}, '{$fv['CompetitorId']}', '" . addslashes($fv['Url']) . "')";
			if (!$this->objMysql->query($to_sql)) return false;
		}
		
		if (!$this->objMysql->query($from_store_competitor_rel_delete_sql)) return false;
		if (!$this->objMysql->query($store_mer_rel_update_sql)) return false;
		if (!$this->objMysql->query($store_coment_rel_update_sql)) return false;
		if (!$this->objMysql->query($store_delete_sql)) return false;
		if (!$this->objMysql->query($sql)) return false;
		
		return true;
		
	}
}
?>