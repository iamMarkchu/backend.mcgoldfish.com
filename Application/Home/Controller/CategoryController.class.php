<?php
namespace Home\Controller;
use \Common\Util\BootstrapPage;
class CategoryController extends CommonController {
    public function index()
    {
        $map = [];
        $category = D('category');
        $count = $category->where($map)->count();
        $page = new BootstrapPage($count, 10);
        foreach($map as $k => $v) {
            $page->parameter[$k] = urlencode($v);
        }
        $show = $page->show();
        $result = $category->where($map)->order('created_at')->limit($page->firstRow. ','. $page->listRows)->select();
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
        $category = D('category');
        if(!$category->create())
        {
            $this->error($category->getDbError());
        }else{
            $category->created_at = date('Y-m-d H:i:s');
            $category->updated_at = date('Y-m-d H:i:s');
            $category->add();
            $this->success('添加成功', U('category/index'));
        }
    }

    public function edit()
    {
        if(!I('get.id', 0)) return $this->error('类别不存在', U('category/index'));
        $id = I('get.id');
        $category = D('category');
        $result = $category->find($id);
        $this->assign('result', $result);
        $this->display();
    }
    public function update()
    {
        if(!I('post.id', 0)) return $this->error('类别不存在', U('category/index'));
        $id = I('post.id');
        $category = D('category');
        if(!$category->create())
        {
            $this->error($category->getDbError());
        }else{
            $category->updated_at = date('Y-m-d H:i:s');
            $category->save();
            $this->success('更新类别成功!', U('category/index'));
        }
    }

    public function delete()
    {
        if(!I('get.id', 0)) return $this->error('类别不存在', U('category/index'));
        $id = I('get.id');
        $category = D('category');
        if($category->delete($id))
        {
            $this->success('删除成功!', U('category/index'));
        }else{
            $this->error('删除失败!');
        }
    }
}