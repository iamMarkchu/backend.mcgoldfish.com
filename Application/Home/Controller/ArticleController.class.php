<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {
	public function _before_index(){
        $searchArray = session('articleSearch');
        $this->assign('searchArray',$searchArray);
        //定义需要引入的page level js css
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
    }
    public function _before_add(){
        $category = D('category');
        $tag = D('tag');
        $allCateInfo =$category->getAllCategory();
        $allTagInfo = $tag->getAllTag();
        $this->assign('allTagInfo',$allTagInfo);
        $this->assign('allCateInfo',$allCateInfo);
        $this->assign('isSelect2',1);
        //$this->assign('isEditor',1);
        $this->assign('isUeditor',1);
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('articleSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$article = D('article');
    	$result = $article->getArticleListForPage($start,$length,$map,$order);
    	$count = $article->getArticleListForPageCount($map,$order);
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
        $article->addeditor = $_SESSION['loginUserName'];
        $article->tip = C('NEW_ARTICLE_MESSAGE');
        $article->addtime = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/article/';
            $imgFile = ImgUpload($path);
            $article->image = $imgFile['savepath'].$imgFile['savename'];
        }
        $articleid = $article->add();
        if($articleid){
            //添加url信息
            $url = D('rewrite_url');
            $requestPath = "/article/{$articleid}.html";
            $urlData['requestpath'] = isset($_POST['requestPath'])?$_POST['requestPath']:$requestPath;
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
            //添加标签信息
            if(isset($_POST['tag_multi_select2'])){
                $tagMapping = D('tagMapping');
                $tagMapping->create();
                foreach ($_POST['tag_multi_select2'] as $k => $v) {
                    $data = array();
                    $data['optdataid'] = $articleid;
                    $data['datatype'] = 'article';
                    if($k == 0){
                        $data['isprimary'] = 'yes';
                    }else{
                        $data['isprimary'] = 'no';
                    }
                    $data['addtime'] = date("Y-m-d H:i:s");
                    $data['tagid'] = $v;
                    $tagMapping->add($data);
                }
            }
        }
        $this->success("添加成功","index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $result = $article->getById($articleid);
        $category = D('category');
        $allCateInfo =$category->getAllCategory();
        $cateInfo = $category->getCategoryByIdAndType($articleid);
        $tag = D('tag');
        $allTagInfo = $tag->getAllTag();
        $tagInfo = $tag->getTagByIdAndType($articleid);
        foreach ($allTagInfo as $k => $v) {
            if(in_array($v['id'],$tagInfo)) $allTagInfo[$k]['selected'] = '1';
            else $allTagInfo[$k]['selected'] = '0';
            
        }
        $pageMeta = D('page_meta');
        $where = "optdataid = {$articleid} and `status` = 'yes' and modeltype='article'";
        $pageMetaInfo = $pageMeta->where($where)->find();
        $url = D('rewrite_url');
        $urlInfo = $url->where($where)->find();
        //变量传到前台
        $this->assign('result',$result);
        $this->assign('categoryid',$cateInfo['categoryid']);
        $this->assign('allCateInfo',$allCateInfo);
        $this->assign('allTagInfo',$allTagInfo);
        $this->assign('pageMetaInfo',$pageMetaInfo);
        $this->assign('urlInfo',$urlInfo);
        //加载插件
        //$this->assign('isEditor',1);
        $this->assign('isUeditor',1);
        $this->assign('isSelect2',1);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $data = $article->find($articleid);
        $data['title'] = $_POST['title'];
        $data['pageh1'] = $_POST['pageh1'];
        $data['articlesource'] = $_POST['articlesource'];
        $data['content'] = addslashes($_POST['content']);
        $data['maintainorder'] = $_POST['maintainorder'];
        $data['articlesource'] = $_POST['articlesource'];
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/article/';
            $imgFile = ImgUpload($path);
            $data['image'] = $imgFile['savename'];
        }
        $article->create($data);
        $article->save();
        //url信息
        $url = D('rewrite_url');
        
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
        //tag处理,删除原有标签，保存post过来的标签
        $tagMapping = D('tagMapping');
        $tagMappingData = $tagMapping->where("optdataid = {$articleid} and datatype = 'article'")->find();
        if(!empty($tagMappingData)){
            foreach ($tagMappingData as $k => $v) {
                $tagMapping->delete($v['id']);
            }
        }
        if(isset($_POST['tag_multi_select2'])){
            $tagMapping->create();
            foreach ($_POST['tag_multi_select2'] as $k => $v) {
                $data = array();
                $data['optdataid'] = $articleid;
                $data['datatype'] = 'article';
                if($k == 0){
                    $data['isprimary'] = 'yes';
                }else{
                    $data['isprimary'] = 'no';
                }
                $data['addtime'] = date("Y-m-d H:i:s");
                $data['tagid'] = $v;
                $tagMapping->add($data);
            }
        }
        $this->success("编辑成功","index");
    }
    public function btn_Search(){
        session('articleSearch','');
        //组成查询及排序数组
        $searchArray = array();
        if(isset($_POST['titleOrId'])){
            $searchArray['where']['titleOrId'] = $_POST['titleOrId'];
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
    public function publish(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $articleid = $_REQUEST['id'];
        $article = D('article');
        $data['id'] = $articleid;
        $data['status'] = 'active';
        $article->create($data);
        $result = $article->save();
        if($result){
            $this->success("发布成功!");
        }else{
            $this->success("发布失败!");
        }
    }
}