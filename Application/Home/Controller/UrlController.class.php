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
        $this->success("添加成功","/Home/Url/index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','/Home/Article/index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $sql = "select * from article where id = {$articleid}";
        $result = $article->query($sql);
        $this->assign('result',$result[0]);
        $this->assign('isEditor',1);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','/Home/Article/index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $data = $article->where($articleid)->find();
        $data['title'] = $_POST['title'];
        $data['pageh1'] = $_POST['pageh1'];
        $data['articlesource'] = $_POST['articlesource'];
        $data['content'] = addslashes($_POST['content']);
        $data['maintainorder'] = $_POST['maintainorder'];
        $data['articlesource'] = $_POST['articlesource'];
        $data['addeditor'] = "admin";
        $data['tip'] = "新文章";
        $data['addtime'] = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $imgFile = $this->upload();
            $data['image'] = $imgFile['savename'];
        }
        $article->create($data);
        $article->save();        
        $this->success("编辑成功","/Home/Article/index");
    }
}