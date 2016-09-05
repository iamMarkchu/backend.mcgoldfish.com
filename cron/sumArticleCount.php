<?php 
/**
 * 每日计算每篇文章的点击次数
 */
echo 'sum start at '.date('Y-m-d H:i:s')."\n";
$scriptName = '/cron/sumArticleCount';
$currentScriptManager = new CurrentScriptManager();
$queryString = sprintf("SELECT `Value` FROM `data_cache` WHERE `Name` = '%s'",$scriptName);
$result = $currentScriptManager->dstDataObj->getFirstRow($queryString);
if(empty($result)){
	$nowTime = date('Y-m-d H:i:s');
	$lastScriptTime = '0000-00-00 00:00:00';
}else{
	$lastScriptTime = $result['Value'];
}
$queryString = sprintf("SELECT `RequestUri`,COUNT(*) AS `ArticleCount` FROM `pagevisitlog` WHERE `VisitTime` >= '%s' AND `PageType` = 'ARTICLE' GROUP BY `RequestUri` ",date('Y-m-d H:i:s',$lastScriptTime));
$resultList = $currentScriptManager->srcDataObj->getRows($queryString,'RequestUri');
$countListByUrl = $resultList;unset($resultList);
$inList = array();
foreach ($countListByUrl as $k => $v) {
	$inList[] = "'".$v['RequestUri']."'";
}
$queryString = sprintf("SELECT `requestpath`,`optdataid` FROM `rewrite_url` WHERE `requestpath` IN (%s)",implode(",", $inList));
$urlList = $currentScriptManager->dstDataObj->getRows($queryString,'requestpath');
$exucString = "UPDATE `article` set `clickcount` = CASE ";
$idList = array();
foreach ($countListByUrl as $k => $v) {
	if(in_array($k, array_keys($urlList))){
		$countListByUrl[$k]['ArticleId'] = $urlList[$k]['optdataid'];
		$idList[] = $urlList[$k]['optdataid'];
		$exucString .= sprintf(" WHEN `id` = '%d' THEN `clickcount` + '%d'",$countListByUrl[$k]['ArticleId'],$countListByUrl[$k]['ArticleCount']);
	}
}
$exucString .= " END WHERE `id` IN (".implode(",", $idList).")";
$flag = $currentScriptManager->dstDataObj->query($exucString);
if($flag)
	$currentScriptManager->insert_data_cache($scriptName,$lastScriptTime);
echo 'sum success! '.$lastScriptTime;
class CurrentScriptManager
{
	public $dstDataObj;
	public $srcDataObj;
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
			$this->srcDataObj = new Mysql(TRACKING_DB_NAME,TRACKING_DB_HOST,TRACKING_DB_USER,TRACKING_DB_PASS);
		} else {
			$this->dstDataObj = new MysqlIM(BASE_DB_NAME,BASE_DB_HOST,BASE_DB_USER,BASE_DB_PASS);
			$this->srcDataObj = new Mysql(TRACKING_DB_NAME,TRACKING_DB_HOST,TRACKING_DB_USER,TRACKING_DB_PASS);
		}
	}
	public function insert_data_cache($name,$value){
 	   if(empty($name) || empty($value)) return false;
 	   $updateTime = date('Y-m-d H:i:s');
 	   $exucString = sprintf("INSERT INTO `data_cache` (`Name`,`Updatetime`,`Value`) VALUES ('%s','%s','%d')",addslashes($name),$updateTime,$value);
 	   $flag = $this->dstDataObj->query($exucString);
 	   return $flag;
	}
}