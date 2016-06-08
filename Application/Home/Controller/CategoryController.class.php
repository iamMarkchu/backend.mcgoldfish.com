<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends CommonController {
    public function insert(){
        $category = D('category');
        $category->create();
        $category->addtime = date("Y-m-d H:i:s");
        $flag = $category->add();
        $back = $category->find($flag);
        if($flag){
            $requestpath = "/category/{$flag}.html";
            $url = D('rewrite_url');
            $urlData['requestpath'] = $requestpath;
            $urlData['modeltype'] = "category";
            $urlData['optdataid'] = $flag;
            $urlData['isjump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            if(IS_AJAX){
                $this->ajaxReturn($back);
            }else{
                $this->success("添加成功","index");
            }
        }
        
    }
}