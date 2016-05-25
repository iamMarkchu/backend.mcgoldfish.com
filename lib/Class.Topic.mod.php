<?php
/*
 * FileName: Class.Topic.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_TOPIC__"))
{
	define("__MOD_CLASS_TOPIC__",1);
	include_once(INCLUDE_ROOT . "func/front.func.php");
	class Topic
	{
		private $objMysql;
		private $name;
		private $displayName;
		private $originalURI;
		private $targetURI;
		private $originalType;
		private $termType;
		private $footwords;
		private $language;
	
		private $meta_Title;
		private $meta_Keyword;
		private $meta_Description;
		
		private $isNull = false;
		/**
	 * @return the $TermType
	 */
	public function getTermType() {
		return $this->termType;
	}

		/**
	 * @param field_type $TermType
	 */
	public function setTermType($TermType) {
		$this->termType = $TermType;
	}

	/**
	 * @return the $isNull
	 */
	public function IsNull() {
		return $this->isNull;
	}

		/**
	 * @param field_type $isNull
	 */
	private function setNull() {
		$this->isNull = true;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

		/**
	 * @return the $displayName
	 */
	public function getDisplayName() {
		return $this->displayName;
	}

		/**
	 * @return the $originalURI
	 */
	public function getOriginalURI() {
		return $this->originalURI;
	}

		/**
	 * @return the $targetURI
	 */
	public function getTargetURI() {
		return $this->targetURI;
	}

		/**
	 * @return the $meta_Title
	 */
	public function getMeta_Title() {
		return $this->meta_Title;
	}

		/**
	 * @return the $meta_Keyword
	 */
	public function getMeta_Keyword() {
		return $this->meta_Keyword;
	}

		/**
	 * @return the $meta_Description
	 */
	public function getMeta_Description() {
		return $this->meta_Description;
	}

		/**
	 * @return the $originalType
	 */
	public function getOriginalType() {
		return $this->originalType;
	}

		/**
	 * @return the $footwords
	 */
	public function getFootwords() {
		return $this->footwords;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
		/**
	 * @param field_type $displayName
	 */
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}

		/**
	 * @param field_type $OriginalURI
	 */
	public function setOriginalURI($OriginalURI) {
//		$OriginalURI = format_topic_uri($OriginalURI);
		$OriginalURI = addDirectorySeparator($OriginalURI);
		$this->originalURI = $OriginalURI;
	}

		/**
	 * @param field_type $targetURI
	 */
	public function setTargetURI($targetURI) {
//		$targetURI = format_topic_uri($targetURI);
		$targetURI = addDirectorySeparator($targetURI);
		$this->targetURI = $targetURI;
	}

		/**
	 * @param field_type $meta_Title
	 */
	public function setMeta_Title($meta_Title) {
		$this->meta_Title = $meta_Title;
	}

		/**
	 * @param field_type $meta_Keyword
	 */
	public function setMeta_Keyword($meta_Keyword) {
		$this->meta_Keyword = $meta_Keyword;
	}

		/**
	 * @param field_type $meta_Description
	 */
	public function setMeta_Description($meta_Description) {
		$this->meta_Description = $meta_Description;
	}

		/**
	 * @param field_type $originalType
	 */
	public function setOriginalType($originalType) {
		$this->originalType = $originalType;
	}
	
	public function setLanguage($language) {
		$this->language = $language;
	}

		/**
	 * @param field_type $footwords
	 */
	public function setFootwords($footwords) {
		$this->footwords = $footwords;
	}

		function Topic($objMysql,$name)
		{
			$this->objMysql = $objMysql;
			$this->name = $name;
			$info = $this->getTopic();
			if ($info) {
				$this->displayName = $info["DisplayName"];
				$this->originalURI = $info["OriginalURI"];
				$this->targetURI = $info["TargetURI"];
				$this->originalType = $info["OriginalType"];
				$this->termType = $info["TermType"];
				$this->footwords = $info["Footwords"];
				$this->meta_Title = $info["Meta_Title"];
				$this->meta_Keyword = $info["Meta_Keyword"];
				$this->meta_Description = $info["Meta_Description"];
				$this->displayName = $info["DisplayName"];
				$this->language = $info["Language"];
			}else {
				$this->setNull();
			}
		}

		function getTopicCount($whereStr="")
		{
			$total = 0;
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = "select count(*) as cnt from topic $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return $total;
		}

		function getTopicListByLimitStr($limitStr="", $whereStr="", $orderStr=" ID DESC ")
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select * from topic $whereStr $orderStr $limitStr";
//			echo $sql;
			$res = $this->objMysql->getRows($sql);
			return $res;
		}
		
		function getTopic()
		{
			$topicName = trim($this->getName());
			if(!$topicName) 
				return array();
			$res = $this->getTopicListByLimitStr("", "Name = '".addslashes($topicName)."'");
			if(isset($res[0]))
				return $res[0];
			return array();			 
		}
		
		function addTopic()
		{
			$name = addslashes($this->getName());
			$displayName = addslashes($this->getDisplayName());
			$metaTitle = addslashes($this->getMeta_Title());
			$metaKw = addslashes($this->getMeta_Keyword());
			$metaDesc = addslashes($this->getMeta_Description());
			
			$originalUri = addslashes($this->getOriginalURI());
			$targetUri = addslashes($this->getTargetURI());
			$originalType = addslashes($this->getOriginalType());
			$termType = addslashes($this->getTermType());
			$footwords = addslashes($this->getFootwords());
			
			$editor = addslashes(trim(get_user()));
			$language = addslashes(trim($this->getLanguage()));
			
			$sql = "insert into topic (Name, DisplayName, Meta_Title, Meta_Keyword, Meta_Description, OriginalURI, TargetURI, OriginalType, TermType, Footwords, AddTime, Editor,Language)";
			$sql .= "values ('$name', '$displayName', '$metaTitle', '$metaKw', '$metaDesc', '$originalUri', '$targetUri', '$originalType', '$termType', '$footwords', NOW(), '$editor','$language')";
			$this->objMysql->query($sql);
		}
		
		function updateTopic()
		{
			$name = addslashes($this->getName());
			$displayName = addslashes($this->getDisplayName());
			$metaTitle = addslashes($this->getMeta_Title());
			$metaKw = addslashes($this->getMeta_Keyword());
			$metaDesc = addslashes($this->getMeta_Description());
			$originalUri = addslashes($this->getOriginalURI());
			$targetUri = addslashes($this->getTargetURI());
			$originalType = addslashes($this->getOriginalType());
			$termType = addslashes($this->getTermType());
			$footwords = addslashes($this->getFootwords());
			
			$editor = addslashes(trim(get_user()));
			$language = addslashes(trim($this->getLanguage()));
			$sql = "Update topic set 
			DisplayName = '$displayName', 
			Meta_Title = '$metaTitle', 
			Meta_Keyword = '$metaKw', 
			Meta_Description = '$metaDesc', 
			OriginalURI = '$originalUri', 
			TargetURI = '$targetUri', 
			OriginalType = '$originalType', 
			TermType = '$termType', 
			Footwords = '$footwords', 
			 Editor = '$editor',
			 Language = '$language'  
			 Where Name = '$name'  ";
			$this->objMysql->query($sql);
		}
		
		function getAllTopic() {
			$sql = "SELECT * FROM topic Order by DisplayName";
			$res = $this->objMysql->getRows($sql);
			return $res; 
		}
		
	}
}
?>
