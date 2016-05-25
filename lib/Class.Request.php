<?php
class Request
{
	function getStr($name,$default="",$toEncoding="")
	{
		if(isset($_REQUEST["$name"])) $sReturn = $_REQUEST["$name"];
		else $sReturn = "";
		if (!get_magic_quotes_gpc()) $sReturn = addslashes($sReturn);
		if($sReturn === "") return $default;
		if($toEncoding != "") $sReturn = iconv("UTF-8",$toEncoding,$sReturn);
		return $sReturn;
	}
	
	function getStrNoSlashes($name,$default="",$toEncoding="")
	{
		if(isset($_REQUEST["$name"])) $sReturn = $_REQUEST["$name"];
		else $sReturn = "";
		if (get_magic_quotes_gpc()) $sReturn = stripslashes($sReturn);
		if($sReturn === "") return $default;
		if($toEncoding != "") $sReturn = iconv("UTF-8",$toEncoding,$sReturn);
		return $sReturn;
	}
	
	function getNumber($name,$default="0")
	{
		if(isset($_REQUEST["$name"])) $iReturn = $_REQUEST["$name"];
		else $iReturn = "";
		$iReturn = trim($iReturn);
		if(! is_numeric($iReturn)) return $default;
		else return $iReturn;
	}

	function get_session($name)
	{
		if(isset($_SESSION["$name"])) return $_SESSION["$name"];
		else return "";
	}

	function set_session($name,$value)
	{
		$_SESSION["$name"] = $value;
	}

	function get_url_para($_ArrWhere,$_per="&",$_ArrSet=array(),$_ArrSkip=array())
	{
		foreach($_ArrSet as $k => $v) $_ArrWhere[$k] = $v;
		$arrKV = array();
		foreach($_ArrWhere as $k => $v)
		{
			if($_ArrSkip[$k] == 1) continue;
			$arrKV[] = $k . "=" . urlencode($v);
		}

		if(sizeof($arrKV) > 0) return($_per . implode("&",$arrKV));
		else return "";
	}
	
}//end class
?>