<?php 
function ImgUpload($path){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     3145728 ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->savePath  =      $path;//C('IMG_SAVE_PATH'); // 设置附件上传目录
    // 上传文件 
    $info   =   $upload->upload();
    if(!$info) {// 上传错误提示错误信息
        //$this->error($upload->getError());
        return false;
    }else{// 上传成功
        return $info['imgFile'];
    }
}