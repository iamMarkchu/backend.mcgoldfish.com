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
}