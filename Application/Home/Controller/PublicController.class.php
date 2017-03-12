<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class PublicController extends CommonController {
	
	public function login() {
		if(!session('?'.C('USER_AUTH_KEY'))) {
			$isRemmberInfo = cookie('saveUser');
			if(!empty($isRemmberInfo)){
				$this->assign('saveUser', cookie('saveUser'));
				$this->assign('remember', 1);
			}
			$this->assign('isLogin', 1);
			$this->display();
		}else{
			//$oldPage = getFromMemcache(session(C('USER_AUTH_KEY'))."_oldPage");
			if(!empty($oldPage))
				$this->redirect($oldPage);
			else
				$this->redirect('/Index/index');
		}
	}

	public function index() {
		redirect(__APP__);
	}
	
	public function checkLogin() {
		$map['account']	=  I('post.account');
        $map["status"]	=  ['eq','active'];
		//if(!$this->check_verify(I('post.verify')))  $this->error('验证码错误!');
		$r = new Rbac;
        $authInfo = $r->authenticate($map);
        
        if(empty($authInfo)) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != md5($_POST['password']))    $this->error('密码错误！');
            $needToSession = [
            	C('USER_AUTH_KEY') => $authInfo['id'],
            	'loginUserName' => $authInfo['user_name'],
            ];
            session([ 'name' => 'session_id', 'expire' => 86400]);
            foreach ($needToSession as $k => $v) {
            	session($k, $v);
            }
            if($authInfo['account']=='admin')    session(C('ADMIN_AUTH_KEY'), 1);
            if(I('post.remember')){
            	$saveUser = [
            		'account' => I('post.account'),
            		'password' => I('post.password'),
            	];
            	cookie('saveUser',$saveUser);
            }
            $r->saveAccessList();
			$this->success('登录成功');

		}
	}
	public function verify()
    {
        $r = new \Think\Verify();
        $r->__set('length',3);
        $r->__set('useImgBg',true);
        $r->entry();
    }
    public function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}
    	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->success('登出成功',"login");
        }else {
            $this->error('已经登出！',"login");
        }
    }
    public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
	public function changepwd(){
		if(IS_POST){
			$user = D('user');
			$userid = $_POST['userid'];
			$userInfo  = $user->find($userid);
			if(md5($_POST['oldPwd']) != $userInfo['password']) $this->error('密码错误');
			if($_POST['newPwd'] != $_POST['rePwd']) $this->error('两次密码不一致');
			$userInfo['password'] = md5($_POST['newPwd']);
			$user->create($userInfo);
			$user->save();
			$this->success('修改密码成功!');
		}else{
			$this->assign('userid',session(C('USER_AUTH_KEY')));
			$this->display();
		}
	}
	public function register(){
		$user = D('user');
		$user->create();
		$user->account = trim($_POST['account']);
		$user->addtime = date("Y-m-d H:i:s");
		if($_POST['password'] == $_POST['rpassword']){
			$user->password = md5($_POST['password']);
		}
		$user->status = 'inactive';
		$user->remark = '新用户';
		$user->add();
		$this->success('注册成功!等待管理员验证');
	}
	public function menu(){
		//读取数据库模块列表生成菜单项
        $node    =   M("Node");
		$id	=	$node->getField("id");
		$where['level']=2;
		$where['status']=1;
		$where['pid']=$id;
        $list	=	$node->where($where)->field('id,name,title')->order('sort asc')->select();
        $r = new \Org\Util\Rbac();
        $accessList = $r->getAccessList($_SESSION[C('USER_AUTH_KEY')]);
        foreach($list as $key=>$module) {
             if(isset($accessList[strtoupper(MODULE_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                //设置模块访问权限
                $module['access'] =   1;
                $menu[$key]  = $module;
            }
        }
        //缓存菜单访问
       // $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
        session(array('menu'.$_SESSION[C('USER_AUTH_KEY')]=>$menu,'expire'=>72000));
        $this->assign('menu',$menu);;
        $this->display();
	}
	public function insertFcategory(){
		$model = D ('f_category');
		
		$model->create();
		$model->addtime = date('Y-m-d H:i:s');
        //保存当前数据对象
        $list=$model->add ();
        if ($list!==false) { 
            $this->ajaxReturn($model->find($list));
        } else {
            echo "0";
        }
	}
	public function insertAsset(){
		$model = D ('asset_new');
		$model->create();
		$model->order = 99;
        //保存当前数据对象
        $list=$model->add();
        if ($list!==false) { 
            $this->ajaxReturn($model->find($list));
        } else {
            echo "0";
        }
	}
	public function insertBudget(){
		$model = D('budget');
		$map['userid'] = $_POST['userid'];
		$map['yearmonth'] = date('Y-m');
		$budgetInfo = $model->where($map)->find();
		$model->create();
		if(!empty($budgetInfo)){
			$budgetInfo['budget'] = $_POST['budget'];
			$model->save($budgetInfo);
		}else{
			$model->realcost = 0;
			$model->yearmonth = date("Y-m");
			$model->addtime = date("Y-m-d H:i:s");
			$model->add();
		}
	}
	public function builderSth()
	{
		$test = new \Think\Build;
		$test::buildModel('Home', 'User');
	}
}