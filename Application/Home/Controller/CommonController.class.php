<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function index(){
        $blockName = CONTROLLER_NAME."|".__FUNCTION__;
        $this->assign("blockName",$blockName);
        $this->display();
    }
    public function add(){
        $blockName = CONTROLLER_NAME."|".__FUNCTION__;
        $this->assign("blockName",$blockName);
        $this->display();
    }
    public function _initialize(){
        $r = new \Org\Util\Rbac();
        // 用户权限检查
        if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
            if (! $r->AccessDecision ()) {
                //检查认证识别号
                if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
                    $this->redirect('Public/login');
                    return;
                    if ($this->isAjax()){ // zhanghuihua@msn.com
                            $this->ajaxReturn(true, "", 301);
                    } else {
                            //跳转到认证网关
                            redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
                    }
            	}
                // 没有权限 抛出错误
                if (C ( 'RBAC_ERROR_PAGE' )) {
                        // 定义权限错误页面
                        redirect ( C ( 'RBAC_ERROR_PAGE' ) );
                } else {
                        if (C ( 'GUEST_AUTH_ON' )) {
                                $this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
                        }
                        // 提示错误信息
                        $this->error ( L ( '_VALID_ACCESS_' ) );
                }
            }
        }
        $map['name'] = CONTROLLER_NAME;
        $controllerInfo = D('node')->where($map)->find();
        $this->assign('conInfo',$controllerInfo);
    }
    public function delete() {
        //删除指定记录
        $name=CONTROLLER_NAME;
        $model = M ($name);
        if (! empty ( $model )) {
            $pk = $model->getPk ();
            $id = $_REQUEST [$pk];
            if (isset ( $id )) {
                $condition = array ($pk => array ('in', explode ( ',', $id ) ) );
                $list=$model->where ( $condition )->setField ( 'status', "deleted" );
                if ($list!==false) {
                    if(IS_AJAX){
                        echo "1";
                    }else{
                        $this->success ('删除成功！' );
                    }
                } else {
                    if(IS_AJAX){
                        echo "0";
                    }else{
                        $this->error ('删除失败！');
                    }
                }
            } else {
                if(IS_AJAX){
                    echo "-1";
                }else{
                    $this->error ( '非法操作' );
                }
            }
        }
    }
    public function foreverdelete() {
        //删除指定记录
        $model = D (CONTROLLER_NAME);
        if (! empty ( $model )) {
            $pk = $model->getPk ();
            $id = $_REQUEST [$pk];
            if (isset ( $id )) {
                $condition = array ($pk => array ('in', explode ( ',', $id ) ) );
                if (false !== $model->where ( $condition )->delete ()) {
                    //echo $model->getlastsql();
                    $this->success ('删除成功！');
                } else {
                    $this->error ('删除失败！');
                }
            } else {
                $this->error ( '非法操作' );
            }
        }
        $this->forward ();
    }
    public function resume() {
        //恢复指定记录
        $model = D (CONTROLLER_NAME);
        $id = $_REQUEST[$model->getPk ()];
        $condition = array ($pk => array ('in', $id ) );
        if (false !== $model->resume ( $condition )) {
            echo "1";
        } else {
            echo "0";
        }
    }
    public function edit(){
        $model = D(CONTROLLER_NAME);
        $id = $_REQUEST[$model->getPk()];
        $result = $model->getById($id);
        $this->assign('result',$result);
        $this->display();
    }
    function update() {
        $model = D(CONTROLLER_NAME);
        if (false === $model->create ()) {
            $this->error ( $model->getError () );
        }
        $list=$model->save ();
        if (false !== $list) {
            $this->success ('编辑成功!','index');
        } else {
            $this->error ('编辑失败!');
        }
    }
    function insert() {
        $model = D (CONTROLLER_NAME);
        if (false === $model->create ()) {
            $this->error ( $model->getError () );
        }
        //保存当前数据对象
        $list=$model->add ();
        if ($list!==false) { 
            if(IS_AJAX){
                echo "1";
            }else{
                $this->success ('新增成功!','index');
            }
        } else {
            if(IS_AJAX){
                echo "0";
            }else{
                $this->error ('新增失败!');
            }
        }
    }
    public function QueryData(){
        $start = $_POST['start'];
        $length = $_POST['length'];
        $map = array();
        $model = D(CONTROLLER_NAME);
        $searchArray = session(CONTROLLER_NAME.'Search');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
        $result = $model->limit($start,$length)->select();
        $count = $model->count();
        $jsonBack = array();
        $jsonBack['data'] = $result;
        $jsonBack['recordsFiltered'] = $count;
        $jsonBack['recordsTotal'] = $count;
        $this->ajaxReturn($jsonBack);
    }
}