<?php
class Channel
{
	var $objMysql;
	var $channels_by_url_name = array();
	function Channel($objMysql)
	{
		$this->objMysql = $objMysql;
	}
	
	function getChannelByUrlName($_channel_url_name)
	{
		if(isset($this->channels_by_url_name[$_channel_url_name])) return $this->channels_by_url_name[$_channel_url_name];
		$sql = "select * from channel where ChannelUrlName = '" . addslashes($_channel_url_name) . "'";
		$arr = $this->objMysql->getFirstRow($sql);
		if(isset($arr["TagIds"]))
		{
			$oTag = new Tag($this->objMysql);
			$arr["TagIds"] = $oTag->GetTagsByMergedTagID($arr["TagIds"],"allinvolvetagid_string");
		}
		$this->channels_by_url_name[$_channel_url_name] = $arr;
		return $arr;
	}
}//end class
?>
