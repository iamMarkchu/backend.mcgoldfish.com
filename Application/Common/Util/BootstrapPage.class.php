<?php 
namespace Common\Util;

use Think\Page;

class BootstrapPage extends Page
{

	public function __construct($totalRows, $listRows=20, $parameter = array())
	{
		parent::__construct($totalRows, $listRows, $parameter);
		$this->setConfig('header','<li class="disabled"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
        $this->setConfig('prev','上一页');
        $this->setConfig('next','下一页');
        $this->setConfig('last','末页');
        $this->setConfig('first','首页');
        $this->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	}

	public function show()
	{
		$show_html = parent::show();
		if($show_html)
		{
			$show_html = str_replace('<div>', '<nav class="pull-right"><ul class="pagination">', $show_html);
			$show_html = str_replace('</div>', '</ul></nav>', $show_html);
			$show_html = str_replace('<span class="current">','<li class="active"><a>',$show_html);
        	$show_html = str_replace('</span>','</a></li>',$show_html);
        	$show_html = str_replace(array('<a class="num"','<a class="prev"','<a class="next"','<a class="end"','<a class="first"'),'<li><a',$show_html);
        	$show_html = str_replace('</a>','</a></li>',$show_html);
		}
		return $show_html;
	}
}