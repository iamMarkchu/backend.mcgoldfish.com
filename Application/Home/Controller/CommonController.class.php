<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class CommonController extends Controller {
    public function _initialize(){
        // Rbac::checkLogin();
        // if(!Rbac::AccessDecision())
        // {
        //     $this->error('您没有权限');
        // }
    }

    public function index(){
        $this->display();
    }

    public function add(){
        $this->display();
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
        $pk = $model->getPk ();
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
                $this->ajaxReturn($model->find($list));
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
        $result = $model->where($map)->order($order)->limit($start,$length)->select();
        $count = $model->where($map)->order($order)->count();
        $jsonBack = array();
        $jsonBack['data'] = $result;
        $jsonBack['recordsFiltered'] = $count;
        $jsonBack['recordsTotal'] = $count;
        $this->ajaxReturn($jsonBack);
    }
}