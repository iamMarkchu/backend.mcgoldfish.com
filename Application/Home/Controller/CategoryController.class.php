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
        $category = D('category');
        $parent_category_list = $category->where(['parent_cate_id' => 0])->select();
        $this->assign('parent_category_list', $parent_category_list);
        $this->display();
    }
    public function insert(){
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
}