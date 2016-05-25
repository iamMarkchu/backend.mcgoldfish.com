<?php
class SiteConfig
{
	function __construct($oMysql)
	{
		$this->objMysql = $oMysql;
	}
	
	function getGlobalConfig($config_name)
	{
		$sql = "select * from site_config where `Key` = '" . addslashes($config_name) . "'";
		$value = $this->objMysql->getFirstRow($sql);
		return $value;
	}
	
	function get($config_name, $default="")
	{
		$sql = "select `Value` from site_config where `Key` = '" . addslashes($config_name) . "'";
		$value = $this->objMysql->getFirstRowColumn($sql);
		if(!$value) return $default;
		return $value;
	}
	
	function set($config_name, $config_value)
	{
		$sql = "insert into site_config(`Key`, `Value`) values ('" . addslashes($config_name) . "', '" . addslashes($config_value) . "') ON DUPLICATE KEY UPDATE `Value` = VALUES(`Value`)";
		$this->objMysql->query($sql);
	}
}
?>