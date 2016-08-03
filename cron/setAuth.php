<?php 
/**
 * 设置权限
 * 编辑组拥有Article模块下除delete,repubilsh功能外的所有权限,Tag,Category模块下的所有功能 role_id = 2
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
$db = new Mysql();
#role_id = 2
// $role_id = "2";
// $authArray = array(
// 				1 => array(1),
// 				2 => array(
// 									2,
// 									7,
// 									8,
// 									16,
// 									25,
// 									45
// 								),
// 				3 => array(
// 									39,
// 									40,
// 									47,
// 									48,
// 									49,
// 									50,
// 									9,
// 									10,
// 									11,
// 									12,
// 									17,
// 									19,
// 									20,
// 								),
// 	);
$role_id = "4";
$authArray = array(
				1 => array(1),
				2 => array(
									2,
									7,
									8,
									16,
									25,
									45,
									22,
								),
				3 => array(
									39,
									40,
									47,
									48,
									49,
									50,
									9,
									10,
									11,
									12,
									17,
									19,
									20,
									26,
									27,
									28,
									33,
									34,
									35,
									36,
									37,
								),
	);
$sql = "delete from access where role_id = '{$role_id}'";
$db->query($sql);
$sql_pre = "insert into access (`role_id`,`node_id`,`level`) values "; 
foreach ($authArray as $k => $v) {
	foreach ($v as $kk => $vv) {
		$sql_post .= "('{$role_id}','{$vv}',0),";
	}
}
$sql = substr($sql_pre.$sql_post,0,-1);
$db->query($sql);