<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
	 public function index(){
      $m = M();
      $sql = "select * from asset";
      $result = $m->query($sql);
      $assetList = $result;
      foreach ($assetList as $k => $v) {
          $tmpAssetName = $v['name'];
          $tmpAssetAmount = $v['value'];
          $sql = "select * from finance where belong = '{$tmpAssetName}' and status = '未处理'";
          $tmpFinanceList = $m->query($sql);
          if(!empty($tmpFinanceList)){
              $m->startTrans();
              $commitFlag = false;
              foreach ($tmpFinanceList as $kk => $vv) {
                  $type = "+";
                  if($vv['type'] != '收入') $type = "-";
                  $sql = "update asset set value= value{$type}{$vv['amount']} where `name` = '{$tmpAssetName}'";
                  $tmpFlag = $m->execute($sql);
                  if($tmpFlag){
                      $sql = "update finance set `status` = '已处理' where id = {$vv['id']}";
                      $tmpFlag = $m->execute($sql);
                  }
                  if($tmpFlag){
                    $commitFlag = true;
                  }else{
                    echo $m->getDbError();
                    break;
                  }
              }
              if($commitFlag) $m->commit();
              else $m->rollback();
          }
      }
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