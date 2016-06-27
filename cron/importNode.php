<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/6/25
 * Time: 上午1:06
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
$db = new Mysql();
$path = "/app/site/mark-ubuntu/web/backend.mcgoldfish.com/Application/Home/Controller/";
$file_array = get_the_directory_file($path);
foreach ($file_array as $k => $v){
    $file = file($path.$v);
    $fileNameReg = '/(.*)Controller\.class\.php/';
    $functionReg = '/public function (.*)\(\)/';
    preg_match($fileNameReg,$v,$match);
    $fileName = $match[1];
    $sql = "select * from node where `name` = '{$fileName}'";
    $result = $db->getFirstRow($sql);
    if(empty($result)){
        $sql = "insert into node (`name`,`pid`,`level`,`addtime`) VALUES ('{$fileName}','1','2','".date("Y-m-d H:i:s")."')";
        $db->query($sql);
        $sql = "select * from node where `name` = '{$fileName}'";
        $result = $db->getFirstRow($sql);
        $pid = $result['id'];
    }else{
        $pid = $result['id'];
    }
    foreach ($file as $kk => $vv){
        if(preg_match($functionReg,$vv,$match)){
            $functionName = $match[1];
            $sql = "insert into node (`name`,`pid`,`level`,`addtime`) VALUES ('{$functionName}','{$pid}','3','".date("Y-m-d H:i:s")."')";
            $db->query($sql);
        }
    }
}
//获取当前目录下的excel文件
function get_the_directory_file($directory){
    $mydir = dir($directory);
    $dd = array();
    while($file = $mydir->read())
    {
        if((is_dir("$directory/$file")) AND ($file!=".") AND ($file!=".."))
        {
            echo "$file";
            tree("$directory/$file");
        }else{
            $reg = "/\.php$/";
            if(preg_match($reg, $file)){
                $dd[] = $file;
            }
        }
    }
    $mydir->close();
    return $dd;
}