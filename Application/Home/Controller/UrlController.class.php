<?php
namespace Home\Controller;
use Think\Controller;
class UrlController extends CommonController {
	public function _before_index(){
        //定义需要引入的page level js css
    }
    public function _before_add(){
        $this->assign('isEditor',1);
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$map = array();
    	$article = D('rewrite_url');
    	$result = $article->limit($start,$length)->select();
    	$count = $article->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
        $article = D('rewrite_url');
        $article->create();
        $a = $article->add();        
        $this->success("添加成功","index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('链接不存在!','index');
        $urlid = $_REQUEST['id'];
        $url = D('rewrite_url');
        $result = $url->getById($urlid);
        $this->assign('result',$result);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('链接不存在!','index');
        $urlid = $_REQUEST['id'];
        $url = D('rewrite_url');
        $url->create();
        $url->save();        
        $this->success("编辑成功","index");
    }
}