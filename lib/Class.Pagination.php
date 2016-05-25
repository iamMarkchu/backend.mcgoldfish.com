<?
/*
 * FileName: Class.Pagination.php
 * Author: Lee
 * Create Date: 2006-9-14
 * Package: package_name
 * Project: project_name
 * Remark: 
*/
if (!defined("__CLASS_PAGINATION__")){
	define("__CLASS_PAGINATION__",1);
   
	class Pagination
	{
	 	var $total;
	    var $onepage;
	    var $num;
	    var $page;
	    var $totalPage;
	    var $offset;
	    var $linkhead;
	
		function Pagination($total,$onepage)
		{
			$this->resetPagination($total, $onepage);
		}
	
		function resetPagination($total, $onepage)
		{
			$page  = array_key_exists('page', $_GET) ? intval($_GET['page']) : '';
			$this->total = $total;
			$this->onepage = $onepage;
			$this->totalPage = ceil($total/$onepage);
			if ($page=='')
			{
				$this->page = 1;
				$this->offset = 0;
			}
			else
			{
				$this->page = $page;
				$this->offset = ($page-1)*$onepage;
			}
			$formlink = '';
			$linkarr = explode("page=", $_SERVER['QUERY_STRING']);
			$linkft  = $linkarr[0];
			
			/*
			//fix the rewrite rule bug for search page
			if(stristr($_SERVER['PHP_SELF'], "/se-") === 0)
			{
			$arrTmp = explode(".html", substr(strchr($_SERVER['PHP_SELF'], "-"), 1));
			$kw = trim($arrTmp[0]);
			$requestUri = str_replace($kw, urlencode($kw), $_SERVER['PHP_SELF']);
			$_SERVER['PHP_SELF'] = $requestUri;
			}
			
			//end
			if ($linkft=='')
			{
			$this->linkhead = $_SERVER['PHP_SELF']."?".$formlink;
			//                $this->linkhead = $_SERVER['PHP_SELF']."&";
			}
			else
			{
			$linkft         = substr($linkft, -1)=="&" ? $linkft : $linkft."&";
			$this->linkhead = $_SERVER['PHP_SELF']."?".$linkft.$formlink;
			//              $this->linkhead = $_SERVER['PHP_SELF']."&";
			}
			$this->linkhead = preg_replace("/&page=\d+/", "", $this->linkhead);
			*/
			$kw = trim($_GET['keyword']);
			//           	$kw = str_replace("_", "-", $kw);
			$kw = my_trim($kw);
			//			$kw = urlencode($kw);
			if($kw)
			{
				$this->linkhead = LINK_ROOT."s_{$kw}_";
			}
			else
			{
				$this->linkhead = LINK_ROOT."hotkeywords_";
			}
		}
	
	    function getOffset()
	    {
	        return $this->offset;
	    }
	    
	    function getTotalPageString($char='')
	    {
	        $linkhead   = $this->linkhead;
	        $totalPage = $this->totalPage;
	        $char = $char ? $char : $totalPage;
	        
	        if($this->totalPage > $this->num && $this->page < $this->totalPage-floor($this->num/2)+1)
	        {
	        	return "<a href=\"$linkhead"."page=$totalPage\" title=\"go to last page\"  class=\"paging\">$char</a> ";
	        }
	    }
	    function getNumBar($num = 8, $color='', $maincolor='', $left='', $right='')
	    {
	        $this->num = $num;
	        $mid = floor($num/2);
	        $last = $num - 1; 
	        $page = $this->page;
	        $totalpage = $this->totalPage;
	        $linkhead  = $this->linkhead;
	        $left  = $left =='' ? "" : $left;
	        $right = $right=='' ? "" : $right;
	        $color = $color=='' ? "#ff0000" : $color;
	        $minpage = ($page-$mid)<1 ? 1 : $page-$mid;
	        $maxpage = $minpage + $last;
	        if ($maxpage>$totalpage)
	        {
	            $maxpage = $totalpage;
	            $minpage = $maxpage - $last;
	            $minpage = $minpage<1 ? 1 : $minpage;
	        }
			
			$linkbar = "";
	        for ($i=$minpage; $i<=$maxpage; $i++)
	        {
	            $chars = $left.$i.$right;
	            if ($i == $this->page)
	            {
	                $linkchar = "&nbsp;<span>".$chars."</span>&nbsp;";
	            }
	            else
	            {
	            	$linkchar = "<a href='$linkhead"."{$i}.html' title=\"go to page $i\" >".$chars."</a> ";
	            }
	            $linkbar .= $linkchar;
	        }
	        return $linkbar;
	    }
	    
	    function getWholeNumBar($num='', $color='', $maincolor='') 
	    {
	        return $this->getNumBar($num, $color, $maincolor);
	    }
	   
	    function getWholeBarString($jump='', $num=10, $color='#000000', $maincolor='#666666') 
	    {
	        $wholeNumBar = $this->getWholeNumBar($num, $color, $maincolor)."&nbsp;";
	        return $wholeNumBar;
	    }
	
	   function getLimitString()
	   {
	   		return "LIMIT {$this->offset}, {$this->onepage}";
	   }
	   function getFrom2str()
	   {
	   		return (($this->offset+$this->onepage) < $this->total) ? 
					($this->offset+1)." to ".($this->offset+$this->onepage) : 
					($this->offset+1)." to ".$this->total;
	   }
	   
	}
}
?>