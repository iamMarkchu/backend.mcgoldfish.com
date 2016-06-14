<?php 
/**
 * 批量导入消费信息
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into finance (`amount`,`type`,`where`,`when`,`merchant`,`content`,`image`,`who`,`belong`) VALUES ";
$file = file(INCLUDE_ROOT.'tmp/xiaofei.csv');
foreach ($file as $k => $v) {
	if($k == 0) continue;
	$tmp = explode(",", $v);
	$amount = $tmp[1];
	$type = $tmp[2];
	$where = $tmp[3];
	$when = $tmp[4];
	$merchant = $tmp[5];
	$content = $tmp[6];
	$image = '';
	$who = $tmp[7];
	$belong = $tmp[8];
	$postfixSql .= "('{$amount}','{$type}','{$where}','{$when}','{$merchant}','{$content}','{$image}','{$who}','{$belong}'),";
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
