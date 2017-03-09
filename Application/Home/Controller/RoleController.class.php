<?php
namespace Home\Controller;
use Common\Util\BootstrapPage;

class RoleController extends CommonController {
   	public function index(){
        $role = M('role');
        $count = $role->count();
        $page = new BootstrapPage($count, 5);
        $show = $page->show();
        $list = $role->where($maps)->order('id')->limit($page->firstRow. ','. $page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->display();
    }
    public function add()
    {
        $node = D('node');
        $node_list = $node->getAllNode();
        $this->assign('node_list', $node_list);
        $this->display();
    }
    public function insert(){
    	$role = D('role');
    	if(!$role->create())
        {
            $this->error($role->getError());
        }else{
            $role->created_at = date("Y-m-d H:i:s");
            $role->pid = 0;
            $role_id = $role->add();
            if(!empty(I('post.node')))
            {
                $node = I('post.node');
                $insertData = [];
                foreach ($node as $v)
                {
                    $tmp = [];
                    $tmp['role_id'] = $role_id;
                    $tmp['node_id'] = $v;
                    $tmp['level'] = 0;
                    $insertData[] = $tmp;
                }

            }
            $access = D('access');
            $access->addAll($insertData);
            $this->success('添加成功', U('role/index'));
        }
    }
    public function edit(){
        if(!I('get.id', 0, 'intval')) $this->error('组别不存在');
        else  $roleid = I('get.id');
        
        $role = D('role');
        $result = $role->find($roleid);
        $this->assign('result',$result);
        $this->display();
    }
    public function update(){
        if(!I('post.id', 0, 'intval')) $this->error('组别不存在');
        else  $roleid = I('post.id');
        $role = D('role');
        if(!$role->create())
        {
            $this->error($role->getError());
        }else{
            $role->save();
            $this->success('编辑成功', U('role/index'));    
        }
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