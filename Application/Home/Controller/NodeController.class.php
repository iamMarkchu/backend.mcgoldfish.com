<?php
namespace Home\Controller;
use Think\Controller;
class NodeController extends CommonController {
    public function index(){
        $this->assign('isEcharts',"1");
        $this->display();
    }
    public function insert(){
    	$node = D('node');
    	$node->create();
    	$node->addtime = date("Y-m-d H:i:s");
    	$node->add();
    	$this->success("添加成功","index");
    }
    public function _before_add(){
        $this->assign('isSelect2',1);
    }
}