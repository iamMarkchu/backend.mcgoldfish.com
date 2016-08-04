<?php 
namespace Home\Model;
use Think\Model;
class AlbumModel extends CommonModel {
	protected $tablePrefix = '';
	public function getAlbumListForPage($start=0,$length=10,$map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['titleOrId'])){
				$titleOrId = $map['titleOrId'];
				$where .= "and (a.id = '{$titleOrId}' or a.`title` LIKE '%{$titleOrId}%') ";
			}
			if(isset($map['albumsource'])){
				$albumsource = $map['albumsource'];
				$where .= "and a.albumsource = '{$albumsource}' ";
			}
			if(isset($map['status'])){
				$status = $map['status'];
				$where .= "and a.status = '{$status}' ";
			}
			if(isset($map['addeditor'])){
				$addeditor = $map['addeditor'];
				$where .= "and a.addeditor = '{$addeditor}' ";
			}
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}
		$sql = "select * from album as a {$where} {$order} limit {$start},{$length}";
		$result = $this->query($sql);
		//添加tag信息,url信息,category信息
		foreach ($result as $k => $v) {
			$sql = "select * from tag_mapping as tm left join tag as t on tm.tagid = t.id where tm.datatype = 'album' and optdataid = {$v['id']}";
			$tmpResult = $this->query($sql);
			$result[$k]['tag'] = $tmpResult;
			$sql = "select * from rewrite_url where optdataid = {$v['id']} and modeltype = 'album' and isjump = 'NO' and `status` = 'yes'";
			$tmpResult = $this->query($sql);
			if(!empty($tmpResult)){
				$result[$k]['rid'] = $tmpResult[0]['id'];
				$result[$k]['requestpath'] = $tmpResult[0]['requestpath'];
			}
			$sql = "select * from category_mapping as cm left join category as c on cm.categoryid = c.id where cm.datatype = 'album' and optdataid = {$v['id']}";
			$tmpResult = $this->query($sql);
			if(!empty($tmpResult)){
				$result[$k]['displayname'] = $tmpResult[0]['displayname'];
			}
		}
		return $result;
	}
	public function getAlbumListForPageCount($map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['titleOrId'])){
				$titleOrId = $map['titleOrId'];
				$where .= "and (a.id = '{$titleOrId}' or a.`title` LIKE '%{$titleOrId}%') ";
			}
			if(isset($map['albumsource'])){
				$albumsource = $map['albumsource'];
				$where .= "and a.albumsource = '{$albumsource}' ";
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
		$sql = "select * from album as a {$where} {$order}";
		$result = $this->query($sql);
		$result = count($result);
		return $result;
	}
}