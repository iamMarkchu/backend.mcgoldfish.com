<?php 
namespace Home\Model;
use Think\Model;

class UserModel extends Model
{
	protected $_validate = [
		['account', 'require', '用户名必须'],
		['account', '', '用户名已存在', 0, 'unique', 1],
		['email', 'require', '邮箱必须'],
		['email', 'email', '邮箱格式不正确'],
	];

	public function getUserRole($userid)
	{
        $roleList = $this->field(['role.*'])->join(['LEFT JOIN role_user ON user.id = role_user.user_id', 'LEFT JOIN role ON role_user.role_id = role.id'])->where(['user.id' => $userid, 'role.status' => 'active'])->select();
        return $roleList;
	}
}