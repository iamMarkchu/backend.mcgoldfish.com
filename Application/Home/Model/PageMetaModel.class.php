<?php 
namespace Home\Model;
use Think\Model;
class PageMetaModel extends Model {
	protected $tablePrefix = '';
	public function getPageMetaByIdAndType($optdataid,$modeltype="article"){
		if(empty($optdataid)) return array();
		$where = "optdataid = {$optdataid} and `status` = 'yes' and modeltype='{$modeltype}'";
   		$result = $this->where($where)->select();
   		if(!empty($result)) return $result[0];
 		else return array();
	}
}