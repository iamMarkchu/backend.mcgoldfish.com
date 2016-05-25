<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {
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
    	$article = D('article');
    	$result = $article->limit($start,$length)->select();
    	$count = $article->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
        $article = D('article');
        $articleData = $article->create();
        $articleData->content = addslashes($_POST['content']);
        $articleData->addeditor = "admin";
        $articleData->tip = "新文章";
        $articleData->addtime = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $imgFile = $this->upload();
            $articleData->image = $imgFile['savename'];
        }
        $articleid = $article->add();
        if($articleid){
            //添加url信息
            $url = D('rewrite_url');
            $requestPath = "/article/{$flag}.html";
            $urlData['RequestPath'] = $requestPath;
            $urlData['ModelType'] = "文章";
            $urlData['OptDataId'] = $articleid;
            $urlData['IsJump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            //添加meta信息
            $pageMeta = D('page_meta');
            $pageMetaData = $pageMeta->create();
            $pageMetaData->optdataid = $articleid;
            $pageMeta->add();
        }
        $this->success("添加成功","/Home/Article/index");
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
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath  =      C('IMG_SAVE_PATH'); // 设置附件上传目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return false;
        }else{// 上传成功
            return $info;
        }
    }
}