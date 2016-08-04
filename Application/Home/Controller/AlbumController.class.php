<?php
namespace Home\Controller;
use Think\Controller;
class AlbumController extends CommonController {
	public function _before_index(){
        $searchArray = session('albumSearch');
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
        //$this->assign('isFileUpload',1);
        $this->assign('isSelect2',1);
        $this->assign('isUeditor',1);
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('albumSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
        $adminFlag = session('administrator');
        if(!isset($adminFlag)) $map['addeditor'] = session('loginUserName');
    	$album = D('album');
    	$result = $album->getAlbumListForPage($start,$length,$map,$order);
    	$count = $album->getAlbumListForPageCount($map,$order);
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
        $album = D('album');
        $album->create();
        $album->content = addslashes(processImgToTopDomain($_POST['content']));
        $album->addeditor = $_SESSION['loginUserName'];
        $album->tip = C('NEW_ARTICLE_MESSAGE');
        $album->addtime = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/album/';
            $imgFile = ImgUpload($path);
            $album->image = $imgFile['savepath'].$imgFile['savename'];
        }
        $albumid = $album->add();
        if($albumid){
            //添加url信息
            $url = D('rewrite_url');
            $requestPath = "/album/{$albumid}.html";
            $urlData['requestpath'] = !empty($_POST['requestPath'])?$_POST['requestPath']:$requestPath;
            $urlData['modeltype'] = "album";
            $urlData['optdataid'] = $albumid;
            $urlData['isjump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            //添加meta信息
            $pageMeta = D('page_meta');
            $pageMeta->create();
            $pageMeta->optdataid = $albumid;
            if(!empty($pageMeta->pagetitle)) $pageMeta->add();
            //添加分类信息
            $cateogyMapping = D('category_mapping');
            $cateogyMapping->create();
            $cateogyMapping->datatype = 'album';
            $cateogyMapping->optdataid = $albumid;
            $cateogyMapping->isprimary = 'yes';
            $cateogyMapping->addtime = date("Y-m-d H:i:s");
            $cateogyMapping->add();
            //添加标签信息
            if(isset($_POST['tag_multi_select2'])){
                $tagMapping = D('tagMapping');
                $tagMapping->create();
                foreach ($_POST['tag_multi_select2'] as $k => $v) {
                    $data = array();
                    $data['optdataid'] = $albumid;
                    $data['datatype'] = 'album';
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
        $key = session(C('USER_AUTH_KEY'))."_";
        deleteFromCache($key);
        $this->success("添加成功","index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $albumid = $_REQUEST['id'];
        $album = D('album');
        $result = $album->getById($albumid);
        $category = D('category');
        $allCateInfo =$category->getAllCategory();
        $cateInfo = $category->getCategoryByIdAndType($albumid,'album');
        $tag = D('tag');
        $allTagInfo = $tag->getAllTag();
        $tagInfo = $tag->getTagByIdAndType($albumid,'album');
        foreach ($allTagInfo as $k => $v) {
            if(in_array($v['id'],$tagInfo)) $allTagInfo[$k]['selected'] = '1';
            else $allTagInfo[$k]['selected'] = '0';
            
        }
        $pageMeta = D('page_meta');
        $where = "optdataid = {$albumid} and `status` = 'yes' and modeltype='album'";
        $pageMetaInfo = $pageMeta->where($where)->find();
        $url = D('rewrite_url');
        $urlInfo = $url->where($where." and isJump = 'NO'")->find();
        //变量传到前台
        $this->assign('result',$result);
        $this->assign('categoryid',$cateInfo['categoryid']);
        $this->assign('allCateInfo',$allCateInfo);
        $this->assign('allTagInfo',$allTagInfo);
        $this->assign('pageMetaInfo',$pageMetaInfo);
        $this->assign('urlInfo',$urlInfo);
        //加载插件
        //$this->assign('isEditor',1);
        //$this->assign('isFileUpload',1);
        $this->assign('isUeditor',1);
        $this->assign('isSelect2',1);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $albumid = $_REQUEST['id'];
        $album = D('album');
        $data = $album->find($albumid);
        $data['title'] = ($data['title'] == $_POST['title'])? '':$_POST['title'];
        $data['pageh1'] = ($data['pageh1'] == $_POST['pageh1'])?'':$_POST['pageh1'];
        $data['content'] = ($data['content'] == $_POST['content'])?'':addslashes(processImgToTopDomain($_POST['content']));
        $data['maintainorder'] = ($data['maintainorder'] == $_POST['maintainorder'])?'':$_POST['maintainorder'];
        $data['albumsource'] = ($data['albumsource'] == $_POST['albumsource'])?'':$_POST['albumsource'];
        unset($data['lastupdatetime']);
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/album/';
            $imgFile = ImgUpload($path);
            $data['image'] = $imgFile['savepath'].$imgFile['savename'];
        }
        $data = array_filter($data);
        $album->create($data);
        $album->save();
        //url信息
        if(!empty($_POST['requestPath'])){
            $url = D('rewrite_url');
            $urlWhere = "optdataid = {$albumid} and modeltype = 'album' and isJump = 'NO' and `status` = 'yes'";
            $oldUrlInfo = $url->where($urlWhere)->find();
            if(!empty($oldUrlInfo) && $oldUrlInfo['requestpath'] != $_POST['requestPath']){
                $newUrlInfo['requestpath'] = $_POST['requestPath'];
                $newUrlInfo['modeltype'] = 'album';
                $newUrlInfo['optdataid'] = $albumid;
                $newUrlInfo['isjump'] = 'NO';
                $newUrlInfo['status'] = 'yes';
                $newUrlId = $url->add($newUrlInfo);
                if($newUrlId){
                    $oldUrlInfo['isjump'] = '301';
                    $oldUrlInfo['jumprewriteurlid'] = $newUrlId;
                    $url->save($oldUrlInfo);
                }
            }elseif(empty($oldUrlInfo)){
                $newUrlInfo['requestpath'] = $_POST['requestPath'];
                $newUrlInfo['modeltype'] = 'album';
                $newUrlInfo['optdataid'] = $albumid;
                $newUrlInfo['isjump'] = 'NO';
                $newUrlInfo['status'] = 'yes';
                $newUrlId = $url->add($newUrlInfo);
            }
        }
        //保存category信息(逻辑删除原有category_mapping然后添加新category_mapping)
        //添加分类信息
        $cateogyMapping = D('category_mapping');
        $cateogyMappingData = $cateogyMapping->where("optdataid = {$albumid} and datatype = 'album'")->find();
        $cateogyMapping->create();
        if(!empty($cateogyMappingData)){
            $cateogyMappingData['categoryid'] = $_POST['categoryid'];
            $cateogyMapping->save($cateogyMappingData);
        }else{
            $cateogyMapping->datatype = 'album';
            $cateogyMapping->optdataid = $albumid;
            $cateogyMapping->isprimary = 'yes';
            $cateogyMapping->addtime = date("Y-m-d H:i:s");
            $cateogyMapping->id = '';
            $cateogyMapping->add();   
        }
        //tag处理,删除原有标签，保存post过来的标签
        $tagMapping = D('tagMapping');
        $tagMappingData = $tagMapping->where("optdataid = {$albumid} and datatype = 'album'")->select();
        if(!empty($tagMappingData)){
            foreach ($tagMappingData as $k => $v) {
                $tagMapping->delete($v['id']);
            }
        }
        if(isset($_POST['tag_multi_select2'])){
            $tagMapping->create();
            foreach ($_POST['tag_multi_select2'] as $k => $v) {
                $data = array();
                $data['optdataid'] = $albumid;
                $data['datatype'] = 'album';
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
        session('albumSearch','');
        //组成查询及排序数组
        $searchArray = array();
        if(isset($_POST['titleOrId'])){
            $searchArray['where']['titleOrId'] = $_POST['titleOrId'];
        }
        if(isset($_POST['selectalbumsource'])){
            $searchArray['where']['albumsource'] = $_POST['selectalbumsource'];
        }
        if(isset($_POST['selectstatus'])){
            $searchArray['where']['status'] = $_POST['selectstatus'];
        }
        if(isset($_POST['selectorderby'])){
            $searchArray['order'] = $_POST['selectorderby'];
        }
        session('albumSearch',$searchArray);
        echo "1";
    }
    public function publish(){
        if(!isset($_REQUEST['id'])) $this->error('文章不存在!','index');
        $albumid = $_REQUEST['id'];
        $album = D('album');
        $data['id'] = $albumid;
        $data['status'] = 'active';
        $album->create($data);
        $result = $album->save();
        if($result){
            $this->success("发布成功!");
        }else{
            $this->success("发布失败!");
        }
    }
    public function saveTmpContetntToCache(){
        if(!IS_AJAX) echo false;
        if(!isset($_POST['contentHtml'])) echo "1";
        if(isset($_POST['albumid'])) $albumid = $_POST['albumid'];
        $key = session(C('USER_AUTH_KEY'))."_".$albumid;
        $value = $_POST['contentHtml'];
        saveToMemcache($key,$value);
        echo $key;
    }
    public function getTmpContentFromCache(){
        //if(!IS_AJAX) echo false;
        if(isset($_POST['albumid'])) $albumid = $_POST['albumid'];
        $key = session(C('USER_AUTH_KEY'))."_".$albumid;
        echo getFromMemcache($key);
    }
}