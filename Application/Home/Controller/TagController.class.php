<?php
namespace Home\Controller;
use Think\Controller;
class TagController extends CommonController {
    public function insert(){
        $tag = D('tag');
        $tag->create();
        $tag->addtime = date("Y-m-d H:i:s");
        $flag = $tag->add();
        $back = $tag->find($flag);
        if($flag){
            $requestpath = "/tag/{$flag}.html";
            $url = D('rewrite_url');
            $urlData['requestpath'] = $requestpath;
            $urlData['modeltype'] = "tag";
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