<?php 
namespace Home\Model;
use Think\Model;
class CommentModel extends Model {
	protected $tablePrefix = '';
	public function getCommentListForPage($start=0,$length=10,$map,$order){
		$where = "where ru.modeltype = 'article' and ru.isjump = 'no' ";
		// if(!empty($map)){
		// 	if(isset($map['titleOrId'])){
		// 		$titleOrId = $map['titleOrId'];
		// 		$where .= "and (a.id = '{$titleOrId}' or a.`title` LIKE '%{$titleOrId}%') ";
		// 	}
		// 	if(isset($map['articlesource'])){
		// 		$articlesource = $map['articlesource'];
		// 		$where .= "and a.articlesource = '{$articlesource}' ";
		// 	}
		// 	if(isset($map['status'])){
		// 		$status = $map['status'];
		// 		$where .= "and a.status = '{$status}' ";
		// 	}
		// }
		// if(!empty($order)){
		// 	$order = "order by {$order}";
		// }
		$sql = "select a.title,ru.`requestpath`,c.*,cc.username as parentname from article as a left join rewrite_url as ru on a.id = ru.optdataid left join comment as c on c.optdataid = a.id left join comment as cc on c.parentcommentid = cc.id {$where} and c.datatype = 'article' and ru.isJump = 'NO' {$order} limit {$start},{$length}";
		$result = $this->query($sql);
		//添加tag信息
		foreach ($result as $k => $v) {
			$sql = "select * from tag_mapping as tm left join tag as t on tm.tagid = t.id where tm.datatype = 'article' and optdataid = {$v['id']}";
			$tmpResult = $this->query($sql);
			$result[$k]['tag'] = $tmpResult;
		}
		return $result;
	}
	public function getCommentListForPageCount($map,$order){
		$where = "where ru.modeltype = 'article' and ru.isjump = 'no' ";
		// if(!empty($map)){
		// 	if(isset($map['titleOrId'])){
		// 		$titleOrId = $map['titleOrId'];
		// 		$where .= "and (a.id = '{$titleOrId}' or a.`title` LIKE '%{$titleOrId}%') ";
		// 	}
		// 	if(isset($map['articlesource'])){
		// 		$articlesource = $map['articlesource'];
		// 		$where .= "and a.articlesource = '{$articlesource}' ";
		// 	}
		// 	if(isset($map['status'])){
		// 		$status = $map['status'];
		// 		$where .= "and a.status = '{$status}' ";
		// 	}
		// }
		// if(!empty($order)){
		// 	$order = "order by {$order}";
		// }
		$sql = "select a.title,ru.`requestpath`,c.*,cc.username as parentname from article as a left join rewrite_url as ru on a.id = ru.optdataid left join comment as c on c.optdataid = a.id left join comment as cc on c.parentcommentid = cc.id {$where} and c.datatype = 'article' and ru.isJump = 'NO' {$order}";
		$result = $this->query($sql);
		$result = count($result);
		return $result;
	}
	public function resume($options,$field='status'){
        if(FALSE === $this->where($options)->setField($field,'active')){
            return false;
        }else {
            return True;
        }
    }
}