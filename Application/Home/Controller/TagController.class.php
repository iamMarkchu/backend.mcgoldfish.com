<?php
namespace Home\Controller;
use \Common\Util\BootstrapPage;
class TagController extends CommonController {
    public function index()
    {
        $map = [];
        $tag = D('tag');
        $count = $tag->where($map)->count();
        $page = new BootstrapPage($count, 10);
        foreach($map as $k => $v) {
            $page->parameter[$k] = urlencode($v);
        }
        $show = $page->show();
        $result = $tag->where($map)->order('created_at')->limit($page->firstRow. ','. $page->listRows)->select();
        $this->assign('result', $result);
        $this->assign('show', $show);
        $this->display();
    }
    public function add()
    {
        $this->display();
    }
    public function insert()
    {
        $tag = D('tag');
        if(!$tag->create())
        {
            $this->error($tag->getDbError());
        }else{
            $tag->created_at = date('Y-m-d H:i:s');
            $tag->updated_at = date('Y-m-d H:i:s');
            $tag->add();
            $this->success('添加成功', U('tag/index'));
        }
    }

    public function edit()
    {
        if(!I('get.id', 0)) return $this->error('标签不存在', U('tag/index'));
        $id = I('get.id');
        $tag = D('tag');
        $result = $tag->find($id);
        $this->assign('result', $result);
        $this->display();
    }
    public function update()
    {
        if(!I('post.id', 0)) return $this->error('标签不存在', U('tag/index'));
        $id = I('post.id');
        $tag = D('tag');
        if(!$tag->create())
        {
            $this->error($tag->getDbError());
        }else{
            $tag->updated_at = date('Y-m-d H:i:s');
            $tag->save();
            $this->success('更新标签成功!', U('tag/index'));
        }
    }

    public function delete()
    {
        if(!I('get.id', 0)) return $this->error('标签不存在', U('tag/index'));
        $id = I('get.id');
        $tag = D('tag');
        if($tag->delete($id))
        {
            $this->success('删除成功!', U('tag/index'));
        }else{
            $this->error('删除失败!');
        }
    }
}