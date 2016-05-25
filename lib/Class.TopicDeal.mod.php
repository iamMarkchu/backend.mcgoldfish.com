<?php
/*
 * FileName: Class.TopicDeal.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_TOPIC_DEAL__"))
{
   define("__MOD_CLASS_TOPIC_DEAL__",1);
   
   class TopicDeal
   {
   		var $objMysql;
   		
   		function TopicDeal($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getTopicDealListByLimitStr($limitStr="", $whereStr="", $orderStr=" ID ASC ")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, TopicID, CouponID, Discount, FinalPrice, `Order`, SelType, Keyword, AddTime from topicdeal $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['topicid'] = trim($arrTmp['TopicID']);
   				$arr[$i]['couponid'] = trim($arrTmp['CouponID']);
   				$arr[$i]['discount'] = trim($arrTmp['Discount']);
   				$arr[$i]['finalprice'] = trim($arrTmp['FinalPrice']);
   				$arr[$i]['order'] = trim($arrTmp['Order']);
   				$arr[$i]['seltype'] = trim($arrTmp['SelType']);
   				$arr[$i]['keyword'] = trim($arrTmp['Keyword']);
   				$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		function getTopicDealCountByTopicID($topicID)
   		{
   			$topicID = intval($topicID);
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from topicdeal  where TopicID = $topicID";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
   		function getTopicDealFullInfoByTopicID($topicID, $order="Order By td.`Order` ASC")
   		{
   			$arr = array();
   			$topicID = intval($topicID);
   			$sql = "select td.ID, td.CouponID, td.TopicID, td.Discount, td.FinalPrice, nc.Title, nc.Code, nc.Remark, ";
   			$sql .= "nm.Name, nc.MerchantID  from topicdeal as td, normalcoupon as nc, normalmerchant as nm ";
   			$sql .= "where td.CouponID = nc.ID and nc.MerchantID = nm.ID and td.TopicID = $topicID $order";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['topicid'] = trim($arrTmp['TopicID']);
   				$arr[$i]['couponid'] = trim($arrTmp['CouponID']);
   				$arr[$i]['discount'] = trim($arrTmp['Discount']);
   				$arr[$i]['finalprice'] = trim($arrTmp['FinalPrice']);
   				$arr[$i]['title'] = trim($arrTmp['Title']);
   				$arr[$i]['code'] = trim($arrTmp['Code']);
   				$arr[$i]['remark'] = trim($arrTmp['Remark']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$arr[$i]['mid'] = trim($arrTmp['MerchantID']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
   		function getTopicDealListByTopicID($topicID, $order="`order` ASC")
   		{
   			$topicID = intval($topicID);
   			$res = $this->getTopicDealListByLimitStr("", " TopicID = $topicID", $order);
   			return $res;
   		}
   		
   		function getTopicDealIDByTopicID($topicID)
   		{
   			$rtn = array();
   			$topicID = intval($topicID);
   			$res = $this->getTopicDealListByLimitStr("", " TopicID = $topicID");
   			foreach($res as $deal)
   			{
   				$rtn[] = $deal['couponid'];
   			}
   			return $rtn;
   		}
   		
   		function addTopicDeal($topicID, $couponID, $discount, $finalPrice, $order, $selType, $keyword)
   		{
   			$topicID = intval($topicID);
   			$couponID = intval($couponID);
   			$discount = trim($discount);
   			$finalPrice = trim($finalPrice);
   			$order = intval($order);
   			$selType = addslashes(trim($selType));
			$keyword = addslashes(trim($keyword));
			$article = addslashes(trim($article));
			
			$sql = "insert into topicdeal (TopicID, CouponID, Discount, FinalPrice, `Order`, SelType, Keyword, AddTime)";
			$sql .= "values ('$topicID', '$couponID', '$discount', '$finalPrice', '$order', '$selType', '$keyword', NOW())";
			$qryId = $this->objMysql->query($sql);
			return $this->objMysql->getLastInsertId($qryId);
   		}
   		
   		function updateTopicDealByID($topicDealID, $topicID, $couponID, $discount, $finalPrice, $order, $selType, $keyword)
   		{
   			$topicDealID = intval($topicDealID);
   			$topicID = intval($topicID);
   			$couponID = intval($couponID);
   			$discount = trim($discount);
   			$finalPrice = trim($finalPrice);
   			$order = intval($order);
   			$selType = addslashes(trim($selType));
			$keyword = addslashes(trim($keyword));
			$article = addslashes(trim($article));
			
			$sql = "update topicdeal set TopicID = '$topicID', CouponID = '$couponID', Discount = '$discount', ";
			$sql .= " FinalPrice = '$finalPrice', `Order` = '$order', SelType = '$selType', Keyword = '$keyword'";
			$sql .= " where ID = $topicDealID";
			$this->objMysql->query($sql);
   		}
   		
   		function deleteTopicDealByID($topicDealID)
		{
			$topicDealID = intval($topicDealID);
			$sql = "Delete from topicdeal where ID = $topicDealID";
			$this->objMysql->query($sql);
		}
		function parseDiscountFromCouponTitle($title)
		{
			$pattern[] = "/^([^a-z]*)\boff\b/i";
			$pattern[] = "/\bget\b([^a-z]*)\boff\b/i";
			$pattern[] = "/\bsave\b([^a-z]*)\bon\b/i";
			$pattern[] = "/\bsave\b([^a-z]*)\boff\b/i";
			$pattern[] = "/\bsave up to\b([^a-z]*)\bon\b/i";
			$pattern[] = "/\b([0-9 %\$]*)\boff\b/i";
			$pattern[] = "/\bsave up to\b([0-9%\$ ]*)\b/i";
			$pattern[] = "/\bsave\b([0-9%\$ ]*)\b/i";
			$pattern[] = "/\breceive\b([0-9%\$ ]*)\b/i";
			
			foreach($pattern as $p)
			{
				if(preg_match($p, $title, $matches))
				{
					$p = "/([^0-9\.]*)([0-9\.]+)([^0-9\.]*)/i";
					if(preg_match($p, $matches[1], $sub_matches))
					{
						if(intval($sub_matches[2]) == 0)
						{
							return "";
						}
						return trim($sub_matches[1].intval($sub_matches[2]).$sub_matches[3]);
					}
					else
					{	
						return $matches[1];
					}
				}
			}
			return "";
		}
		
		function deleteAutoTopicDeals($topicID)
		{
			$topicID = intval($topicID);
			$sql = "delete from topicdeal where TopicID = $topicID and SelType = 'auto'";
			$this->objMysql->query($sql);
			return $this->objMysql->getAffectedRows();
		}
		function deleteTopicDealByCouponID($topicID, $arrCouponID)
		{
			$topicID = intval($topicID);
			if(!is_array($arrCouponID) || count($arrCouponID) < 1)
				return 0;
			$sql = "delete from topicdeal where TopicID = $topicID and CouponID in (".implode(",", $arrCouponID).")";
			$this->objMysql->query($sql);
			return $this->objMysql->getAffectedRows();
		}
		
		function resetTopicDealOrder($topicID)
		{
			$topicID = intval($topicID);
			$this->objMysql->query("SET @temp =0;");
			$this->objMysql->query("UPDATE topicdeal SET `Order` = @temp := ( @temp +1 ) order by `Order` ASC;");
		}
   }
}
?>
