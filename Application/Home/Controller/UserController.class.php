<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
   	public function _before_index(){
        if(isset($_REQUEST['role_id'])){
            $searchArray = session('userSearch');
            $searchArray['where']['role_id'] = $_REQUEST['role_id'];
            session('userSearch',$searchArray);
            $this->assign('role_id',$_REQUEST['role_id']);
        }
        $role = D('role');
        $allRoleInfo =$role->where("status='active'")->select();
        $this->assign('allRoleInfo',$allRoleInfo);
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function _before_add(){
        $role = D('role');
        $allRoleInfo =$role->where("status='active'")->select();
        $this->assign('allRoleInfo',$allRoleInfo);
        $this->assign('isSelect2',1);
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('userSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$user = D('user');
    	$result = $user->join("role_user on user.id = role_user.user_id")->where($map)->order($order)->limit($start,$length)->select();
    	$count = $user->join("role_user on user.id = role_user.user_id")->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
    	$user = D('user');
    	$user->create();
    	$user->password = md5("123456");
    	$user->addtime = date("Y-m-d H:i:s");
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/user/';
            $imgFile = ImgUpload($path);
            $imageObj = new \Think\Image(); 
            $abPath = C('IMG_SAVE_PATH').$imgFile['savepath'].$imgFile['savename'];
            $imageObj->open($abPath);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $imageObj->thumb(9, 9)->save(C('IMG_SAVE_PATH').'/thumb/'.$imgFile['savename']);
            $user->image = '/thumb/'.$imgFile['savename'];
        }
    	$userid = $user->add();
        if($userid){
            $roleUser = D('role_user');
            $roleUser->create();
            $roleUser->user_id = $userid;
            $roleUser->add();
        }
    	$this->success("添加成功","index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('用户不存在!','index');
        $userid = $_REQUEST['id'];
        $user = D('user');
        $result = $user->getById($userid);
        $role = D('role');
        $allRoleInfo =$role->where("status='active'")->select();
        $sql = "select * from role_user as ru left join role as r on ru.role_id = r.id where ru.user_id = {$userid}";
        $roleInfo = $role->query($sql);
        if(!empty($roleInfo))
            $this->assign('roleInfo',$roleInfo[0]);    
        //变量传到前台
        $this->assign('result',$result);
        $this->assign('allRoleInfo',$allRoleInfo);
        //
        $this->assign('isSelect2',1);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('用户不存在!','index');
        $userid = $_REQUEST['id'];
        $user = D('user');
        $user->create();
        if(!empty($_FILES['imgFile']['name'])){
            $path = '/user/';
            $imgFile = ImgUpload($path);
            $imageObj = new \Think\Image(); 
            $abPath = C('IMG_SAVE_PATH').$imgFile['savepath'].$imgFile['savename'];
            $imageObj->open($abPath);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $imageObj->thumb(20, 20)->save(C('IMG_SAVE_PATH').'/thumb/'.$imgFile['savename']);
            $user->image = '/thumb/'.$imgFile['savename'];
        }
        $user->save();
        $sql = "delete from role_user where user_id = {$userid}";
        $user->execute($sql);
        $roleUser = D('role_user');
        $roleUser->create();
        $roleUser->user_id = $userid;
        $roleUser->add();
        $this->success("编辑成功","/User/index");
    }
    public function btn_Search(){
        session('userSearch','');
        //组成查询及排序数组
        $searchArray = array();
        if(isset($_POST['selectrole'])){
            $searchArray['where']['role_id'] = $_POST['selectrole'];
        }
        if(isset($_POST['selectorderby'])){
            $searchArray['order'] = $_POST['selectorderby'];
        }
        session('userSearch',$searchArray);
        echo "1";
    }
}