<?php 
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model {
	protected $tablePrefix = '';
	public function getArticleListForPage($start=0,$length=10,$map,$order){
		$where = "where ru.modeltype = 'article' and ru.isjump = 'no' ";
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
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}
		$sql = "select a.*,ru.`id` as rid,ru.`requestpath`,c.displayname from article as a left join rewrite_url as ru on a.id = ru.optdataid left join category_mapping as cm on a.id = cm.`optdataid` left join category as c on cm.`categoryid` = c.id {$where} {$order} limit {$start},{$length}";
		$result = $this->query($sql);
		return $result;
	}
	public function getArticleListForPageCount($map,$order){
		$where = "where ru.modeltype = 'article' and ru.isjump = 'no' ";
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
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}
		$sql = "select a.*,ru.`id` as rid,ru.`requestpath`,c.displayname from article as a left join rewrite_url as ru on a.id = ru.optdataid left join category_mapping as cm on a.id = cm.`optdataid` left join category as c on cm.`categoryid` = c.id {$where} {$order}";
		$result = $this->query($sql);
		$result = count($result);
		return $result;
	}
}