<?php 
/**
 * 每日计算所有的文章，所有的类别，所有的标签的数量，已经新增文章的数量，最后更新时间
 */
echo 'sum start at '.date('Y-m-d H:i:s')."\n";
$currentScriptManager = new CurrentScriptManager();
$sql = "DELETE FROM `site_config` WHERE `Key` IN ('totalArticleCount', 'activeArticleCount', 'originArticleCount')";
$sql = "SELECT id,`status`,`articlesource` FROM `article`";
$result = $currentScriptManager->dstDataObj->getRows($sql);
foreach ($result as $k => $v) {
	if($v['status'] == 'active'){
		$data['activeArticle'][] = $v;
	}
	if($v['articlesource'] == '原创'){
		$data['originArticle'][] = $v;
	}
}
$data['totalArticle'] = $result;

$sum['totalArticleCount'] = count($data['totalArticle']);
$sum['activeArticleCount'] = count($data['activeArticle']);
$sum['originArticleCount'] = count($data['originArticle']);

$sql = "SELECT count(*) as c FROM `category`";
$result = $currentScriptManager->dstDataObj->getFirstRow($sql);
$sum['totalCategoryCount'] = $result['c'];
$sql = "SELECT count(*) as c FROM `tag`";
$result = $currentScriptManager->dstDataObj->getFirstRow($sql);
$sum['totalTagCount'] = $result['c'];
$sql = "INSERT INTO `site_config` (`Key`, `Value`, `LastChangeTime`) VALUES ";
foreach ($sum as $k => $v) {
	$sql .= sprintf("('%s', '%s', '%s'),", $k, $v, date('Y-m-d H:i:s'));
}
$sql = substr($sql, 0, -1);
$flag = $currentScriptManager->dstDataObj->query($sql);
echo 'sum success! '.$lastScriptTime;
class CurrentScriptManager
{
	public $dstDataObj;
	function __construct(){
		error_reporting(1);
		include_once(dirname(dirname(__FILE__))."/etc/const.php");
		include_once(INCLUDE_ROOT."lib/Class.MyException.php");
		if(version_compare(PHP_VERSION,'7.0.0','<'))  
			include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
		else 
			include_once(INCLUDE_ROOT."lib/Class.MysqlIm.php");      //php 7 兼容
		include_once(INCLUDE_ROOT."func/function.php");
		if (version_compare(PHP_VERSION,'7.0.0','<')) {
			$this->dstDataObj = new Mysql(BASE_DB_NAME,BASE_DB_HOST,BASE_DB_USER,BASE_DB_PASS);
		} else {
			$this->dstDataObj = new MysqlIM(BASE_DB_NAME,BASE_DB_HOST,BASE_DB_USER,BASE_DB_PASS);
		}
	}
}