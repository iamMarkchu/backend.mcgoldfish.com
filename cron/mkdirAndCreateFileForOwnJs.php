<?php 
/**
 * 生成对应Controller的own-js文件
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
$return = 0;
define("MARK_DEBUG", false);
if(!isset($_GET['controllerName'])){
	echo '控制器参数不能为空!';exit;
}
//创建目录
$ownJsPath = "/app/site/mark-ubuntu/web/backend.mcgoldfish.com/Public/own-js/";
$controllerName = $_GET['controllerName'];
if(!is_dir($ownJsPath.$controllerName)){
	$f = mkdir($ownJsPath.$controllerName,0775);
	if(!$f){
		echo '创建目录失败,请检查 '.$ownJsPath.'目录的权限';exit;
	}
}
//创建index.js,add.js,edit.js三种默认的js文件并填充模板js文件
$fileNameArray = array('index','add','edit');
$fileTypeArray = array('js');
$fileType = $fileTypeArray[0];   //js文件
foreach ($fileNameArray as $k => $v) {
	$fileName = $v.".".$fileType;
	$tmpFileArray = file($ownJsPath."Default/".$fileName);
	$fHandle = fopen($ownJsPath.$controllerName."/".$fileName,"w");
	foreach ($tmpFileArray as $kk => $vv) {
		$strReplace_1 = "[controllerName]";
		$strReplace_2 = "[actionName]";
		$tmpV = str_replace($strReplace_1,$controllerName,$vv);
		$tmpV = str_replace($strReplace_2,$v,$tmpV);
		fwrite($fHandle, $tmpV);
	}
	fclose($fHandle);
	if(is_file($ownJsPath."Default/".$fileName)) $return = 1;
}
echo $return;
