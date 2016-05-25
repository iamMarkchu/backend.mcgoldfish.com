<?
if (!defined("__MOD_CLASS_POLL__"))
{
	define("__MOD_CLASS_POLL__",1);

	class Poll	{
		
		private $_objMysql;
		private $m_cn;
		
		public function __construct($objMysql){
			$this->_objMysql = $objMysql;
		}
		
		/**
		 * desc 判断查询polls_iswork 满足条件的查询语句是否存�?
		 * @params string where
		 * @return bool  true|false
		*/
		public function checkPollRows($whereStr){
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = " SELECT COUNT(*) AS count FROM polls_iswork ".$whereStr;
			$qryId = $this->_objMysql->query($sql);
			$arrTmp = $this->_objMysql->getRow($qryId);
			$this->_objMysql->freeResult($qryId);
			if( $arrTmp["count"] > 0 ){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * desc polls_iswork增加数据
		 * @params array $arrinfos 数据数组
		 * @return bool false|true
		*/
		public function setPollInfos($arrinfos=""){
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
			$key_str	 = substr($key_str,0,-1);
			$val_str = substr($val_str,0,-1);
			$sql = " INSERT INTO polls_iswork (".$key_str.") VALUES(".$val_str.") ";
			$this->_objMysql->query($sql);
			return true;
		}

		/**
		 * desc polls_iswork获取数据
		 * @params string $select="",$where="",$groupby="",$orderby="",$limit=""
		 * @return array rtn 结果数组
		*/
		public function getPollInfos($select="",$where="",$groupby="",$orderby="",$limit=""){
			
			$select = ($select  != "") ? $select : "*";
			$where = ($where   != "") ? " WHERE ".$where : "";
			$orderby = ($orderby != "") ? " ORDER BY ".$orderby : "";
			$groupby = ($groupby != "") ? " GROUP BY ".$groupby : "";
			$limit = ($limit   != "") ? " LIMIT ".$limit : "";
			$sql = " SELECT ".$select." FROM polls_iswork ".$where.$groupby.$orderby.$limit;
			$qryId = $this->_objMysql->query($sql);
			$rtn = array();
			while($arrTmp = $this->_objMysql->getRow($qryId)){
				$rtn[]	 = $arrTmp;	
			}
			$this->_objMysql->freeResult($qryId);
			if( !empty($rtn) ){
				return $rtn;
			}else{
				return false;
			}
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
		
		/**
		 * 数据库安全转�? SQL 语句中使用的字符串中的特殊字�?
		 *
		 * @param string $string
		 * @return string
		 */
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
	}
}
?>