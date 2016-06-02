<?php 
/**
 * 更新所有article的clickcount
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$sql = "select id from article";
$executeResult = $db->getRows($sql,"id");
$articleList = array_keys($executeResult);
foreach ($articleList as $k => $v) {
    $clickcount = rand(100,20000);
    $sql = "update article set clickcount = '{$clickcount}' where id = '{$v}'";
    $db->query($sql);	
}
echo "success";
