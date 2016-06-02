<?php 
/**
 * 批量导入文章测试数据,并生成对应url
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
echo "----------Start----------".date("Y-m-d H:i:s")."----------";
$db = new mysql();
$prefixSql = "insert into article (`title`,`content`,`tip`,`addtime`,`pageh1`,`maintainorder`,`status`,`articlesource`,`addeditor`) VALUES ";
$defaultTitle = '测试文章title';
$defaultContent = '测试文章content';
$defaultTip = '测试文章tip';
$defaultAddtime = date("Y-m-d H:i:s");
$defaultPageh1 = '测试文章pageh1';
$defaultStatus = array('active','deleted','republish');
$defaultarticlesource = array('转载','原创');
$defaultAddeditor = 'admin';
$totalCount = 100000;
for ($i=1; $i < $totalCount; $i++) { 
	$title = $defaultTitle."-{$i}";
	$content = $defaultContent."-{$i}";
	$tip = $defaultTip."-{$i}";
	$addtime = $defaultAddtime;
	$pageh1 = $defaultPageh1."-{$i}";
	$maintainorder = rand(0,100);
	$status = $defaultStatus[$maintainorder%3];
	$articlesource = $defaultarticlesource[$maintainorder%2];
	$addeditor = $defaultAddeditor."-{$i}";
	$postfixSql .= "('{$title}','{$content}','{$tip}','{$addtime}','{$pageh1}','{$maintainorder}','{$status}','{$articlesource}','{$addeditor}'),";
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
//导入url
$prefixUrl = "/article/";
$postfixUrl = ".html";
$sql = "select id from article";
$executeResult = $db->getRows($sql);
$prefixUrlSql = "insert into rewrite_url (`requestpath`,`modeltype`,`optdataid`,`isjump`,`status`) values ";
foreach ($executeResult as $v) {
	$requestpath = $prefixUrl."{$v['id']}".$postfixUrl;
	$modeltype = "article";
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