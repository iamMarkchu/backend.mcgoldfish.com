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
	 	private $total;
	    private $onepage;
	    private $num;
	    private $page;
	    private $totalPage;
	    private $offset;
		private $type;
		var $obj_id;
		var $obj_name;
	
		public function Pagination($total, $onepage, $type='cate') 
		{
			$this->resetPagination($total, $onepage, $type);
		}
	
		function resetPagination($total, $onepage, $type='cate')
		{
			$page  = array_key_exists('page', $_GET) ? intval($_GET['page']) : '';
			$this->total = $total;
			$this->onepage = $onepage;
			$this->totalPage =  ceil($total/$onepage);
			$this->type = $type;
		
			if ($page=='')
			{
			    $this->page = 1;      
			    $this->offset = 0;    
			}
			else
			{
			    $this->page = $page > $this->totalPage ? $this->totalPage : $page;
			    $this->offset = ($this->page-1)*$onepage;
			}
		}
		
	    public function getOffset()
	    {
	        return $this->offset;
	    }
	    
	    private function getFirstPageString($char='1')
	    {
        	if($this->totalPage > $this->num && $this->page > floor($this->num/2)+1)
	        {
	        	return "<a href=\"".$this->get_url(1)."\" >$char</a> ";
	        }
	    }
	    
	    private function getPrePageString($char='< Prev')
	    {
	        $page     = $this->page;
	        if ($this->totalPage > $this->num && $page>floor($this->num/2)+1)
	        {
	            $prePage = $page - 1;
	            return "<a href=\"".$this->get_url($prePage)."\">$char</a> | ";
	        }
	    }
	    
	    private function getNextPageString($char='Next >') 
	    {
	        $totalPage = $this->totalPage;
	        $page = $this->page;
	        if ($this->totalPage > $this->num && $page < $this->totalPage-floor($this->num/2)+1)
	        {
	            $nextPage = $page + 1;
	            return "<a href=\"".$this->get_url($nextPage)."\">$char</a>";
	        }
	    }
	    
	    private function getNumBar($num = 6)
	    {
	        $this->num = $num;
	        $mid = floor($num/2);
	        $last = $num - 1; 
	        $page = $this->page;
	        $totalpage = $this->totalPage;
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
	            $chars = $i;
	            if ($i == $this->page)
	            {
	                $chars = "<span style='color:red'>".$chars."</span>";
	            }
	            $linkchar = "<a href='".$this->get_url($i)."'>".$chars."</a> | ";
	            $linkbar .= $linkchar;
	        }
	        return $linkbar;
	    }
	    
	    private function getWholeNumBar($num=6) 
	    {
	        $numBar = $this->getNumBar($num);
			$nxtStr = $this->getNextPageString();
			if(!$nxtStr) $numBar = rtrim($numBar, " | ");
	        return  $this->getPrePageString(). $numBar. $nxtStr;
	    }
	   
	    public function getWholeBarString($num=6) 
	    {
	        $wholeNumBar = $this->getWholeNumBar($num, $color, $maincolor);

	        return " <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n".
	               "   <tr>\n". 
	               "      <td align=\"right\" width='90%'>Pagination: $wholeNumBar &nbsp;&nbsp;&nbsp;&nbsp;</td>\n".
	               "   </tr>\n".
	               " </table>\n";
	    }
	
	   public function getLimitString()
	   {
	   		return "LIMIT {$this->offset}, {$this->onepage}";
	   }
	   
		private function get_url($bn)
		{
			$para = array("page" => $bn);
			return get_rewrited_url($this->type,$this->obj_name,$this->obj_id,"/",true,$para);
		}
	}
}
?>