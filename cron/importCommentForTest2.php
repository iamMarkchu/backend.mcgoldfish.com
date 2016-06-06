<?php 
/**
 * 批量导入评论测试数据
 */
error_reporting(1);
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into comment (`content`,`addtime`,`username`,`email`,`optdataid`,`status`,`parentcommentid`) VALUES ";
$defaultUserid = array();
$defaultRoleid = array();
$sql = "select id from article";
$executeResult = $db->getRows($sql,"id");
$articleList = array_keys($executeResult);
$sql = "select id from comment";
$executeResult = $db->getRows($sql,"id");
$commentList = array_keys($executeResult);
$defaultContent = '测试回复评论content';
$defaultAddtime = date("Y-m-d H:i:s");
$defaultStatus = array('active','deleted','republish');
$defaultUsername = array('编辑','admin','markchu');
foreach ($articleList as $k => $v) {
    //评论数量
    $count = rand(1,8);
    $optdataid = $v;
    for ($i=0; $i <$count ; $i++) { 
        shuffle($commentList);
        $parentcommentid = $commentList[0];
        $addtime = date("Y-m-d H:i:s");
        $content = $defaultContent.rand_string(10,3);
        $status = $defaultStatus[$i%3];
        $username = $defaultUsername[$i%3];
        $postfixSql .= "('{$content}','{$addtime}','{$username}','{$email}','{$optdataid}','{$status}','{$parentcommentid}'),";
    }
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------insert comment success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}