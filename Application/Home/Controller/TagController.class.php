<?php
namespace Home\Controller;
use Think\Controller;
class TagController extends CommonController {
    public function insert(){
        $tag = D('tag');
        $tag->create();
        $tag->addtime = date("Y-m-d H:i:s");
        $flag = $tag->add();
        if($flag){
            $requestPath = "/tag/{$flag}.html";
            $url = D('rewrite_url');
            $urlData['requestPath'] = $requestPath;
            $urlData['modeltype'] = "tag";
            $urlData['optdataidd'] = $flag;
            $urlData['isjump'] = "NO";
            $urlData['status'] = "yes";
            $url->create($urlData);
            $flag = $url->add();
            $this->success("添加成功","/Home/Tag/index");
        }
        
    }
}