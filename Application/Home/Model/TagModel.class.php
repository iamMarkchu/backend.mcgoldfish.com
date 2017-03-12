<?php 
namespace Home\Model;
use Think\Model;
class TagModel extends Model {
   	public function getAllTag(){
        $result = $this->order('display_order')->select();
        return $result;
   	}
   	public function getTagByIdAndType($optdataid,$datatype="article"){
   		if(empty($optdataid)) return array();
   		$sql = "select t.* from tag as t left join tag_mapping as tm on t.id = tm.tagid where tm.optdataid ={$optdataid} and datatype = '{$datatype}';";
   		$result = $this->query($sql);
   		$data = array();
 		if(!empty($result)){
 			foreach ($result as $k => $v) {
 				$data[] = $v['id'];
 			}
 			return $data;
 		}else{
 			return array();
 		}
   	}
}