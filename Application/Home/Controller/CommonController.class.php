<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function index(){
        $blockName = __FUNCTION__." ".CONTROLLER_NAME;
        $this->assign("blockName",$blockName);
        $this->display();
    }
    public function add(){
        $blockName = __FUNCTION__." ".CONTROLLER_NAME;
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
                    $this->success ('删除成功！' );
                } else {
                    $this->error ('删除失败！');
                }
            } else {
                $this->error ( '非法操作' );
            }
        }
    }
    public function foreverdelete() {
        //删除指定记录
        $name=$this->getActionName();
        $model = D ($name);
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
}