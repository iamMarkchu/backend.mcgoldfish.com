<?php 
namespace Home\Model;
use Think\Model;
class CategoryModel extends Model {
	protected $tablePrefix = ''; 
   	public function getAllCategory(){
        $result = $this->order('parent_cate_id')->select();
        $cateInfo = array();
        foreach ($result as $k => $v) {
            if($v['parent_cate_id'] == 0){
                $cateInfo[$v['id']] = $v;
            }else{
                $cateInfo[$v['parent_cate_id']]['child'][] = $v;
            }
        }
        return $cateInfo;
   	}
   	public function getCategoryByArticleId($articleid){
   		if(empty($articleid)) return [];
   		$sql = "SELECT c.* FROM `article` as a LEFT JOIN `category` as c on a.category_id = c.id where a.id ={$articleid}";
   		$result = $this->query($sql);
 		if(!empty($result)) return $result[0];
 		else return [];
   	}
}