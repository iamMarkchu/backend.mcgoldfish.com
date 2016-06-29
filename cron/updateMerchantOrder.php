<?php 
/**
 * 分析finance表,通过merchant的使用频率来给merchant排序
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new Mysql();
$sql = "select `merchant`,count(*) as c from `finance` group by `merchant` order by c desc";
$result = $db->getRows($sql);
$merchantUsedList = $result;
unset($result);
$sql = "select `name` from merchant";
$result = $db->getRows($sql,'name');
$merchantArray = array_keys($result);
unset($result);
$order = 1;
foreach ($merchantUsedList as $k => $v) {
	if(in_array($v['merchant'], $merchantArray)){
		$merchant = $v['merchant'];
		$sql = "update merchant set `order` = '{$order}' where `name` = '{$merchant}'";
		$flag = $db->query($sql);
		if($flag) echo "1";
		else echo "0";
		$order++;
	}
}
echo "<br>";
echo "---------End----------".date("Y-m-d H:i:s")."----------";
$sql = "select `category`,count(*) as c from `finance` group by `category` order by c desc";
$result = $db->getRows($sql);
$merchantUsedList = $result;
unset($result);
$sql = "select `displayname` from f_category";
$result = $db->getRows($sql,'displayname');
$merchantArray = array_keys($result);
unset($result);
$order = 1;
foreach ($merchantUsedList as $k => $v) {
	if(in_array($v['category'], $merchantArray)){
		$category = $v['category'];
		$sql = "update f_category set `displayorder` = '{$order}' where `displayname` = '{$category}'";
		$flag = $db->query($sql);
		if($flag) echo "1";
		else echo "0";
		$order++;
	}
}
echo "<br>";
echo "---------End----------".date("Y-m-d H:i:s")."----------";
$sql = "select `belong`,count(*) as c from `finance` group by `belong` order by c desc";
$result = $db->getRows($sql);
$belongUsedList = $result;
unset($result);
$sql = "select `name` from asset";
$result = $db->getRows($sql,'name');
$belongArray = array_keys($result);
unset($result);
$order = 1;
foreach ($belongUsedList as $k => $v) {
	if(in_array($v['belong'], $belongArray)){
		$belong = $v['belong'];
		$sql = "update asset set `order` = '{$order}' where `name` = '{$belong}'";
		$flag = $db->query($sql);
		if($flag) echo "1";
		else echo "0";
		$order++;
	}
}