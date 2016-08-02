<?php
namespace Home\Controller;
use Think\Controller;
class FinanceController extends CommonController {
    public function _before_index(){
        $searchArray = session('FinanceSearch');
        if(is_mobile())
            $this->assign('isMobile',"1");
        $userid = $_SESSION[C('USER_AUTH_KEY')];
        $budgetMapping = D('budgetMapping');
        $mappingFlag = $budgetMapping->where('userid ='.$userid)->find();
        if(isset($mappingFlag['isprimary']) && $mappingFlag['isprimary'] =='no'){
            $this->assign('noSetBudget',"1");
        }
        $this->assign('userid',$_SESSION[C('USER_AUTH_KEY')]);
        $this->assign('username',$_SESSION['loginUserName']);
        $this->assign('searchArray',$searchArray);
        $this->assign('isEcharts',"1"); 
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
        //$result = $model->where($map)->order('`when` desc')->limit($start,$length)->select();
        $result = $model->getFinanceListPage($start,$length,$map,$order);
        //$count = $model->where($map)->count();
        $count = $model->getFinanceListForPageCount($map,$order);
        $jsonBack = array();
        $jsonBack['data'] = $result;
        $jsonBack['recordsFiltered'] = $count;
        $jsonBack['recordsTotal'] = $count;
        $this->ajaxReturn($jsonBack);
    }
    public function _before_add(){
    	$asset = D('asset_new');
        $merchant = D('merchant');
        $fCategory = D('f_category');
    	$allAssetInfo = $asset->where('`order` != 0 and `order` is not null')->order('`order`')->select();
        $allMerchnatInfo = $merchant->where('`order` != 0 and `order` is not null')->order('`order`')->select();
        $allFcategoryInfo = $fCategory->where('`displayorder` != 0 and `displayorder` is not null')->order('`displayorder`')->select();
        $this->assign('isSelect2',1);
    	$this->assign('allAssetInfo',$allAssetInfo);
        $this->assign('allMerchnatInfo',$allMerchnatInfo);
        $this->assign('allFcategoryInfo',$allFcategoryInfo);
    }
    public function insert(){
        $model = D (CONTROLLER_NAME);
        //保存当前数据对象
        $model->create();
        $model->who = $_SESSION[C('USER_AUTH_KEY')];
        $list=$model->add();
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
    public function getSumFinance(){
        $userid = $_SESSION[C('USER_AUTH_KEY')];
        $budgetMapping = D('budgetMapping');
        $mappingFlag = $budgetMapping->where('userid ='.$userid)->find();
        if(!empty($mappingFlag)){
            $userid .=",".$mappingFlag['binduserid'];
        }
        $finance = D('finance');
        $budget = D('budget');
        $sumList = $finance->getSumList($userid);
        $dayList = $finance->getSumByDay($userid);
        $budgetInfo = $budget->where('userid in ('.$userid.") and yearmonth = '".date("Y-m")."'")->select();
        $budgetInfoSum = array();
        if(count($budgetInfo) > 1){
            foreach ($budgetInfo as $k => $v) {
                if($v['userid'] == $mappingFlag['binduserid'] && $mappingFlag['isprimary'] == 'no'){
                    $budgetInfoSum['budget'] = $v['budget'];
                    $budgetInfoSum['userid'] = $v['userid'];
                    $budgetInfoSum['yearmonth'] = $v['yearmonth'];
                    $budgetInfoSum['realcost'] = $v['realcost'];
                    $budgetInfoSum['type'] = '1';
                }else if($mappingFlag['isprimary'] == 'yes' && $v['userid'] == $mappingFlag['userid']){
                    $budgetInfoSum['budget'] = $v['budget'];
                    $budgetInfoSum['userid'] = $v['userid'];
                    $budgetInfoSum['yearmonth'] = $v['yearmonth'];
                    $budgetInfoSum['realcost'] = $v['realcost'];
                    $budgetInfoSum['type'] = '1';
                }else{
                    $budgetInfoSum['realcost'] += $v['realcost'];
                }
            }
        }else{
            if(isset($budgetInfo[0])){
                $budgetInfoSum = $budgetInfo[0];
                $budgetInfoSum['type']='0';
            }
        }
        $jsonBack['sumList'] = $sumList;
        $jsonBack['dayList'] = $dayList;
        $jsonBack['budgetInfo'] = $budgetInfoSum;
        $this->ajaxReturn($jsonBack);
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
    public function ajaxGetMapSuggestion(){
        if(!IS_AJAX) echo 0;
        $keyword = trim($_REQUEST['key']);
        $apiUrlBase = 'http://api.map.baidu.com/place/v2/suggestion';
        $output = 'json';
        $ak = C('SUGGESTION_AK');
        $region = '131';
        $query = $keyword;
        $apiUrl = $apiUrlBase."?query=".$query."&output=".$output."&region=".$region."&ak=".$ak;
        $return = file_get_contents($apiUrl);
        $return = json_decode($return,true);
        $finalReturn = array();
        foreach ($return['result'] as $k => $v) {
            $tmp['id'] = $v['name'];
            $tmp['text'] = $v['name'];
            $finalReturn['data'][] = $tmp;
        }
        $this->ajaxReturn($finalReturn);
    }
    public function setBudget(){
        $userid = $_SESSION[C('USER_AUTH_KEY')];
        $budgetMapping = D('budgetMapping');
        $mappingFlag = $budgetMapping->where('userid ='.$userid)->find();
        if(!empty($mappingFlag)){
            $userid .=",".$mappingFlag['binduserid'];
        }
        $budget = D('budget');
        $budgetInfo = $budget->where("userid in (".$userid.") and yearmonth = '".date("Y-m")."'")->find();
        $this->ajaxReturn($budgetInfo);   
    }
    public function getHisBudgetList(){
        $userid = $_SESSION[C('USER_AUTH_KEY')];
        $budgetMapping = D('budgetMapping');
        $mappingFlag = $budgetMapping->where('userid ='.$userid)->find();
        if(!empty($mappingFlag)){
            $userid .=",".$mappingFlag['binduserid'];
        }
        $start = $_POST['start'];
        $length = $_POST['length'];
        $budget = D('budget');
        $budgetList = $budget->where('userid in ('.$userid.')')->order('yearmonth desc')->limit($start,$length)->select();
        $budgetInfoTmp = array();
        $budgetInfoSum = array();
        if(count($budgetList)> 1){
            foreach ($budgetList as $k => $v) {
                $budgetInfoTmp[$v['yearmonth']][] = $v;
            }
            $count = 0;
            foreach ($budgetInfoTmp as $k => $v) {
                foreach ($v as $kk => $vv) {
                    if((isset($mappingFlag['userid']) && ($vv['userid'] == $mappingFlag['userid'] && $mappingFlag['isprimary'] == 'yes'))|| (isset($mappingFlag['binduserid']) && ($vv['userid'] == $mappingFlag['binduserid'] && $mappingFlag['isprimary'] == 'no'))){
                        $budgetInfoSum[$count] = $vv;
                    }else if(isset($mappingFlag['binduserid']) && $mappingFlag['isprimary'] == 'no'){
                        $budgetInfoSum[$count]['realcost']+= $vv['realcost'];
                    }else{
                        $budgetInfoSum[$count] = $vv;
                    }
                }
                $count++;
            }
        }
        $jsonBack = array();
        $jsonBack['data'] = $budgetInfoSum;
        $jsonBack['recordsFiltered'] = count($budgetInfoSum);
        $jsonBack['recordsTotal'] = count($budgetInfoSum);
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