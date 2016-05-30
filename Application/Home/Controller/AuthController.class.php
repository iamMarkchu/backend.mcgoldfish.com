<?php
namespace Home\Controller;
use Think\Controller;
class AuthController extends CommonController {
   	public function _before_index(){
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
        session('roleSearch','');
    	$searchArray = session('roleSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$article = D('role');
    	$result = $article->where($map)->order($order)->limit($start,$length)->select();
    	$count = $article->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
    	$role = D('role');
    	$role->create();
    	$role->addtime = date("Y-m-d H:i:s");
        $role->pid = 0;
    	$role->add();
    	$this->success("添加成功","/Home/Auth/index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','/Home/Auth/index');
        $roleid = $_REQUEST['id'];
        $role = D('role');
        $result = $role->getById($roleid);
        //变量传到前台
        $this->assign('result',$result);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','/Home/Auth/index');
        $role = D('role');
        $role->create();
        $role->save();
        $this->success("编辑成功","/Home/Auth/index");
    }
}