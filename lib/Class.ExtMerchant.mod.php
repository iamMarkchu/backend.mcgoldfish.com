<?

if (!defined("__MOD_CLASS_EXTMERCHANT__"))
{
	define("__MOD_CLASS_EXTMERCHANT__",1);
	
	class ExtMerchant	{
		
		protected $_objMysql;
		
		public function __construct($objMysql){
			$this->_objMysql = $objMysql;
		}

		public function setName($name){
			$this->_name = $name;
		}


		public function quote_smart($string)
		{
			if (get_magic_quotes_gpc()) $value = stripslashes($string);
			if (!is_numeric($value)){
				if (version_compare(phpversion(), "4.3.0") == "-1") {
					$value = mysql_escape_string($string);
				}
				elseif (is_resource($this->m_cn)) {
					$value = mysql_real_escape_string($string, $this->m_cn);
				}else{
					$value = $string;
				}
			}
			return $value;
		}

		public function handleIntString($string){
			if( is_string($string) ){
				return $this->quote_smart($string);
			}elseif( is_integer($string) ){
				return (int)$string;
			}else{
				return $string;
			}
		}

		public function setTableInfos($arrinfos=""){
			if( $arrinfos == "" || $this->_name == ""){
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
			$sql = " INSERT INTO ".$this->_name." (".$key_str.") VALUES(".$val_str.") ";

			$this->_objMysql->query($sql);
			return $this->_objMysql->getLastInsertId();
		}

		public function updateTableInfos($arrinfos="",$whereStr){
			if( $arrinfos == "" || $this->_name == ""){
				return false;
			}

			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$update_str = "";

			foreach( $arrinfos as $key => $arrinfo )
			{
				$update_str .= "`".$key."` = '".$this->handleIntString($arrinfo)."',";
			}

			$update_str = substr($update_str,0,-1);
			$sql = " UPDATE ".$this->_name." SET ".$update_str.$whereStr;

			$this->_objMysql->query($sql);
			return true;
		}

		public function checkTableRows($whereStr){
			if( $this->_name == ""){
				return false;
			}

			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = " SELECT COUNT(*) AS count FROM ".$this->_name." ".$whereStr;
			$qryId = $this->_objMysql->query($sql);
			$arrTmp = $this->_objMysql->getRow($qryId);
			$this->_objMysql->freeResult($qryId);
			if( $arrTmp["count"] > 0 ){
				return $arrTmp["count"];
			}else{
				return false;
			}
		}

		public function getTableInfos($select="",$where="",$groupby="",$orderby="",$limit=""){
			if( $this->_name == ""){
				return false;
			}
			
			$select = ($select  != "") ? $select : "*";
			$where = ($where   != "") ? " WHERE ".$where : "";
			$orderby = ($orderby != "") ? " ORDER BY ".$orderby : "";
			$groupby = ($groupby != "") ? " GROUP BY ".$groupby : "";
			$limit = ($limit   != "") ? " LIMIT ".$limit : "";
			$sql = " SELECT ".$select." FROM ".$this->_name." ".$where.$groupby.$orderby.$limit;
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
	}
}
?>