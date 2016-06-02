<?php 
namespace Home\Model;
use Think\Model;
class CommonModel extends Model {
	public function resume($options,$field='status'){
        if(FALSE === $this->where($options)->setField($field,'active')){
            return false;
        }else {
            return True;
        }
    }
}