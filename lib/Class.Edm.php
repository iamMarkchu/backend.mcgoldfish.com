<?php
include_once(INCLUDE_ROOT . "func/string.func.php");
include_once(INCLUDE_ROOT . "func/admin.func.php");
include_once(INCLUDE_ROOT . "func/front.func.php");
include_once(INCLUDE_ROOT . "func/gpc.func.php");
include_once(INCLUDE_ROOT."lib/Image.php");
include_once(INCLUDE_ROOT."lib/Class.NormalCoupon.mod.php");
/*
 * FileName: Class.Term.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_EDM__"))
{
	define("__MOD_CLASS_EDM__",1);

	class Edm
	{
		public $objMysql;
		public $batchTotal = 0;
		public $keywords = array();
		public $statistics = array();

		function Edm($objMysql=NULL)
		{
			if($objMysql) $this->objMysql = $objMysql;
			else $this->objMysql = new Mysql(PROD_DB_NAME, PROD_DB_HOST, PROD_DB_USER, PROD_DB_PASS);
		}
		
		function getList(){
			$sql = 'SELECT * FROM edm ORDER BY AddTime desc';
			$row = $this->objMysql->getRows($sql);
			return $row;
		}
		
		function _instance($id){
			$sql = 'SELECT * FROM edm WHERE ID = '.intval($id);
			$row = $this->objMysql->getFirstRow($sql);
			return $row;
		}
		
		function save($data){
			if(isset($data['id']) && $data['id']){
				$sql = 'UPDATE edm SET
						Name = "'.addslashes($data['name']).'",
						Top_Block_Name = "'.addslashes(serialize($data['top_title'])).'",
						Top_Block_Link = "'.addslashes(serialize($data['top_link'])).'",
						Banner_Block_Name = "'.addslashes(serialize($data['banner_title'])).'",
						Banner_Block_Link = "'.addslashes(serialize($data['banner_link'])).'",
						List_Block_Termid = "'.addslashes(serialize($data['term_list_id'])).'",
						List_Block_Couponid = "'.addslashes(serialize($data['coupon_list_id'])).'",
						Footer_Block_Termid = "'.addslashes(serialize($data['term_footer_id'])).'",
						Modified = '.time().'
						WHERE ID = '.intval($data['id']);
			}else{
				$sql = 'INSERT INTO edm SET 
						Name = "'.addslashes($data['name']).'",
						Top_Block_Name = "'.addslashes(serialize($data['top_title'])).'",
						Top_Block_Link = "'.addslashes(serialize($data['top_link'])).'",
						Banner_Block_Name = "'.addslashes(serialize($data['banner_title'])).'",
						Banner_Block_Link = "'.addslashes(serialize($data['banner_link'])).'",
						List_Block_Termid = "'.addslashes(serialize($data['term_list_id'])).'",
						List_Block_Couponid = "'.addslashes(serialize($data['coupon_list_id'])).'",
						Footer_Block_Termid = "'.addslashes(serialize($data['term_footer_id'])).'",
						AddTime = '.time().',
						Modified = '.time();
			}
			$this->objMysql->query($sql);
		}
		
		function getCouponInfo($id,$termInfo=array()){
			
			$sql = "SELECT c.*,ca.promotiondetail,ca.promotionoff FROM normalcoupon AS c  LEFT JOIN normalcoupon_addinfo AS ca  ON (c.ID = ca.ID) WHERE c.ID = ".intval($id);
			$coupon = $this->objMysql->getFirstRow($sql);
			$coupon["pc2011title"] = getpc2011title(!empty($coupon["Code"]) ? true : false, $coupon["promotiondetail"], $coupon["promotionoff"]);
			$coupon['Ends'] = ($coupon['ExpireTime'] == '0000-00-00 00:00:00')? '':substr($coupon['ExpireTime'],5,2).'/'.substr($coupon['ExpireTime'],8,2).'/'.substr($coupon['ExpireTime'],0,4);
			$coupon["coupon_rd_url"] = base64_encode("coupon").'|'.base64_encode($coupon['ID']).'|'.base64_encode("sync").'|'.base64_encode('');
			$coupon["BlockName"] = 'PromoEDM';
			$coupon["promotionstr"] = getPromotionStr($coupon["promotiondetail"], $coupon["promotionoff"]);
			$coupon['TermName'] = $termInfo['Name'];
			$coupon['TermID'] = $termInfo['ID'];
// 			echo '<pre>';print_r($coupon);exit();
			$coupon = simpleSpliceCouponTitle($coupon);
			
			return $coupon;
		}
		
		function getManualPromotion($id){
				
			$sql = "SELECT * FROM manual_promotion WHERE ID = ".intval($id);
			$coupon = $this->objMysql->getFirstRow($sql);
			
			$coupon["pc2011title"] = getpc2011title(!empty($coupon["Code"]) ? true : false, $coupon["PromotionDetail"], $coupon["PromotionOff"]);
			$coupon['Ends'] = ($coupon['ExpireTime'] == '0000-00-00 00:00:00')? '':substr($coupon['ExpireTime'],5,2).'/'.substr($coupon['ExpireTime'],8,2).'/'.substr($coupon['ExpireTime'],0,4);
			$coupon["coupon_rd_url"] = base64_encode("manualpromotion").'|'.base64_encode($coupon["ID"]).'|'.base64_encode("manu").'|'.base64_encode($coupon["URL"]);
			$coupon["BlockName"] = 'PromoEDM';
			$coupon['displaytitle'] = $coupon['Title'];
			return $coupon;
		}

	}
	
}
?>