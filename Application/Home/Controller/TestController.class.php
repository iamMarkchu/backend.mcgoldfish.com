<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends CommonController {
	public function _before_index(){
		$this->assign('isModal',1);
	}
}