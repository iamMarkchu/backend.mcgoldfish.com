<?php 
/**
 * 爬取文章作为测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
//website
$websiteToRobot = array("http://loveteemo.com/","http://toilove.com/","http://ilunhui.cn/");
$db = new Mysql();
echo "----------开始----------".date("Y-m-d H:i:s")."----------";
$prefixSql = "insert into robot (`status`,`content`,`from`,`addtime`) VALUES ";
loop_robot(137,0);
loop_robot(49,1);
loop_robot(60,2,"Article/index/a_id/");
function insert_data($postfixSql){
	global $prefixSql,$db;
	$sql = $prefixSql.$postfixSql;
	$sql = substr($sql,0,-1);
	if(MARK_DEBUG){
	    echo $sql;
	    echo "\n";
	}else{
	    $executeResult = $db->query($sql);
	    if($executeResult){
	    	echo "----------insert success----------".date("Y-m-d H:i:s")."----------";
	    	echo "\n";
	    }
	}
}

function loop_robot($loopNum,$webKey,$prefixUrl="article-"){
	global $websiteToRobot;
	$postfixSql = '';
	for($i=1;$i<$loopNum;$i++){
		$articleUrl = $websiteToRobot[$webKey].$prefixUrl.$i.".html";
		$tmpHtml = file_get_contents($articleUrl);
		if($tmpHtml){
			$tmpHtml = addslashes($tmpHtml);
			$addTime = date("Y-m-d H:i:s");
			$postfixSql = "('active','{$tmpHtml}','{$articleUrl}','{$addTime}'),";
			insert_data($postfixSql);
			echo $articleUrl."\n";
		}
	}
}