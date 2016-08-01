<?php 
/**
 * 生成对应Controller的own-js文件
 * 生成对应Controller文件
 * 生成对应Model文件
 * 生成对应的View文件
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
$controllerName = $_GET['controllerName'];
//创建目录own-js
$ownJsPath = "../Public/own-js/";
if(!is_dir($ownJsPath.$controllerName)){
	$f = mkdir($ownJsPath.$controllerName,0777);
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
//创建目录
$ViewPath = "../Application/Home/View/";
if(!is_dir($ViewPath.$controllerName)){
	$f = mkdir($ViewPath.$controllerName,0777);
	if(!$f){
		echo '创建目录失败,请检查 '.$ViewPath.'目录的权限';exit;
	}
}
//创建index.html,add.html,edit.html三种默认的html文件并填充模板html文件
$fileNameArray = array('index','add','edit');
$fileTypeArray = array('html');
$fileType = $fileTypeArray[0];   //js文件
foreach ($fileNameArray as $k => $v) {
	$fileName = $v.".".$fileType;
	$tmpFileArray = file($ViewPath."Default/".$fileName);
	$fHandle = fopen($ViewPath.$controllerName."/".$fileName,"w");
	foreach ($tmpFileArray as $kk => $vv) {
		$strReplace_1 = "[controllerName]";
		$strReplace_2 = "[actionName]";
		$strReplace_3 = "[actionUpdate]";
		$strReplace_4 = "[actionIndex]";
		$tmpV = str_replace($strReplace_1,$controllerName,$vv);
		$tmpV = str_replace($strReplace_2,$v,$tmpV);
		$tmpV = str_replace($strReplace_3,'update',$tmpV);
		$tmpV = str_replace($strReplace_4,'index',$tmpV);
		fwrite($fHandle, $tmpV);
	}
	fclose($fHandle);
}
//创建目录
$ControllerPath = "../Application/Home/Controller/";
//创建index.html,add.html,edit.html三种默认的html文件并填充模板html文件
$fileName = $controllerName."Controller.class.php";
$fHandle = fopen($ControllerPath.$fileName,"w");
fclose($fHandle);
//创建目录
$ModelPath = "../Application/Home/Model/";
//创建index.html,add.html,edit.html三种默认的html文件并填充模板html文件
$fileName = $controllerName."Model.class.php";
$fHandle = fopen($ModelPath.$fileName,"w");
fclose($fHandle);
echo $return;
