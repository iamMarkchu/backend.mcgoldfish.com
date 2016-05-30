<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {
	public function _before_index(){
        $searchArray = session('articleSearch');
        $this->assign('searchArray',$searchArray);
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function _before_add(){
        $category = D('category');
        $AllcateInfo =$category->getAllCategory();
        $this->assign('AllcateInfo',$AllcateInfo);
        $this->assign('isSelect2',1);
        $this->assign('isEditor',1);
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('articleSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$article = D('article');
    	$result = $article->where($map)->order($order)->limit($start,$length)->select();
    	$count = $article->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
        $article = D('article');
        $article->create();
        $article->content = addslashes($_POST['content']);
        $article->addeditor = "admin";
        $article->tip = "新文章";
        $article->addtime = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $imgFile = $this->upload();
            $article->image = $imgFile['savename'];
        }
        $articleid = $article->add();
        if($articleid){
            //添加url信息
            $url = D('rewrite_url');
            $requestPath = "/article/{$articleid}.html";
            $urlData['requestpath'] = $requestPath;
            $urlData['Modeltype'] = "文章";
            $urlData['optdataid'] = $articleid;
            $urlData['isjump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            //添加meta信息
            $pageMeta = D('page_meta');
            $pageMeta->create();
            $pageMeta->optdataid = $articleid;
            if(!empty($pageMeta->pagetitle)) $pageMeta->add();
            //添加分类信息
            $cateogyMapping = D('category_mapping');
            $cateogyMapping->create();
            $cateogyMapping->datatype = 'article';
            $cateogyMapping->optdataid = $articleid;
            $cateogyMapping->isprimary = 'yes';
            $cateogyMapping->addtime = date("Y-m-d H:i:s");
            $cateogyMapping->add();
        }
        $this->success("添加成功","/Home/Article/index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','/Home/Article/index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $result = $article->getById($articleid);
        $category = D('category');
        $AllcateInfo =$category->getAllCategory();
        $cateInfo = $category->getCategoryByIdAndType($articleid);
        $pageMeta = D('page_meta');
        $where = "optdataid = {$articleid} and `status` = 'yes' and modeltype='article'";
        $pageMetaInfo = $pageMeta->where($where)->find();
        //变量传到前台
        $this->assign('result',$result);
        $this->assign('categoryid',$cateInfo['categoryid']);
        $this->assign('AllcateInfo',$AllcateInfo);
        $this->assign('pageMetaInfo',$pageMetaInfo);
        //加载插件
        $this->assign('isEditor',1);
        $this->assign('isSelect2',1);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','/Home/Article/index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $data = $article->where($articleid)->find();
        $data['title'] == $_POST['title'];
        $data['pageh1'] = $_POST['pageh1'];
        $data['articlesource'] = $_POST['articlesource'];
        $data['content'] = addslashes($_POST['content']);
        $data['maintainorder'] = $_POST['maintainorder'];
        $data['articlesource'] = $_POST['articlesource'];
        if(!empty($_FILES['imgFile']['name'])){
            $imgFile = $this->upload();
            $data['image'] = $imgFile['savename'];
        }
        $article->create($data);
        $article->save();
        //保存category信息(逻辑删除原有category_mapping然后添加新category_mapping)
        //添加分类信息
        $cateogyMapping = D('category_mapping');
        $cateogyMappingData = $cateogyMapping->where("optdataid = {$articleid} and datatype = 'article'")->find();
        $cateogyMapping->create();
        if(!empty($cateogyMappingData)){
            $cateogyMappingData['categoryid'] = $_POST['categoryid'];
            $cateogyMapping->save($cateogyMappingData);
        }else{
            $cateogyMapping->datatype = 'article';
            $cateogyMapping->optdataid = $articleid;
            $cateogyMapping->isprimary = 'yes';
            $cateogyMapping->addtime = date("Y-m-d H:i:s");
            $cateogyMapping->add();   
        }
        $this->success("编辑成功","/Home/Article/index");
    }
    public function btn_Search(){
        session('articleSearch','');
        //组成查询及排序数组
        $searchArray = array();
        if(isset($_POST['titleOrId'])){
            $searchArray['where']['_query'] = "title={$_POST['titleOrId']}&id={$_POST['titleOrId']}&_logic=or";
        }
        if(isset($_POST['selectarticlesource'])){
            $searchArray['where']['articlesource'] = $_POST['selectarticlesource'];
        }
        if(isset($_POST['selectstatus'])){
            $searchArray['where']['status'] = $_POST['selectstatus'];
        }
        if(isset($_POST['selectorderby'])){
            $searchArray['order'] = $_POST['selectorderby'];
        }
        session('articleSearch',$searchArray);
        echo "1";
    }
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath  =      C('IMG_SAVE_PATH'); // 设置附件上传目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            //$this->error($upload->getError());
            return false;
        }else{// 上传成功
            return $info['imgFile'];
        }
    }
}