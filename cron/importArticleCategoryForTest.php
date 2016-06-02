<?php 
/**
 * 批量导入文章和类别的关系测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into category_mapping (`optdataid`,`categoryid`,`datatype`,`isprimary`,`addtime`) VALUES ";
$defaultUserid = array();
$defaultRoleid = array();
$sql = "select id from article";
$executeResult = $db->getRows($sql,"id");
$articleList = array_keys($executeResult);
$sql = "select id from category";
$executeResult = $db->getRows($sql,"id");
$categoryList = array_keys($executeResult);
foreach ($articleList as $k => $v) {
	shuffle($categoryList);
	$optdataid = $v;
    $datatype = 'article';
    $addtime = date("Y-m-d H:i:s");
    $categoryid = $categoryList[0];
    $isprimary = 'yes';
    $postfixSql .= "('{$optdataid}','{$categoryid}','{$datatype}','{$isprimary}','{$addtime}'),";
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------insert category_mapping success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}