<?php
namespace Home\Controller;
use Think\Controller;
use Common\Util\BootstrapPage;
class NodeController extends CommonController {
    public function index(){
        $node = D('node');
        $count = $node->count();
        $page = new BootstrapPage($count, 10);
        $show = $page->show();
        $list = $node->where($maps)->order('level,sort')->limit($page->firstRow. ','. $page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->display();
    }
    public function insert(){
    	$node = D('node');
    	if(!$node->create())
        {
            $this->error($node->getDbError());
        }else{
            $node->created_at = date("Y-m-d H:i:s");
            $node->add();
            $this->success("添加成功","index");
        }
    }
    public function edit()
    {
        if(!I('get.id', 0, 'intval')) $this->error('不存在节点');
        $id = I('get.id');
        $node = D('node');
        $result = $node->find($id);
        $level = $result['level'];
        $parent_list = $node->field(['id','name'])->where(['status'=> 'active', 'level' => $level-1])->select();
        if(empty($parent_list)) $parent_list[] = ['id' => 0, 'name' => '无'];
        $this->assign('result', $result);
        $this->assign('parent_list', $parent_list);
        $this->display();
    }
    public function update()
    {
        if(!I('post.id', 0, 'intval')) $this->error('不存在节点');
        $id = I('post.id');
        $node = D('node');
        if(!$node->create())
        {
            $this->error($node->getDbError());
        }else{
            $node->save();
            $this->success('更新节点成功!', U('node/index'));
        }
    }
    public function ajaxGetParentNode(){
        $level = I('post.level', 1, 'intval');
        $node = D('node');
        $list = $node->field(['id','name'])->where(['status'=> 'active', 'level' => $level-1])->select();
        $this->ajaxReturn($list, 'JSON');
    }
}