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
$prefixSql = "insert into user (`account`,`nickname`,`password`,`addtime`,`email`) VALUES ";
$defaultAccount = '';
$defaultNickname = '测试用户nickname';
$defaultPassword = md5("123456");
$defaultAddtime = date("Y-m-d H:i:s");
$defaultEmail = array("@qq.com","@163.com","@sina.com","@Gmail.com","@126.com","@yahoo.com");
$totalCount = 1000;
for ($i=1; $i < $totalCount; $i++) { 
	$account = rand_string(9);
	$nickname = $defaultNickname."-{$i}";
	$password = $defaultPassword;
	$addtime = $defaultAddtime;
	$randNum = rand(1,99);
	if($randNum%2 ==1){
		$Email = $account.$defaultEmail[($randNum%6)];
	}else{
		$Email = rand_string(8).$defaultEmail[($randNum%6)];
	}
	$postfixSql .= "('{$account}','{$nickname}','{$password}','{$addtime}','{$Email}'),";
}
$sql = $prefixSql.$postfixSql;
$sql = substr($sql,0,-1);
if(MARK_DEBUG){
    echo $sql;
    echo "<br/>";
}else{
    $executeResult = $db->query($sql);
    if($executeResult){
    	echo "----------insert user success----------".date("Y-m-d H:i:s")."----------";
    	echo "<br/>";
    }
}