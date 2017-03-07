<?php
namespace Home\Controller;
use Common\Util\BootstrapPage;
class UserController extends CommonController {
   	public function index()
    {
        $user = D('user');
        $count = $user->count();
        $page = new BootstrapPage($count, 10);
        $show = $page->show();
        $list = $user->field('user.*,role.`name` as group_name')->join('LEFT JOIN role_user ON user.id = role_user.user_id ')->join('LEFT JOIN role ON role_user.role_id = role.id ')->where($maps)->order('id')->limit($page->firstRow. ','. $page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->display();
    }
    public function add(){
        $role = D('role');
        $allRoleInfo = $role->where(['status' => 'active'])->select();
        $this->assign('allRoleInfo',$allRoleInfo);
        $this->display();
    }
    public function insert(){
    	$user = D('user');
    	if(!$user->create())
        {
            $this->error($user->getError());
        }else{
            //默认密码
            $user->password = md5('123456');
            $user->created_at = date('Y-m-d H:i:s');
            $user->remark = '新用户待激活';
            $userid = $user->add();
            if($userid){
                $roleUser = D('role_user');
                if($roleUser->create())
                {
                    $roleUser->user_id = $userid;
                    $roleUser->add();
                    $this->success('添加成功','index');
                }else{
                    $this->error('组别指定失败，用户已创建', 'index');
                }
            }else{
                $this->error('新增失败');
            }
            
        }
    }
    public function edit(){
        if(!I('get.id', 0, 'intval')) $this->error('用户不存在!', U('/user/index'));
        else $userid = I('get.id', 0, 'intval');      
        $user = D('user');
        $result = $user->getById($userid);
        $role = D('role');
        $allRoleInfo =$role->where(['status' => 'active'])->select();
        $roleInfo = $user->getUserRole($userid);

        $this->assign('result',$result);
        $this->assign('allRoleInfo',$allRoleInfo);
        $this->assign('roleInfo',$roleInfo[0]);
        $this->display();
    }
    public function update(){
        if(!I('post.id', 0, 'intval')) $this->error('用户不存在!', U('/user/index'));
        else $userid = I('post.id', 0, 'intval');      
        $user = D('user');
        if($user->create())
        {
            $user->save();
            $roleUser = D('role_user');
            $roleUser->where(['user_id' => $userid])->delete();
            $roleUser->create();
            $roleUser->user_id = $userid;
            $roleUser->add();
            $this->success('编辑成功', U('/user/index'));
        }else{
            $this->error('更新失败');
        }
    }
    public function delete()
    {
       if(!I('get.id', 0, 'intval')) $this->error('用户不存在!', U('/user/index'));
        else $userid = I('get.id', 0, 'intval');  
        $user = D('user');
        $user->delete($userid);
        $this->success('删除成功!');
    }
}