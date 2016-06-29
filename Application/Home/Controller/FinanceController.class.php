<?php
namespace Home\Controller;
use Think\Controller;
class FinanceController extends CommonController {
    public function _before_index(){
        $searchArray = session('FinanceSearch');
        //dump($searchArray);die;
        $this->assign('searchArray',$searchArray);
        $this->assign('isDatePicker',"1");
    }
    public function QueryData(){
        $start = $_POST['start'];
        $length = $_POST['length'];
        $map = array();
        $model = D(CONTROLLER_NAME);
        $searchArray = session(CONTROLLER_NAME.'Search');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
        $result = $model->where($map)->order('`when` desc')->limit($start,$length)->select();
        $count = $model->where($map)->count();
        $jsonBack = array();
        $jsonBack['data'] = $result;
        $jsonBack['recordsFiltered'] = $count;
        $jsonBack['recordsTotal'] = $count;
        $this->ajaxReturn($jsonBack);
    }
    public function _before_add(){
    	$asset = D('asset');
        $merchant = D('merchant');
        $fCategory = D('f_category');
    	$allAssetInfo = $asset->order('`order`')->select();
        $allMerchnatInfo = $merchant->order('`order`')->select();
        $allFcategoryInfo = $fCategory->order('`displayorder`')->select();
        $this->assign('isSelect2',1);
    	$this->assign('allAssetInfo',$allAssetInfo);
        $this->assign('allMerchnatInfo',$allMerchnatInfo);
        $this->assign('allFcategoryInfo',$allFcategoryInfo);
    }
    public function insert(){
        $model = D (CONTROLLER_NAME);
        //保存当前数据对象
        $model->create();
        $model->who = $_SESSION['loginUserName'];
        $list=$model->add ();
        $this->success('插入成功','index');
    }
    public function btn_Search(){
        session('FinanceSearch','');
        //组成查询及排序数组
        $searchArray = array();
        if(isset($_POST['type'])){
            $searchArray['where']['type'] = $_POST['type'];
        }
        if(isset($_POST['startdate']) && !empty($_POST['startdate'])){
            $searchArray['where']['when'][] = array('egt',$_POST['startdate']);
        }
        if(isset($_POST['enddate']) && !empty($_POST['enddate'])){
            $searchArray['where']['when'][]= array('elt',$_POST['enddate']);
        }
        session('FinanceSearch',$searchArray);
        echo "1";
    }
    public function QueryDataCsv(){
        $allInfofile = file("/app/site/mark-ubuntu/web/backend.mcgoldfish.com/tmp/xiaofei.csv");
        $start = $_POST['start'];
        $length = $_POST['length'];
        $key = explode(",", $allInfofile[0]);
        $xiaoFeiList = array();
        foreach ($allInfofile as $k => $v) {
            if($k == 0 ) continue;
            $value = explode(",", $v);
            $subTmp = array();
            foreach ($key as $kk => $vv) {
                $subTmp[$vv] = $value[$kk];
            }
            $xiaoFeiList[] = $subTmp;
        }
        $backList = array_slice($xiaoFeiList,$start,$length);
        $count = count($allInfofile);
        $jsonBack = array();
        $jsonBack['data'] = $backList;
        $jsonBack['recordsFiltered'] = $count;
        $jsonBack['recordsTotal'] = $count;
        $this->ajaxReturn($jsonBack);
    }
    public function indexCsv(){
        $this->display();
    }
    public function addToCsv(){
        $this->display();
    }
    public function updateCsv(){
        $info = $_POST['amount'];
        $now = date("Y-m-d H:i:s");
        $info = str_replace("when", $now, $info);
        $count = count(file("/app/site/mark-ubuntu/web/backend.mcgoldfish.com/tmp/xiaofei.csv")) ;
        $file = fopen("/app/site/mark-ubuntu/web/backend.mcgoldfish.com/tmp/xiaofei.csv", "a+");
        $flag = fwrite($file, $count.",".$info."\n");
        fclose($file);
        $this->success('插入成功','indexCsv');
    }
}