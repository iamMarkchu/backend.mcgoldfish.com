<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends CommonController {
	public function _before_index(){
		$this->assign('isTress',1);
		$this->assign('isUeditor',1);	
	}
	public function index(){
		$imageObj = new \Think\Image(); 
		$abPath = './Public/user/2016-06-06/57551deca1277.png';
        $a =$imageObj->open($abPath);
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        $b = $imageObj->crop(150, 150)->save('./Public/1.png');
        dump($imageObj);die;
	}
}