<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
   	public function _before_index(){
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
        session('userSearch','');
    	$searchArray = session('userSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$article = D('user');
    	$result = $article->where($map)->order($order)->limit($start,$length)->select();
    	$count = $article->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
    	$user = D('user');
    	$user->create();
    	$user->password = md5("123456");
    	$user->addtime = date("Y-m-d H:i:s");
    	$user->add();
    	$this->success("添加成功","/Home/User/index");
    }
}