<?php 
namespace Home\Model;
use Think\Model;
class FinanceModel extends CommonModel {
	public function getSumList(){
		$sql = "select `category` as `name`,sum(amount) as `value` from finance where `type` ='支出' group by `category`";
		$result = $this->query($sql);
		return $result;
	}
	public function getSumByDay(){
		$sql = "select date_format(`when`,'%Y-%m-%d') as day ,sum(amount) as money from finance where `type` = '支出' group by date_format(`when`,'%Y-%m-%d')";
		$result = $this->query($sql);
		return $result;
	}
}