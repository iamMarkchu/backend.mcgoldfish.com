<?php 
/**
 * 每日计算每个类别下的文章数量
 */
echo 'sum start at '.date('Y-m-d H:i:s')."\n";
$currentScriptManager = new CurrentScriptManager();
$exucString = "UPDATE `category` SET `articlecount` = 0";
$currentScriptManager->dstDataObj->query($exucString);

$exucString = "select categoryid,count(*) as c from category_mapping as c left join article as a on (c.`optdataid` = a.id and c.`datatype` = 'article') where a.`status` = 'active'  group by categoryid";
$result = $currentScriptManager->dstDataObj->getRows($exucString);
if(!empty($result)){
	$exucString = "UPDATE `category` SET `articlecount` = CASE ";
	$inList = [];
	foreach ($result as $k => $v) {
		$inList[] = $v['categoryid'];
		$exucString .= sprintf(" WHEN `id` = '%d' THEN `articlecount` + '%d' ", $v['categoryid'], $v['c']);
	}
	$exucString .= " END WHERE `id` IN (". implode(",", $inList).")";
	$flag = $currentScriptManager->dstDataObj->query($exucString);
}
$exucString = "select parentcategoryid,sum(articlecount) as c from category where parentcategoryid != 0  group by parentcategoryid";
$result = $currentScriptManager->dstDataObj->getRows($exucString);
if(!empty($result)){
	$exucString = "UPDATE `category` SET `articlecount` = CASE ";
	$inList = [];
	foreach ($result as $k => $v) {
		$inList[] = $v['parentcategoryid'];
		$exucString .= sprintf(" WHEN `id` = '%d' THEN `articlecount` + '%d' ", $v['parentcategoryid'], $v['c']);
	}
	$exucString .= " END WHERE `id` IN (". implode(",", $inList).")";
	$flag = $currentScriptManager->dstDataObj->query($exucString);
}

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
	public function insert_data_cache($name,$value){
 	   if(empty($name) || empty($value)) return false;
 	   $updateTime = date('Y-m-d H:i:s');
 	   $exucString = sprintf("INSERT INTO `data_cache` (`Name`,`Updatetime`,`Value`) VALUES ('%s','%s','%s')",addslashes($name),$updateTime,$value);
 	   $flag = $this->dstDataObj->query($exucString);
 	   return $flag;
	}
	public function update_data_cache($name,$value){
 	   if(empty($name) || empty($value)) return false;
 	   $updateTime = date('Y-m-d H:i:s');
 	   $exucString = sprintf("update `data_cache` SET `Updatetime` = '%s',`Value` = '%s' WHERE `Name` = '%s'", $updateTime, $value, addslashes($name));
 	   $flag = $this->dstDataObj->query($exucString);
 	   return $flag;
	}
}