<?php 
/**
 * 批量导入文章和标签的关系测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into tag_mapping (`optdataid`,`tagid`,`datatype`,`isprimary`,`addtime`) VALUES ";
$defaultUserid = array();
$defaultRoleid = array();
$sql = "select id from article";
$executeResult = $db->getRows($sql,"id");
$articleList = array_keys($executeResult);
$sql = "select id from tag";
$executeResult = $db->getRows($sql,"id");
$tagList = array_keys($executeResult);
foreach ($articleList as $k => $v) {
    //tag数量
    $count = rand(1,4);
	shuffle($tagList);
	$optdataid = $v;
    $datatype = 'article';
    for ($i=0; $i <$count ; $i++) { 
        if($i == 0){
            $isprimary = 'yes';
        }else{
            $isprimary = 'no';
        }
        $addtime = date("Y-m-d H:i:s");
        $tagid = $tagList[$i];
        $postfixSql .= "('{$optdataid}','{$tagid}','{$datatype}','{$isprimary}','{$addtime}'),";
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
    	echo "----------insert tag_mapping success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}