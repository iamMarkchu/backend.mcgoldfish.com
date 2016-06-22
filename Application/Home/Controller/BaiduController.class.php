<?php
namespace Home\Controller;
use Think\Controller;
class BaiduController extends CommonController {
    public function saveKeyword(){
        if(!IS_POST) exit('0');
        $keyword = addslashes(trim($_POST['keyword']));
        $my_search_keyword = D('my_search_keyword');
        $my_search_keyword->create();
        $data['keyword'] = $keyword;
        $data['addtime'] = date('Y-m-d H:i:s');
        $flag = $my_search_keyword->add($data);
        echo $flag;
    }
}