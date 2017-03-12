<?php 
namespace Home\Model;
use Think\Model;

class RoleModel extends Model
{
	protected $_validate = [
        ['name', 'require', '组别名必须'],
		['name', '', '组别已存在', 0, 'unique', 1],
	];
}