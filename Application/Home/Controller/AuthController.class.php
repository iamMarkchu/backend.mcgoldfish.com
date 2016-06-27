<?php
namespace Home\Controller;
use Think\Controller;
class AuthController extends CommonController {
   	public function _before_index(){
        $this->assign('noAjax',1);
        $this->assign('isSelect2',1);
        //定义需要引入的page level js css
    }
    public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
        session('roleSearch','');
    	$searchArray = session('roleSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$article = D('role');
    	$result = $article->where($map)->order($order)->limit($start,$length)->select();
    	$count = $article->where($map)->count();
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }
    public function insert(){
    	$role = D('role');
    	$role->create();
    	$role->addtime = date("Y-m-d H:i:s");
        $role->pid = 0;
    	$role->add();
    	$this->success("添加成功","index");
    }
    public function edit(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','index');
        $roleid = $_REQUEST['id'];
        $role = D('role');
        $result = $role->getById($roleid);
        //变量传到前台
        $this->assign('result',$result);
        $this->display();
    }
    public function update(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','index');
        $role = D('role');
        $role->create();
        $role->save();
        $this->success("编辑成功","index");
    }
    public function delete(){
        if(!isset($_REQUEST['id'])) $this->error('组别不存在!','index');
        $roleid = $_REQUEST['id'];
        $data['id'] = $roleid;
        $data['status'] = 'inactive';
        $role = D('role');
        $flag = $role->save($data);
        if($flag) $this->success('删除成功');
        else $this->error('删除失败');
    }
    public function giveAuth(){
        $role_id = $_REQUEST['role_id'];
        $node = D('node');
        $nodeInfo = $node->getAllNode();
        $role = D('role');
        $roleInfo = $role->find($role_id);
        $this->assign('roleInfo',$roleInfo);
        $this->assign('nodeInfo',$nodeInfo);
        $this->assign('isMutiSelect',1);
        $this->assign('isSelect2',1);
        $this->display();
    }
    public function getAccess(){
        $role_id = $_POST['role_id'];
        $access = D('access');
        $result = $access->field('node_id')->where('role_id = '.$role_id)->select();
        $accessList = array();
        foreach ($result as $k => $v){
           $accessList[] = $v['node_id']; 
        }
        $this->ajaxReturn($accessList);
    }
    public function insertAuth(){
        $role_id = $_POST['role_id'];
        $access = D('access');
        $access->where('role_id = '.$role_id)->delete();
        $data = array();
        $node_ids = array_unique($_POST['access']);
        $node_ids[] = '8';
        $node_ids[] = '1';
        foreach ($node_ids as $k => $v){
            $tmp['role_id'] = $role_id;
            $tmp['level'] = 0;
            $tmp['node_id'] = $v;
            $data[] = $tmp;
        }
        $access->addAll($data);
        $this->success('授权成功','index');
    }
}