<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends CommonController {
	public function _before_index(){
		$this->assign('isTress',1);
		$this->assign('isUeditor',1);	
	}
	public function index(){
		$db = D('');
		$db->db(1,"mysql://mark:vlvsTPeG@192.168.1.10:3306/mark_base");
		//一级
		$sql  = "select id as `value`,displayname as `name` from category where LanguageId = 13 and ParentCategoryId = 0";
		$result = $db->query($sql);
		$categoryArray = array();
		foreach ($result as $k => $v) {
			$tmp = array();
			$sql = "select id as value,displayname as `name` from category where LanguageId = 13 and ParentCategoryId = {$v['value']}";
			$tmp = $db->query($sql);
			foreach ($tmp as $kk => $vv) {
				$tmp_2 = array();
				$sql = "select id as value,displayname as `name` from category where LanguageId = 13 and ParentCategoryId = {$vv['value']}";
				$tmp_2 = $db->query($sql);
				foreach ($tmp_2 as $kkk => $vvv) {
					$tmp_3 = array();
					$sql = "select id as value,displayname as `name` from category where LanguageId = 13 and ParentCategoryId = {$vvv['value']}";
					$tmp_3 = $db->query($sql);
					$tmp_2[$kkk]['children'] = $tmp_3;
				}
				$tmp[$kk]['children'] = $tmp_2;
			}
			$result[$k]['children'] = $tmp;
			$categoryArray[] = $result[$k];
		}
		$json['name'] = 'ssen';
		$json['value'] = '1';
		$json['children'] = $categoryArray;
		//$json['children'] = array('name'=>'123','value'=>'1');
		//$json = '[{name:"ds",value:1,children:[{name:"ssen",value:4},{name:"ssde",value:2},{name:"ssen",value:3}]}]';
		//echo "<pre>";
		$this->ajaxReturn($json);
	}
}