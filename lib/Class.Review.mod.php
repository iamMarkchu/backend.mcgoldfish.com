<?php
/*
 * FileName: Class.Review.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_REVIEW__"))
{
   define("__MOD_CLASS_REVIEW__",1);
   
   class Review
   {
   		var $objMysql;
   		
   		function Review($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getReviewCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from merchantreview $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
   		function getReviewListByLimitStr($limitStr="", $whereStr="", $orderStr=" ID DESC ")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Name, Rating, Title, Review, AddTime, Status, Source, MerchantID from merchantreview $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$arr[$i]['rating'] = intval($arrTmp['Rating']);
   				$arr[$i]['title'] = trim($arrTmp['Title']);
   				$arr[$i]['review'] = trim($arrTmp['Review']);
   				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
   				$arr[$i]['status'] = trim($arrTmp['Status']);
   				$arr[$i]['src'] = trim($arrTmp['Source']);
   				$arr[$i]['mid'] = intval($arrTmp['MerchantID']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		
   		function addReview($mid, $name, $rating,$title,$review,$SessionID="",$ServerID="",$addtime="", $status="INITIAL",$src="csus")
   		{
   			$mid = intval($mid);
   			$rating = intval($rating);
   			$name = addslashes(trim($name));
   			$title = addslashes(trim($title));
   			$review = addslashes(trim($review));
			if(!$addtime) $addtime = date("Y-m-d H:i:s");
   			$addtime = addslashes(trim($addtime));
   			$status = addslashes(trim($status));
   			$src = addslashes(trim($src));
			$sql = "insert into merchantreview (Name, Rating, Title, Review, AddTime, Status, Source, MerchantID,SessionID,ServerID)";
			$sql .= " values ('$name', $rating, '$title', '$review', '$addtime', '$status', '$src',$mid,'$SessionID','$ServerID') ";
   			$qryId = $this->objMysql->query($sql);
   			return $this->objMysql->getLastInsertId($qryId);
   		}
   		
		function getApprovedReviewCntByMerID($mid)
	   {
   			$mid = intval($mid);
   			$sql = "select count(*) as cnt from merchantreview where Status = 'APPROVED' and MerchantID = ".intval($mid);
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$cnt = intval($arrTmp['cnt']);
   			$this->objMysql->freeResult($qryId);
   			return $cnt;
	   }

	   function getAvgApproveRatingByMerID($mid)
	   {
   			$mid = intval($mid);
   			$sql = "select AVG(Rating) as r from merchantreview where Status = 'APPROVED' and MerchantID = ".intval($mid);
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$cnt = ceil($arrTmp['r']);
   			$this->objMysql->freeResult($qryId);
   			return $cnt;
	   }

   		function getReviewListByMerID($mid, $limit="LIMIT 0, 50")
   		{
   			$arrTmp = $this->getReviewListByLimitStr($limit, "MerchantID = ".intval($mid));
   			return $arrTmp;
   		}
   		
   		function getRatingImgString($ratingFloat, $numeric = false){
   			$retStr = "0";
   			$ratingFloat = floatval($ratingFloat);
   			if($numeric == true){
   				if($ratingFloat >= 4.8){
	   				$retStr = "5";
	   			}elseif($ratingFloat > 4.3){
	   				$retStr = "4.5";
	   			}elseif($ratingFloat > 3.8){
	   				$retStr = "4";
	   			}elseif($ratingFloat > 3.3){
	   				$retStr = "3.5";
	   			}elseif($ratingFloat > 2.8){
	   				$retStr = "3";
	   			}elseif($ratingFloat > 2.3){
	   				$retStr = "2.5";
	   			}elseif($ratingFloat > 1.8){
	   				$retStr = "1";
	   			}elseif($ratingFloat > 0.4){
	   				$retStr = "0.5";
	   			}
   			}else{
	   			if($ratingFloat >= 4.8){
	   				$retStr = "5";
	   			}elseif($ratingFloat > 4.3){
	   				$retStr = "4_5";
	   			}elseif($ratingFloat > 3.8){
	   				$retStr = "4";
	   			}elseif($ratingFloat > 3.3){
	   				$retStr = "3_5";
	   			}elseif($ratingFloat > 2.8){
	   				$retStr = "3";
	   			}elseif($ratingFloat > 2.3){
	   				$retStr = "2_5";
	   			}elseif($ratingFloat > 1.8){
	   				$retStr = "1";
	   			}elseif($ratingFloat > 0.4){
	   				$retStr = "0_5";
	   			}
   			}
   			return $retStr;
   		}
   }
}
?>
