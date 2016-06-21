<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends CommonController {
    // 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl','Public/login');
			$this->error('没有登录');
		}
	}
	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$isRemmberInfo = cookie('saveUser');
			if(!empty($isRemmberInfo)){
				$this->assign('saveUser',cookie('saveUser'));
				$this->assign('remember',1);
			}
			$this->assign('isLogin',1);
			$this->display();
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index() {
		//如果通过认证跳转到首页
		redirect(__APP__);
	}
	// 登录检测
	public function checkLogin() {
		if(empty($_POST['account'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须！');
		}elseif (empty($_POST['verify'])){
		 	$this->error('验证码必须！');
		}
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['account']	= $_POST['account'];
        $map["status"]	=	array('eq','active');
		if(!$this->check_verify($_POST['verify'])){
			$this->error('验证码错误!');
		}
		$r = new \Org\Util\Rbac();
        $authInfo = $r->authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(empty($authInfo)) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != md5($_POST['password'])) {
            	$this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['loginUserName']		=	$authInfo['nickname'];
            $_SESSION['userImage']		=	$authInfo['image'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
            if($authInfo['account']=='admin') {
            	$_SESSION['administrator']		=	true;
            }
            if(isset($_POST['remember'])){
            	$saveUser['account'] = $_POST['account'];
            	$saveUser['password'] = $_POST['password'];
            	cookie('saveUser',$saveUser);
            } 
            //保存登录信息
			$User	=	M('User');
			$ip		=	get_client_ip();
			$time	=	time();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	$ip;
			$User->save($data);
			// 缓存访问权限
            $r->saveAccessList();
			$this->success('登录成功');

		}
	}
	public function verify()
    {
        $r = new \Think\Verify();
        $r->__set('codeSet','1');
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
}