<?php 
/**
 * 拆分finance表
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
$db = new Mysql();
$sql = "select * from finance";
$result = $db->getRows($sql);
$financeList = $result;
foreach ($financeList as $k => $v) {
	$finance_id = $v['id'];
	$belong = $v['belong'];
	$sql = "select * from `asset_new` where `name` = '{$belong}'";
	$belongFlag = $db->getFirstRow($sql);
	if(empty($belongFlag)){
		$sql = "insert into `asset_new` (`name`,`value`,`order`) values ('{$belong}','0','99')";
		$flag = $db->query($sql);
		$sql = "select * from `asset_new` where `name` = '{$belong}'";
		$belongFlag = $db->getFirstRow($sql);
	}
	if(!empty($belongFlag)){
		$asset_new_id = $belongFlag['id'];
		$sql = "update finance set `belong` = '{$asset_new_id}' where id = '{$finance_id}'";
		$flag = $db->query($sql);
	}
	// $category = $v['category'];
	// if(is_numeric($category) || empty($category)) continue;
	// $sql = "select * from `f_category` where `displayname` = '{$category}'";
	// $categoryFlag = $db->getFirstRow($sql);
	// if(empty($categoryFlag)){
	// 	$sql = "insert into `f_category` (`displayname`,`parentcategoryid`,`addtime`,'displayorder') values ('{$category}','0','".date("Y-m-d H:i:s")."','99')";
	// 	$flag = $db->query($sql);
	// 	$sql = "select * from `f_category` where `displayname` = '{$category}'";
	// 	$categoryFlag = $db->getFirstRow($sql);
	// }
	// if(!empty($categoryFlag)){
	// 	$f_category_id = $categoryFlag['id'];
	// 	$sql = "update finance set `category` = '{$f_category_id}' where id = '{$finance_id}'";
	// 	$flag = $db->query($sql);
	// }
	// $merchant = $v['merchant'];
	// if(is_numeric($merchant) || empty($merchant)) continue;
	// $sql = "select * from `merchant` where `name` = '{$merchant}'";
	// $merchantFlag = $db->getFirstRow($sql);
	// if(empty($merchantFlag)){
	// 	$sql = "insert into `merchant` (`name`,`order`) values ('{$merchant}','0')";
	// 	$flag = $db->query($sql);
	// 	$sql = "select * from `merchant` where `name` = '{$merchant}'";
	// 	$merchantFlag = $db->getFirstRow($sql);
	// }
	// if(!empty($merchantFlag)){
	// 	$merchant_id = $merchantFlag['id'];
	// 	$sql = "update finance set `merchant` = '{$merchant_id}' where id = '{$finance_id}'";
	// 	$flag = $db->query($sql);
	// }
	// $who = $v['who'];
	// if(is_numeric($who) || empty($who)) continue;
	// $sql = "select * from `user` where `nickname` = '{$who}'";
	// $userFlag = $db->getFirstRow($sql);
	// if(!empty($userFlag)){
	// 	$user_id = $userFlag['id'];
	// 	$sql = "update finance set `who` = '{$user_id}' where id = '{$finance_id}'";
	// 	$flag = $db->query($sql);
	// }
}
echo 1;