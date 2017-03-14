<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {
	public function index(){
        $searchArray = session('articleSearch');
        $conditionArray = [
            'articleSource' => ['原创', '转载'],
            'status' => ['active', 'deleted', 'republish'],
            'order' => ['id desc', 'clickcount', 'clickcount desc', 'maintainorder', 'maintainorder desc'],
        ];
        $this->assign('searchArray',$searchArray);
        $this->assign('conditionArray',$conditionArray);
        $this->display();
    }
    public function add(){
        $category = D('category');
        $tag = D('tag');
        $allCateInfo =$category->getAllCategory();
        $allTagInfo = $tag->getAllTag();
        $this->assign('allTagInfo',$allTagInfo);
        $this->assign('allCateInfo',$allCateInfo);
        $this->display();
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
        if(!$article->create())
        {
            $this->error($article->getDbError());
        }else{
            if(!empty(I('post.content', '')))
            {
                $article->content = addslashes($_POST['content']);
            }
            $article->add_editor = session('?user_name')?session('user_name'):'';
            $article->tip = C('NEW_ARTICLE_MESSAGE');
            $article->created_at = date('Y-m-d H:i:s');
            $articleid = $article->add();
            if($articleid){
                if(!empty(I('post.tag_multi_select2', ''))){
                    $tagMapping = D('tagMapping');
                    $tagMapping->create();
                    foreach (I('post.tag_multi_select2') as $k => $v) {
                        $data = [];
                        $data['tag_id'] = $v;
                        $data['article_id'] = $articleid;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $tagMapping->add($data);
                    }
                }
            }
            $this->success("添加成功","index");
        }

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
            $cateInfo = $category->getCategoryByArticleId($id);
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
        if(!empty(I('post.source', ''))){
            $searchArray['where']['source'] = I('post.source');
        }
        if(!empty(I('post.status', ''))){
            $searchArray['where']['status'] = I('post.status');
        }
        if(!empty(I('post.display_order'))){
            $searchArray['order'] = I('post.display_order');
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

    public function uploadImage()
    {
        if(I('get.name', 'image') == 'image')
        {
            $key = 'imageFile';
            $path = '/article/';
            $width = 400;
        }else{
            $key = 'files';
            $path = '/markdown/';
            $width = 1000;
        }
        if(!empty($_FILES[$key]['name'])){
            $imgFile = ImgUploadOne($path, $_FILES[$key]);
            $image = new \Think\Image();
            $imagePath = C('ImageDirectoryV2'). $imgFile['savepath']. $imgFile['savename'];
            $image->open($imagePath);
            $newImagePath = C('ImageDirectoryV1'). $imgFile['savepath'].'max_w_'. $width.'_' .$imgFile['savename'];
            $image->thumb($width, $width, 1)->save('.'.$newImagePath);
            $jsonBack['success'] = true;
            $jsonBack['msg'] = "上传成功！";
            $jsonBack['file_path'] = $newImagePath;
            $this->ajaxReturn($jsonBack);
        }else{
            $jsonBack['success'] = true;
            $jsonBack['msg'] = "上传成功！";
            $jsonBack['file_path'] = '';
            $this->ajaxReturn($jsonBack);
        }

    }

    public function viewMarkdown()
    {
        $parsedown = new \Common\Util\Parsedown();
        $markdownContent = htmlspecialchars_decode(I('post.content'));
        $htmlContent = $parsedown->text($markdownContent);
        $jsonBack['htmlContent'] = $htmlContent;
        $this->ajaxReturn($jsonBack);
    }
}