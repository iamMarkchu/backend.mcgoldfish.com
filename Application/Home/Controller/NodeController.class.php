<?php
namespace Home\Controller;
use Think\Controller;
class NodeController extends CommonController {
   	public function _before_index(){
        if(isset($_GET['pid'])){
            $pid = $_GET['pid'];
        }else{
            $pid = 0;
        }
        $searchArray = session('nodeSearch');
        $searchArray['where']['pid'] = $pid;
        session('nodeSearch',$searchArray);
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('nodeSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$node = D('node');
    	$result = $node->where($map)->order($order)->limit($start,$length)->select();
    	$count = $node->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
    	$node = D('node');
    	$node->create();
    	$node->addtime = date("Y-m-d H:i:s");
    	$node->add();
    	$this->success("添加成功","/Home/Node/index");
    }
    public function _before_add(){
        $this->assign('isSelect2',1);
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','/Home/Auth/index');
        $nodeid = $_REQUEST['id'];
        $node = D('node');
        $result = $node->getById($nodeid);
        //变量传到前台
        $this->assign('result',$result);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','/Home/Auth/index');
        $node = D('node');
        $node->create();
        $node->save();
        $this->success("编辑成功","/Home/Auth/index");
    }
}