<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends CommonController {
    public function insert(){
        $category = D('category');
        $category->create();
        $category->addtime = date("Y-m-d H:i:s");
        $flag = $category->add();
        if($flag){
            $requestPath = "/category/{$flag}.html";
            $url = D('rewrite_url');
            $urlData['requestPath'] = $requestPath;
            $urlData['modeltype'] = "category";
            $urlData['optdataid'] = $flag;
            $urlData['isjump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            $this->success("添加成功","/Home/Category/index");
        }
        
    }
}