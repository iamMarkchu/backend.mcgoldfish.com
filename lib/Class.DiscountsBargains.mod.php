<?php
class DiscountsBargains
{
	var $objMysql;
	
	function DiscountsBargains($objMysql)
	{
		$this->objMysql = $objMysql;
	}
	
	function getRandomKeywordId()
	{
		$sql = "SELECT t1.Id FROM `discounts_bargains_keywords` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(Id) FROM `discounts_bargains_keywords`)-(SELECT MIN(Id) FROM `discounts_bargains_keywords`))+(SELECT MIN(Id) FROM `discounts_bargains_keywords`)) AS Id) AS t2 WHERE t1.Id >= t2.Id ORDER BY t1.Id LIMIT 1";
		$qry = $this->objMysql->query($sql);
		while($arrTmp = $this->objMysql->getRow($qry))
		{
			return $arrTmp["Id"];
		}
		return 0;
	}
	
	function getNeighboringKeywordsByKeyword($_keyword_1,$_keyword_2="")
	{
		$keywordId = $this->getKeywordIdByKeyword($_keyword_1,$_keyword_2);
		return $this->getNeighboringKeywordsById($keywordId);
	}
	
	function getNeighboringKeywordsById($_id=0)
	{
		$arrReturn = array();
		$numtoselect = 20;
		if($_id == 0) $_id = $this->getRandomKeywordId();

		//$arrId = array();
		//$arrId[] = $arrId;
		$sql = "select * from discounts_bargains_keywords where Id > $_id and Status = 1 order by Id limit $numtoselect";
		$qry = $this->objMysql->query($sql);
		while($arrTmp = $this->objMysql->getRow($qry))
		{
			//$arrId[] = $arrTmp["Id"];
			$arrReturn[] = $arrTmp;
			$numtoselect --;
		}
		
		if($numtoselect > 0)
		{
			//and Id not in(".implode(",",$arrId).")
			$sql = "select * from discounts_bargains_keywords where Status = 1 order by Id limit $numtoselect";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qry))
			{
				$arrReturn[] = $arrTmp;
				$numtoselect --;
			}
		}

		$this->capitalizeKeywordInList($arrReturn);
		return $arrReturn;
	}
	
	
	function getTopKeywords($_limitnum=50)
	{
		$arrReturn = array();
		if($_limitnum == -1) $strLimit = "";
		else $strLimit = "limit $_limitnum";
		
		$sql = "select * from discounts_bargains_keywords where Status = 1 order by Id desc $strLimit";
		$arrReturn = $this->objMysql->getRows($sql);
		$this->capitalizeKeywordInList($arrReturn);
		return $arrReturn;
	}
	
	
	function getKeywordIdByKeyword($_keyword_1,$_keyword_2="")
	{
		$sql = "select Id from discounts_bargains_keywords where Keyword = '" . addslashes($_keyword_1) . "'";
		if($_keyword_2)
		{
			$sql .= " or Keyword = '" . addslashes($_keyword_2) . "'";
		}
		$arrReturn = $this->objMysql->getRows($sql);
		$this->capitalizeKeywordInList($arrReturn);
		if(isset($arrReturn[0]))
		{
			$this->currentKeywordInfo = $arrReturn[0];
			return $arrReturn[0]["Id"];
		}
		return 0;
	}
	
	function &getFilterPatterns()
	{
		if(!isset($this->arrFilter))
		{
			$arrFilter = array();
			$arrBadKeyword = array(
				"vouchercodes{0,1}","couponcodes{0,1}","e-vouchers{0,1}","e-coupons{0,1}","e vouchers{0,1}",
				"coupons{0,1}","discountcodes{0,1}","discounts{0,1}","vouchers{0,1}","coupons{0,1}",
				"promos{0,1}","promotion","promotional","codes{0,1}","for","of","discounts{0,1}",
				"offers{0,1}","special","free shippings{0,1}","deals{0,1}","online","free","delivery","2009","shopping",
			);
			$arrFilter[] = '/\b(' . implode("|",$arrBadKeyword) . ')\b/i';
			$arrFilter[] = '/[^a-z0-9\.]/i';//this one should be the last one!!!
			$this->arrFilter = $arrFilter;
		}
		return $this->arrFilter;
	}
	
	function getNormalizedKeyword($_keyword)
	{
		$arrFilter = $this->getFilterPatterns();
		//$_keyword = substr(strstr($_keyword, '_'), 1);
		//$_keyword = preg_replace($arrFilter, " ", $_keyword);
		$_keyword = preg_replace('/\s+/', " ", $_keyword);
		return trim($_keyword);
	}
	
	function capitalizeKeywordInList(&$list)
	{
		foreach($list as &$v)
		{
			if(!isset($v["Keyword"])) break;
			$this->capitalizeString($v["Keyword"]);
		}
	}

	function capitalizeString(&$str)
	{
		$str = ucwords(strtolower($str));
	}
}
?>