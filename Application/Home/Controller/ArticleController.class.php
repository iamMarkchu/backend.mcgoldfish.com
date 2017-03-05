<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {
	public function _before_index(){
        $searchArray = session('articleSearch');
        $conditionArray = [
            'articleSource' => ['原创', '转载'],
            'status' => ['active', 'deleted', 'republish'],
            'order' => ['id desc', 'clickcount', 'clickcount desc', 'maintainorder', 'maintainorder desc'],
        ];
        $this->assign('searchArray',$searchArray);
        $this->assign('$conditionArray',$conditionArray);
    }
    public function _before_add(){
        $category = D('category');
        $tag = D('tag');
        $allCateInfo =$category->getAllCategory();
        $allTagInfo = $tag->getAllTag();
        if(isset($_GET['type']) && $_GET['type'] == 'markdown') $this->assign('isMarkDown',1);
        $this->assign('allTagInfo',$allTagInfo);
        $this->assign('allCateInfo',$allCateInfo);
        $this->assign('isUeditor', 1);
        $this->assign('isSelect2', 1);
    }
    public function queryData(){
    	$start = I('post.start', 0);
    	$length = I('post.length', 10);
    	$map = [];
    	$order = '';
    	if(session('?articleSearch.where'))
        {
            $map = session('articleSearch.where');
        }
        if(session('?articleSearch.order'))
        {
            $order = session('articleSearch.order');
        }
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
        if(!empty(I('post.content', '')))
        {
            $article->content = addslashes($_POST['content']);
        }
        $article->addeditor = 'test';
        $article->tip = C('NEW_ARTICLE_MESSAGE');
        $article->addtime = date('Y-m-d H:i:s');
        $articleid = $article->add();
        if($articleid){
            //添加分类信息
            $cateogyMapping = D('category_mapping');
            $cateogyMapping->create();
            $cateogyMapping->datatype = 'article';
            $cateogyMapping->optdataid = $articleid;
            $cateogyMapping->isprimary = 'yes';
            $cateogyMapping->addtime = date('Y-m-d H:i:s');
            $cateogyMapping->add();
            //添加标签信息
            if(!empty(I('post.tag_multi_select2', ''))){
                $tagMapping = D('tagMapping');
                $tagMapping->create();
                foreach (I('post.tag_multi_select2') as $k => $v) {
                    $data = [];
                    $data['optdataid'] = $articleid;
                    $data['datatype'] = 'article';
                    $data['addtime'] = date('Y-m-d H:i:s');
                    $data['tagid'] = $v;
                    $tagMapping->add($data);
                }
            }
        }
        $key = session(C('USER_AUTH_KEY'))."_";
        deleteFromCache($key);
        $this->success("添加成功","index");
    }
    public function edit(){
        if(!I('get.id', 0))
        {
            $this->error('文章不存在!','index');
        }else{
            $id = I('get.id');
            $article = D('article');
            $result = $article->find($id);
            $category = D('category');
            $allCateInfo =$category->getAllCategory();
            $cateInfo = $category->getCategoryByIdAndType($id);
            $tag = D('tag');
            $allTagInfo = $tag->getAllTag();
            $tagInfo = $tag->getTagByIdAndType($id);
            foreach ($allTagInfo as $k => $v) {
                if(in_array($v['id'],$tagInfo)) $allTagInfo[$k]['selected'] = '1';
                else $allTagInfo[$k]['selected'] = '0';
            }
            //变量传到前台
            $this->assign('result',$result);
            $this->assign('categoryid',$cateInfo['categoryid']);
            $this->assign('allCateInfo',$allCateInfo);
            $this->assign('allTagInfo',$allTagInfo);
            $this->display();
        }
    }
    public function update(){
        if(!I('post.id', 0))
        {
            $this->error('文章不存在!','index');
        }else{
            $id = I('post.id');
            $article = D('article');
            $article->create();
            $article->save();
            $cateogyMapping = D('category_mapping');
            $cateogyMappingData = $cateogyMapping->where(['optdataid' => $id, 'datatype' => 'article'])->find();
            $cateogyMapping->create();
            if(!empty($cateogyMappingData)){
                $cateogyMappingData['categoryid'] = I('post.categoryid');
                $cateogyMapping->save($cateogyMappingData);
            }else{
                $cateogyMapping->datatype = 'article';
                $cateogyMapping->optdataid = $id;
                $cateogyMapping->isprimary = 'yes';
                $cateogyMapping->addtime = date('Y-m-d H:i:s');
                $cateogyMapping->add();
            }
            //tag处理,删除原有标签，保存post过来的标签
            $tagMapping = D('tagMapping');
            $tagMappingData = $tagMapping->where(['optdataid' => $id, 'datatype' => 'article'])->select();
            if(!empty($tagMappingData)){
                foreach ($tagMappingData as $k => $v) {
                    $tagMapping->delete($v['id']);
                }
            }
            if(isset($_POST['tag_multi_select2'])){
                $tagMapping->create();
                foreach ($_POST['tag_multi_select2'] as $k => $v) {
                    $data = array();
                    $data['optdataid'] = $id;
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
    }
    public function search(){
        session('articleSearch','');
        //组成查询及排序数组
        $searchArray = [];
        if(!empty(I('post.title', ''))){
            $searchArray['where']['title'] = I('post.title');
        }
        if(!empty(I('post.articleSource', ''))){
            $searchArray['where']['articleSource'] = I('post.articleSource');
        }
        if(!empty(I('post.status', ''))){
            $searchArray['where']['status'] = I('post.status');
        }
        if(!empty(I('post.maintainorder'))){
            $searchArray['order'] = I('post.maintainorder');
        }
        $flag = session('articleSearch',$searchArray);
        $this->ajaxReturn($flag);
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
    public function saveTmpContetntToCache(){
        if(!IS_AJAX) echo false;
        if(!isset($_POST['contentHtml'])) echo "1";
        if(isset($_POST['articleid'])) $articleid = $_POST['articleid'];
        $key = session(C('USER_AUTH_KEY'))."_".$articleid;
        $value = $_POST['contentHtml'];
        saveToMemcache($key,$value);
        echo $key;
    }
    public function getTmpContentFromCache(){
        if(isset($_POST['articleid'])) $articleid = $_POST['articleid'];
        $key = session(C('USER_AUTH_KEY'))."_".$articleid;
        echo getFromMemcache($key);
    }
    public function checkTitleDuplicated(){
        if(!IS_AJAX || !isset($_POST['title'])) echo "-1";
        $title = $_POST['title'];
        $article = D('article');
        $condArray = array('title'=>$title);
        $articleInfo = $article->where($condArray)->find();
        if(!empty($articleInfo)) echo "0";
            else echo "1";
    }
    public function saveImage(){
        if(!empty($_FILES['upload_file']['name'])){
            $path = "/article/";
            $imgFile = ImgUpload($path);
            $jsonBack['success'] = true;
            $jsonBack['msg'] = "上传成功！";
            $jsonBack['file_path'] = "/Public". $imgFile['upload_file']['savepath']. $imgFile['upload_file']['savename'];
            $this->ajaxReturn($jsonBack);
        }
    }

    public function uploadImage()
    {
        if(!empty($_FILES['files']['name'])){
            $path = '/markdown/';
            $imgFile = ImgUploadOne($path, $_FILES['files']);
            //缩略图
            $image = new \Think\Image();
            $imagePath = './Public'. $imgFile['savepath']. $imgFile['savename'];
            $image->open($imagePath);
            $newImagePath = '/Public'. $imgFile['savepath'].'max_w_1000_' .$imgFile['savename'];
            $image->thumb(1000, 1000, 1)->save('.'.$newImagePath);
        }
        if(!empty($_FILES['imageFile']['name']))
        {
            $path = '/article/';
            $imgFile = ImgUploadOne($path, $_FILES['imageFile']);
            $image = new \Think\Image();
            $imagePath = C('ImageDirectoryV2'). $imgFile['savepath']. $imgFile['savename'];
            $image->open($imagePath);
            $newImagePath = C('ImageDirectoryV1'). $imgFile['savepath']. C('ThumbPrefix'). '400_'.$imgFile['savename'];
            $image->thumb(400, 400, 1)->save('.'.$newImagePath);
        }
        $jsonBack['success'] = true;
        $jsonBack['msg'] = "上传成功！";
        $jsonBack['file_path'] = $newImagePath;
        $this->ajaxReturn($jsonBack);
    }

    public function viewMarkdown()
    {
        $parsedown = new \Common\Util\Parsedown();
        //$markdownContent = htmlspecialchars(I('post.content'));
        $htmlContent = $parsedown->text($_POST['content']);
        $jsonBack['htmlContent'] = $htmlContent;
        $this->ajaxReturn($jsonBack);
        $image = new \Think\Image();
    }
}