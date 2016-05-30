<?php 
/**
 * 批量导入用户和组别的关系测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into role_user (`role_id`,`user_id`) VALUES ";
$defaultUserid = array();
$defaultRoleid = array();
$sql = "select id from user";
$executeResult = $db->getRows($sql,"id");
$userList = array_keys($executeResult);
$sql = "select id from role";
$executeResult = $db->getRows($sql,"id");
$roleList = array_keys($executeResult);
foreach ($userList as $k => $v) {
	shuffle($roleList);
	$user_id = $v;
	$role_id = $roleList[0];
	$postfixSql .= "('{$role_id}','{$user_id}'),";
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------insert role_user success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}