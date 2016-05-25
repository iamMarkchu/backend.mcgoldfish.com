<?php
if (!defined("__MOD_CLASS_MERCHANTPORTAL__")){
	define("__MOD_CLASS_MERCHANTPORTAL__",1);

	class MerchantPortal extends ExtMerchant {

		public function __construct($objMysql){
			parent::__construct($objMysql);
		}

		public function setPortalInfos($arrinfos=""){
			if( $arrinfos == "" ){
				return false;
			}
			$key_str = "";
			$val_str = "";
			foreach( $arrinfos as $key => $arrinfo )
			{
				$key_str .= "`".$key."`,";
				$val_str .= "'".$this->handleIntString($arrinfo)."',";
			}
			$key_str = substr($key_str,0,-1);
			$val_str = substr($val_str,0,-1);
			$sql = " INSERT INTO normalmerchant_approval (".$key_str.") VALUES(".$val_str.") ";

			$this->_objMysql->query($sql);
			return $this->_objMysql->getLastInsertId();
		}

		public function updatePortalInfos($arrinfos="",$whereStr){
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$update_str = "";

			foreach( $arrinfos as $key => $arrinfo )
			{
				$update_str .= "`".$key."` = '".$this->handleIntString($arrinfo)."',";
			}

			$update_str = substr($update_str,0,-1);
			$sql = " UPDATE normalmerchant_approval SET ".$update_str.$whereStr;

			$this->_objMysql->query($sql);
			return true;
		}

		public function checkPortalRows($whereStr){
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = " SELECT COUNT(*) AS count FROM normalmerchant_approval ".$whereStr;

			$qryId = $this->_objMysql->query($sql);
			$arrTmp = $this->_objMysql->getRow($qryId);
			$this->_objMysql->freeResult($qryId);
			if( $arrTmp["count"] > 0 ){
				return $arrTmp["count"];
			}else{
				return false;
			}
		}

		public function getPortalInfos($select="",$where="",$groupby="",$orderby="",$limit=""){
			
			$select = ($select  != "") ? $select : "*";
			$where = ($where   != "") ? " WHERE ".$where : "";
			$orderby = ($orderby != "") ? " ORDER BY ".$orderby : "";
			$groupby = ($groupby != "") ? " GROUP BY ".$groupby : "";
			$limit = ($limit   != "") ? " LIMIT ".$limit : "";
			$sql = " SELECT ".$select." FROM normalmerchant_approval ".$where.$groupby.$orderby.$limit;

			$qryId = $this->_objMysql->query($sql);
			$rtn = array();
			while($arrTmp = $this->_objMysql->getRow($qryId)){
				$rtn[] = $arrTmp;	
			}
			$this->_objMysql->freeResult($qryId);
			if( !empty($rtn) ){
				return $rtn;
			}else{
				return false;
			}
		}

		public function checkAndmkdirBySessionId($sessionid="",$parentdir=""){
			if( $parentdir == "" ){
				$parentdir = INCLUDE_ROOT."image/extMerchant/";
			}

			if( !is_dir($parentdir) ){
				if( !@mkdir($parentdir, 0777) )
				{
					return false;
				}
			}

//			if( !is_dir($parentdir.$sessionid) ){
//				if( !@mkdir($parentdir.$sessionid, 0777) )
//				{
//					return false;
//				}
//			}
//			return $parentdir.$sessionid;
			
			return $parentdir;
		}

		public static function catchWebImagebyUrl($httpurl,$width,$height,$savepath,$savename){
				
			$browser = new COM("InternetExplorer.Application");
			$handle = $browser->HWND;
			$browser->Visible = true;
			$browser->Fullscreen = true;

			$browser->Navigate($httpurl);
			while ($browser->Busy) {
				com_message_pump(4000);
			}
			$im = imagegrabwindow($handle, 0);
			$new_img = imagecreatetruecolor($width,$height);
			imagecopyresampled($new_img,$im,0,0,0,0,$width,$height,imagesx($im),imagesy($im));
			imagejpeg($new_img, $savepath.$savename,100);

			$browser->Quit();
		}

		public static function url_exists($url){
			/*
			$head = @get_headers($url);
			if( is_array($head) ){
				$t = explode(" ",$head[0]);
				if( strtoupper($t[count($t)-1]) == "OK" ){
					return true;
				}
				else{
					return false;
				}
			}else{
				return false;
			}
			*/
			$rtn = @file($url);
			if($rtn === false)
				return false;
			else
				return true;
		}

		public static function Fsize($file,$conversion=0,$unit=1024,$locate=1) {
			$a = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
			$pos = 0;
			$size = $file;
			if($conversion==0) {
				$size = filesize($file);
			}

			while ($size >= $unit && $pos < $locate) {
				$size /= $unit;

				$pos++;
			}

			//return round($size,2)." ".$a[$pos];
			return round($size,2);
		}
	}
}
?>