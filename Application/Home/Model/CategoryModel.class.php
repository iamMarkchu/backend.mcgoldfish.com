<?php 
namespace Home\Model;
use Think\Model;
class CategoryModel extends Model {
	protected $tablePrefix = ''; 
   	public function getAllCategory(){
        $result = $this->order('parentcategoryid')->select();
        $cateInfo = array();
        foreach ($result as $k => $v) {
            if($v['parentcategoryid'] == 0){
                $cateInfo[$v['id']] = $v;
            }else{
                $cateInfo[$v['parentcategoryid']]['child'][] = $v;
            }
        }
        return $cateInfo;
   	}
   	public function getCategoryByIdAndType($optdataid,$datatype="article"){
   		if(empty($optdataid)) return array();
   		$sql = "select * from category as c left join category_mapping as cm on c.id = cm.categoryid where cm.optdataid ={$optdataid} and cm.isprimary = 'yes' and datatype = '{$datatype}';";
   		$result = $this->query($sql);
 		if(!empty($result)) return $result[0];
 		else return array();
   	}
}