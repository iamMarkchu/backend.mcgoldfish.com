<?php
namespace Home\Controller;
use Think\Controller;
class ImagesController extends CommonController {

  	public function insert(){
  		if(!empty($_FILES['imgFile']['name'])){
  			$fileArray = $_FILES['imgFile']['name'];
  			$nameArray = $_POST['name'];
  			$path = '/albumImage/';
            $imgFile = ImgUpload($path);
            $insertData = array();
  			foreach ($fileArray as $k => $v) {
  				$tmp['name'] = $nameArray[$k];
  				$tmp['image'] = $imgFile[$k]['savepath'].$imgFile[$k]['savename'];
  				$tmp['adduser'] = $_SESSION['loginUserName'];
  				$tmp['addtime'] = date('Y-m-d H:i:s');
  				$insertData[] = $tmp;
  			}
  			$Images = D('images');
  			$flag = $Images->addAll($insertData);
  			if($flag) $this->success('成功','index');
  				else $this->error('失败');
  		}
  	}
}