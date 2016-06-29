<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
  public function index(){
    $this->assign("isEcharts","1");
    $this->display();
  }
  public function testFile(){
    $file = fopen('./text',"r");
    echo fread($file,0);
  }
  public function testJson(){
    $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
    dump($json);
    dump(json_decode($json));
    dump(json_decode($json, true));
    $array = json_decode($json, true);
    dump(json_encode($array));
  }
}