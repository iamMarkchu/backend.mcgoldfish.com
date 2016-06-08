<?php 
/**
 * 批量导入支付宝消费测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", true);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
echo "<br>";
$db = new mysql();
$file = file(INCLUDE_ROOT.'tmp/alipay_record_20160607_1711_1.csv');
//dump($file);die;
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
//dump($financeInfoLst);die;
$prefixSql = "insert into finance (`amount`,`type`,`when`,`merchant`,`content`,`belong`) VALUES ";
$defaultBelong = array('cash','zhifubao','cmb');
foreach ($financeInfoLst as $k => $v) {
	$amount = $v['amount'];
	$rand = rand(0,100);
	$type = $v['type'];
	$when = $v['when'];
	$merchant = $v['merchant'];
	$content = $v['content'];
	$belong = $defaultBelong[$rand%3];
	$postfixSql .= "('{$amount}','{$type}','{$when}','{$merchant}','{$content}','{$belong}'),";
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------insert article success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}