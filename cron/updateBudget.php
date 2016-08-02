<?php 
/**
 * 统计每个用户的消费情况
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
$db = new mysql();
$sql = "select * from user";
$userList = $db->getRows($sql);
foreach ($userList as $k => $v) {
	$userId = $v['id'];
	$sql = "select date_format(`when`,'%Y-%m') as day ,sum(amount) as money from finance where `type` = '支出' and `who` = '{$userId}'  group by date_format(`when`,'%Y-%m') ";
	$zhichuList = $db->getRows($sql);
	foreach ($zhichuList as $kk => $vv) {
		$yearmonth = $vv['day'];
		$realcost = $vv['money'];
		$sql = "select * from budget where userid = '{$userId}' and yearmonth = '{$yearmonth}'";
		$budgetInfo = $db->getFirstRow($sql);
		if(empty($budgetInfo)){
			$sql = "insert into budget (`budget`,`userid`,`realcost`,`yearmonth`,`addtime`) values ('0','{$userId}','{$realcost}','{$yearmonth}','".date("Y-m-d H:i:s")."')";
			$db->query($sql);
		}else{
			$sql = "update budget set realcost = '{$realcost}' where userid = '{$userId}' and yearmonth = '{$yearmonth}'";
			$db->query($sql);
		}
	}
}
