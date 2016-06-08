<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
	 public function index(){
        $file = file('../../../tmp/alipay_record_20160607_1711_1.csv');   
      $financeInfoLst = array();
      foreach ($file as $k => $v) {
        if($k <5 || $k > 207) continue;
        $tmp = explode(",", $v);
        //dump($tmp);die;
        $data['amount'] = $tmp[9];
        $data['type'] = $tmp[10];
        $data['when'] = $tmp[4];
        $data['merchant'] = $tmp[7];
        $data['content'] = $tmp[8];
        $financeInfoLst[] = $data;
      }
      dump($financeInfoLst);die;
   }
}