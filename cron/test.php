<?php 
/**
 * 批量导入类别,并生成对应url
 */
function dump($data){
	echo "<pre>";
	print_r($data);
}
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
define("MARK_DEBUG", false);
//定义类别数组
echo "----------开始----------".date("Y-m-d H:i:s")."----------";
echo "<br/>";
echo "----------组织数组----------".date("Y-m-d H:i:s")."----------";
echo "<br />";
$prefixSql = "insert into category (`displayname`,`parentcategoryid`,`addtime`,`displayorder`) values ";
//第一级分类
$categoryInfo = array("代码","工作","游戏","运动","音乐","电影");
foreach ($categoryInfo as $k => $v) {
	$addtime = date('Y-m-d H:i:s');
	$parentcategoryid = 0;
	$displayorder = $k;
	$displayname = addslashes($v);
	$postfixSql .=  "('{$v}','{$parentcategoryid}','{$addtime}','{$displayorder}'),";
}
$db = new mysql();
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------一级分类创建成功----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}
//二级分类
$categorySecondInfo = array(
		"代码" => array("PHP","HTML","CSS","JS","数据库","LINUX"),
		"工作" => array("工作周记","工作总结","团建"),
		"运动" => array("减肥历程","跑步","健身"),
		"音乐" => array("好歌推荐"),
		"电影" => array("经典电影","最新电影")
	);
$sql = "select id,displayname from category where parentcategoryid = 0";
$postfixSql = "";
$executeResult = $db->getRows($sql,"displayname");
foreach ($executeResult as $k => $v) {
	if(in_array($v['displayname'], $categoryInfo)){
		foreach ($categorySecondInfo[$v['displayname']] as $kk => $vv) {
			$addtime = date('Y-m-d H:i:s');
			$parentcategoryid = $v['id'];
			$displayorder = $kk;
			$displayname = addslashes($vv);
			$postfixSql .=  "('{$displayname}','{$parentcategoryid}','{$addtime}','{$displayorder}'),";
		}
	}
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql, 0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------二级分类创建成功----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}
//导入url
$prefixUrl = "/category/";
$postfixUrl = ".html";
$sql = "select id from category";
$executeResult = $db->getRows($sql);
$prefixUrlSql = "insert into rewrite_url (`requestpath`,`modeltype`,`optdataid`,`isjump`,`status`) values ";
foreach ($executeResult as $v) {
	$requestpath = $prefixUrl."{$v['id']}".$postfixUrl;
	$modeltype = "category";
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






