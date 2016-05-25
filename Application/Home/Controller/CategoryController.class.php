<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends CommonController {
	public function _before_index(){
        //定义需要引入的page level js css
    }
    public function _before_add(){
        
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$map = array();
    	$article = D('category');
    	$result = $article->limit($start,$length)->select();
    	$count = $article->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
        $article = D('category');
        $data['displayname'] = $_POST['displayname'];
        $data['parentcategoryid'] = $_POST['parentcategoryid'];
        $data['displayorder'] = $_POST['displayorder'];
        $data['addtime'] = date("Y-m-d H:i:s");
        $article->create($data);
        $flag = $article->add();
        if($flag){
            $requestPath = "/category/{$flag}.html";
            $url = D('rewrite_url');
            $urlData['RequestPath'] = $requestPath;
            $urlData['ModelType'] = "类别";
            $urlData['OptDataId'] = $flag;
            $urlData['IsJump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            $this->success("添加成功","/Home/Category/index");
        }
        
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('类别不存在!','/Home/Category/index');
        $categoryid = $_REQUEST['id'];
        $category = D('category');
        $sql = "select * from category where id = {$categoryid}";
        $result = $category->query($sql);
        $this->assign('result',$result[0]);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('类别不存在!','/Home/Category/index');
        $categoryid = $_REQUEST['id'];
        $category = D('category');
        $data = $category->where($categoryid)->find();
        $data['displayname'] = $_POST['displayname'];
        $data['parentcategoryid'] = $_POST['parentcategoryid'];
        $data['displayorder'] = $_POST['displayorder'];
        $category->create($data);
        $category->save();        
        $this->success("编辑成功","/Home/Category/index");
    }
}