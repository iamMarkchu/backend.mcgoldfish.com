<?php 
namespace Home\Model;
use Think\Model;
class ArticleModel extends CommonModel {
	protected $tablePrefix = '';
	protected $_validate = [
	    ['title','','帐号名称已经存在！',0,'unique',1],
    ];
	public function getArticleListForPage($start=0,$length=10,$map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['title'])){
				$titleOrId = $map['title'];
				$where .= " and a.`title` LIKE '%{$titleOrId}%' ";
			}
			if(isset($map['articlesource'])){
				$articlesource = $map['articlesource'];
				$where .= " and a.articlesource = '{$articlesource}' ";
			}
			if(isset($map['status'])){
				$status = $map['status'];
				$where .= " and a.status = '{$status}' ";
			}
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}
		$sql = "select * from article as a {$where} {$order} limit {$start},{$length}";
		$result = $this->query($sql);
		//添加tag信息,category信息
		foreach ($result as $k => $v) {
			$sql = "select * from tag_mapping as tm left join tag as t on tm.tagid = t.id where tm.datatype = 'article' and optdataid = {$v['id']}";
			$tmpResult = $this->query($sql);
			$result[$k]['tag'] = $tmpResult;
			$sql = "select * from category_mapping as cm left join category as c on cm.categoryid = c.id where cm.datatype = 'article' and optdataid = {$v['id']}";
			$tmpResult = $this->query($sql);
			if(!empty($tmpResult)){
				$result[$k]['displayname'] = $tmpResult[0]['displayname'];
			}
		}
		return $result;
	}
	public function getArticleListForPageCount($map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['titleOrId'])){
				$titleOrId = $map['titleOrId'];
				$where .= "and (a.id = '{$titleOrId}' or a.`title` LIKE '%{$titleOrId}%') ";
			}
			if(isset($map['articlesource'])){
				$articlesource = $map['articlesource'];
				$where .= "and a.articlesource = '{$articlesource}' ";
			}
			if(isset($map['status'])){
				$status = $map['status'];
				$where .= "and a.status = '{$status}' ";
			}
			if(isset($map['addeditor'])){
				$status = $map['addeditor'];
				$where .= "and a.addeditor = '{$addeditor}' ";
			}
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}
		$sql = "select * from article as a {$where} {$order}";
		$result = $this->query($sql);
		$result = count($result);
		return $result;
	}
}