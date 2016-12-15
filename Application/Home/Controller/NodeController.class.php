<?php
namespace Home\Controller;
use Think\Controller;
class NodeController extends CommonController {
    public function index(){
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
    public function ajaxGetNode(){
        $nodelist = [];
        $nodelist = $this->getNode();
        return $this->ajaxReturn($nodelist);
    }
    public function getNode($startPid=0){
        $tmpArr = [];
        $node = D('node');
        $tmp = $node->field('id as value,name')->where('pid = '.$startPid)->select();
        if(empty($tmp)) return [];
        foreach ($tmp as $k => $v) {
            $tmpArr[$k] = $v;
            $tmpArr[$k]['children'] = $this->getNode($v['value']);
        }
        return $tmpArr;
    }
}