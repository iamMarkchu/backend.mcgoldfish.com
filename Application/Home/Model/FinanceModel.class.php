<?php 
namespace Home\Model;
use Think\Model;
class FinanceModel extends CommonModel {
	public function getSumList($userid){
		//获取当前月
		$nowMonth = date("Y-m");
		$sql = "select fc.`displayname` as `name`,sum(amount) as `value` from finance as f left join f_category as fc on f.`category` = fc.id where `type` ='支出' and `when` >= '{$nowMonth}' and `who` in ({$userid}) group by `category`";
		$result = $this->query($sql);
		return $result;
	}
	public function getSumByDay($userid){
		//获取当前月
		$nowMonth = date("Y-m");
		$sql = "select date_format(`when`,'%Y-%m-%d') as day ,sum(amount) as money from finance where `type` = '支出' and `when` >= '{$nowMonth}' and `who` in ({$userid}) group by date_format(`when`,'%Y-%m-%d')";
		$result = $this->query($sql);
		return $result;
	}
	public function getFinanceListPage($start=0,$length=10,$map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['type'])){
				$type = $map['type'];
				$where .= "and f.`type` = '{$type}' ";
			}
			if(isset($map['when'])){
				$egtWhen = $map['when'][0][1];
				$eltWhen = $map['when'][1][1];
				$where .= "and (f.`when` >= '{$egtWhen}' and f.`when` <= '{$eltWhen}') ";
			}
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}else{
			$order = "order by `when` desc";
		}
		$sql = "select f.*,an.`name` as anName,fc.displayname as fcdisplayname,m.`name` as mname,u.`nickname` as `uname` from finance as f left join asset_new as an on f.belong = an.id left join f_category as fc on fc.id = f.`category` left join merchant as m on m.id = f.merchant left join `user` as u on u.id = f.`who` {$where} {$order} limit {$start},{$length}";
		$result = $this->query($sql);
		return $result;
	}
	public function getFinanceListForPageCount($map,$order){
		$where = "where 1=1 ";
		if(!empty($map)){
			if(isset($map['type'])){
				$type = $map['type'];
				$where .= "and f.`type` = '{$type}' ";
			}
			if(isset($map['when'])){
				$egtWhen = $map['when'][0][1];
				$eltWhen = $map['when'][1][1];
				$where .= "and (f.`when` >= '{$egtWhen}' and f.`when` <= '{$eltWhen}') ";
			}
		}
		if(!empty($order)){
			$order = "order by {$order}";
		}else{
			$order = "order by `when` desc";
		}
		$sql = "select f.*,an.`name` as anName,fc.displayname as fcdisplayname from finance as f left join asset_new as an on f.belong = an.id left join f_category as fc on fc.id = f.`category` {$where} {$order}";
		$result = $this->query($sql);
		$result = count($result);
		return $result;
	}
}