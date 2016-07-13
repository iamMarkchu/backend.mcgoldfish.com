<?php
/**
 * 批量导入标签测试数据,并生成对应url
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
// $tagFile = file_get_contents(INCLUDE_ROOT."tmp/tag.html");
// $tmpArray = explode("</li>",$tagFile);
// $newArray = array();
// foreach ($tmpArray as $k => $v) {
// 	$reg = '/.*"\/article\-(\d+).html">(.*)<\/a>/';
// 	$result = preg_replace($reg,"$2",$v);
// 	$newArray[] = $result;
// }
$db = new mysql();
// $prefixSql = "insert into tag (`displayname`,`addtime`,`displayorder`) VALUES ";
// foreach ($newArray as $k => $v) {
// 	if(!empty($v)){
// 		$displayname = addslashes($v);
// 		$addtime = date("Y-m-d H:i:s");
// 		$displayorder = rand(0,30);
// 		$postfixSql .= "('{$displayname}','{$addtime}','{$displayorder}'),";
// 	}
// }
// $sql = $prefixSql.$postfixSql;
// $sql = substr($sql,0,-1);

// if(MARK_DEBUG){
//     echo $sql;
//     echo "<br/>";
// }else{
//     $executeResult = $db->query($sql);
//     if($executeResult){
//     	echo "----------insert tag success----------".date("Y-m-d H:i:s")."----------";
//     	echo "<br/>";
//     }
// }
//导入url
$prefixUrl = "/category/";
$postfixUrl = ".html";
$sql = "select id from tag ";
$executeResult = $db->getRows($sql);
$prefixUrlSql = "insert into rewrite_url (`requestpath`,`modeltype`,`optdataid`,`isjump`,`status`) values ";
foreach ($executeResult as $v) {
	$requestpath = $prefixUrl."{$v['id']}".$postfixUrl;
	$modeltype = "tag";
	$optdataid = $v['id'];
	$isjump = 'NO';
	$status = "yes";
	$postfixUrlSql .= "('{$requestpath}','{$modeltype}','{$optdataid}','{$isjump}','{$status}'),";
}
$sql = $prefixUrlSql.$postfixUrlSql;
$sql = substr($sql, 0, -1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------链接创建成功----------".date("Y-m-d H:i:s")."----------";
    }
}