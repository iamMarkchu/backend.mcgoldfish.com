<?php 
namespace Home\Model;
use Think\Model;
class NodeModel extends Model {
	public function getAllNode(){
		$sql = "select * from node where level = 1";
		$levelOneNode = $this->query($sql);
		foreach ($levelOneNode as $k => $v) {
			$sql = "select * from node where level = 2 and pid = {$v['id']} and name != 'Public'";
			$levelTwoNode = $this->query($sql);
			foreach ($levelTwoNode as $kk => $vv) {
				$sql = "select * from node where level = 3 and pid = {$vv['id']}";
				$levelThreeNode = $this->query($sql);
				$levelTwoNode[$kk]['child'] = $levelThreeNode;
				//读取公共方法
				$sql = "select * from node where pid = 8 and level = 3";
				$publicNode = $this->query($sql);
				$levelTwoNode[$kk]['child'] = $publicNode;
			}
			$levelOneNode[$k]['child'] = $levelTwoNode;
		}
		return $levelOneNode;
	}
}