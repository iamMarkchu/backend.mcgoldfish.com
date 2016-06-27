<?php 
/**
 * 批量导入文章和类别的关系测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
error_reporting(E_ALL);
define("MARK_DEBUG", false);
$db = new Mysql();
//echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$url = "https://list.lu.com/list/all?minMoney=&maxMoney=&minDays=0&maxDays=0&minRate=&maxRate=&mode=&tradingMode=&isCx=&currentPage=1&orderCondition=&isShared=&canRealized=&productCategoryEnum=";
$file = file_get_contents($url);
$file = str_replace("\n","", $file);
$preg = "/>零活宝-28日聚财(.*)\<li class=\"collection\-mode\">{0,1}/SU";
preg_match($preg,$file,$match);
if(isset($match[1])) $mainInfo = $match[1];
else die("html变化请重新设定正则");
$preg2 = "/每万元收益 (.*)元/";
preg_match($preg2, strip_tags($mainInfo),$match1);
if(isset($match1[1])) $perTenThound = $match1[1];
else die("html变化请重新设定正则");
$sql = "select value from asset where `name` = '陆金所'";
$result = $db->getFirstRow($sql);
if(!empty($result)) $totalFinance = $result['value'];
else $totalFinance = 0;
//计算
if($totalFinance){
	$todayFinance = floatval($totalFinance)/10000 * $perTenThound;
	$sql = "START TRANSACTION";
	$db->query($sql);
	$sql = "INSERT INTO `finance` ( `amount`, `type`, `where`, `when`, `merchant`, `content`, `image`, `who`, `belong`, `status`)
VALUES
	('{$todayFinance}', '收入', '在理财产品', '".date('Y-m-d H:i:s')."', '陆金所', '理财收益', NULL, 'mark', '陆金所', '已处理')"	;
	$result1 = $db->query($sql);
	$sql = "update asset set value = value+{$todayFinance} where `name` = '陆金所'";
	$result2 = $db->query($sql);
	if($result1 && $result2){
		$sql = "COMMIT";
		$result = $db->query($sql);
		echo "成功";
	}else{
		$sql = "ROLLBACK";
		$db->query($sql);
		echo "更新数据库失败";
	}
}