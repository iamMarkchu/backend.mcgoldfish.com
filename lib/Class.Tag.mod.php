<?php
/*
 * FileName: Class.Tag.mod.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_TAG__"))
{
	define("__MOD_CLASS_TAG__",1);
   
	class Tag
	{
		var $objMysql;

		function Tag($objMysql)
		{
			$this->objMysql = $objMysql;
		}

		function getTagCount($whereStr="")
		{
			$total = 0;
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$sql = "select count(*) as cnt from tag $whereStr";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$this->objMysql->freeResult($qryId);
			$total = intval($arrTmp['cnt']);
			return $total;
		}

		function getTagTypeListByLimitStr($limitStr="", $whereStr="", $orderStr=" `Name` ASC ", $onlyRtnId=false){
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, `Name`, 	ImageTemplate , ShortDescTemplate, LongDescTemplate, AddTime from tag_type $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($onlyRtnId)
				{
					$arr[$i] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['name'] = trim($arrTmp['Name']);
					$arr[$i]['imagetemplate'] = trim($arrTmp['ImageTemplate']);
					$arr[$i]['shortdesctemplate'] = trim($arrTmp['ShortDescTemplate']);
					$arr[$i]['longdesctemplate'] = trim($arrTmp['LongDescTemplate']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
				}
				$i++;

				unset($arrTmp);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getTagTypeListByTagTypeID($nTagTypeID){
			$arrResult = $this->getTagTypeListByLimitStr(" LIMIT 1", "ID = $nTagTypeID", "", false);
			return $arrResult[0];
		}

		function getTagTypeFieldListByLimitStr($limitStr="", $whereStr="", $orderStr=" FieldName ASC ", $onlyRtnId=false){
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, TagTypeID, FieldName , FieldTitle, FieldType, EditorTips, AddTime from tag_type_field $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($onlyRtnId)
				{
					$arr[$i] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['tagtypeid'] = trim($arrTmp['TagTypeID']);
					$arr[$i]['fieldname'] = trim($arrTmp['FieldName']);
					$arr[$i]['fieldtitle'] = trim($arrTmp['FieldTitle']);
					$arr[$i]['fieldtype'] = trim($arrTmp['FieldType']);
					$arr[$i]['editortips'] = trim($arrTmp['EditorTips']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
				}
				$i++;

				unset($arrTmp);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getTagTypeFieldListByTagTypeID($TagTypeID)
		{
			$TagTypeID	 = intval($TagTypeID);
			$arrTmp = $this->getTagTypeFieldListByLimitStr("", "TagTypeID = $TagTypeID");
			return $arrTmp;
		}

		function getTagTypeValueByLimitStr($TagTypeID, $limitStr="", $whereStr="", $orderStr=""){
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$strTableName = 'tag_type_value_'.$TagTypeID;
			$sql = "select * from $strTableName $whereStr $orderStr $limitStr";
			//echo $sql;
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arr[$i] = $arrTmp;

				$i++;

				unset($arrTmp);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getTagTypeValueByTagID($TagTypeID, $TagID)
		{
			$TagID	 = intval($TagID);
			$TagTypeID	 = intval($TagTypeID);
			$arrTmp = $this->getTagTypeValueByLimitStr($TagTypeID, " LIMIT 1 ", "TagID = $TagID");
			return $arrTmp;
		}

		function UpdateTagTypeValueByTagID($TagTypeID, $TagID, $arrTagTypeFieldValue){
			$strTableName = 'tag_type_value_'.$TagTypeID;

			$arrTagTypeField = $this->getTagTypeFieldListByTagTypeID($TagTypeID);
			$arrTagTypeFieldName = array();
			foreach($arrTagTypeField as $arrCat)
			{
				$arrTagTypeFieldName[$arrCat['id']] = $arrCat['fieldname'];
				unset($arrCat);
			}
			unset($arrTagTypeField);

			$sql = '';
			$sqlvalue = '';
			$nCount = 0;
			foreach($arrTagTypeFieldValue as $k => $v){
				$strFieldID = substr($k, strlen('tagtype_field_'));
				$strFieldName = $arrTagTypeFieldName[$strFieldID];

				$sql .= ", `$strFieldName`";
				$sqlvalue .= ", \"".addslashes(trim($v))."\"";

				$nCount++;
				unset($k);
				unset($v);
			}
			$sql = "REPLACE INTO $strTableName (TagID" . $sql . ", AddTime)VALUES($TagID" . $sqlvalue . ", NOW())";
			echo $sql;
			$this->objMysql->query($sql);
		}

		function getTagByTagIds($_arrIds,$orderStr="")
		{
			if(empty($_arrIds)) return array();
			if(is_string($_arrIds))
			{
				$arr_temp = explode(",",$_arrIds);
				$_arrIds = array();
				foreach($arr_temp as $id) if(is_numeric($id)) $_arrIds[$id] = $id;
			}
			$arr_tags = array();
			$arr = $this->getTagListByLimitStr("","ID in (".implode(",",$_arrIds).")",$orderStr);
			foreach($arr as $tag)
			{
				$tag["TagPageUrl"] = get_rewrited_url('tag', $tag["name"], $tag["id"]);
				$arr_tags[$tag["id"]] = $tag;
			}
			if($orderStr == "")
			{
				//resort
				$arr_temp = array();
				foreach($_arrIds as $_id)
				{
					if(isset($arr_tags[$_id])) $arr_temp[$_id] = $arr_tags[$_id];
				}
				$arr_tags = $arr_temp;
			}
			return $arr_tags;
		}
		
		function getTagListByLimitStr($limitStr="", $whereStr="", $orderStr=" TagName ASC ", $onlyRtnId=false)
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, TagName, TagTypeID, ShortDesc, LongDesc, BlogArticle, Image, MetaTitle, MetaKeyword, MetaDesc, ";
			$sql .= "SEOTitle, Alias, AllCouponCnt, ActiveCouponCnt, AddTime from tag $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($onlyRtnId)
				{
					$arr[$i] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['name'] = trim($arrTmp['TagName']);
					$arr[$i]['tagtypeid'] = trim($arrTmp['TagTypeID']);
					$arr[$i]['shortdesc'] = trim($arrTmp['ShortDesc']);
					$arr[$i]['longdesc'] = trim($arrTmp['LongDesc']);
					$arr[$i]['blog'] = trim($arrTmp['BlogArticle']);
					$arr[$i]['img'] = trim($arrTmp['Image']);
					$arr[$i]['metaTitle'] = trim($arrTmp['MetaTitle']);
					$arr[$i]['metakw'] = trim($arrTmp['MetaKeyword']);
					$arr[$i]['metadesc'] = trim($arrTmp['MetaDesc']);
					$arr[$i]['seotitle'] = trim($arrTmp['SEOTitle']);
					$arr[$i]['alias'] = $arrTmp['Alias'] ? trim($arrTmp['Alias']) : trim($arrTmp['TagName']);
					$arr[$i]['aliasTagList'] = $arrTmp['Alias'] ? trim($arrTmp['Alias']) : "";
					$arr[$i]['allcouponcnt'] = trim($arrTmp['AllCouponCnt']);
					$arr[$i]['activecouponcnt'] = trim($arrTmp['ActiveCouponCnt']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
				}
				$i++;

				unset($arrTmp);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getTagAllInfoByTagID($tagID)
		{
			$arrRes = array();
			$tagID	 = intval($tagID);
			$arrTmp = $this->getTagListByLimitStr("Limit 0, 1", "ID = $tagID");
			if(isset($arrTmp[0]))
			{
				$arrRes = $arrTmp[0];
				$arrRtn = $this->getTagRelationshipByTagID($tagID);
				if(isset($arrRtn[$tagID]['mer']))
					$arrRes['mer'] = $arrRtn[$tagID]['mer'];
				if(isset($arrRtn[$tagID]['cat']))
					$arrRes['cat'] = $arrRtn[$tagID]['cat'];
			}
			return $arrRes;
		}
		
		function getAllTags($key='id', $case='lc',$arrId=array())
		{
			$arrTags = array();
			if(sizeof($arrId))
			{
				$whereStr = "ID in (" . implode(",",$arrId) . ")";
				$arrTmp = $this->getTagListByLimitStr("",$whereStr);
			}
			else 
			{
				$arrTmp = $this->getTagListByLimitStr();
			}
			
			foreach($arrTmp as $v)
			{
				$id = intval($v['id']);
				$name = trim($v['name']);
				if($case == 'lc')
				{
					$name = strtolower($name);
				}
				else if($case == 'uc')
				{
					$name = strtoupper($name);
				}
				if(!$id || !$name) continue;
				if($key == 'id')
				{
					$arrTags[$id] = $name;
				}
				else
				{
					$arrTags[$name] = $id;
				}
			}
			return $arrTags;
		}
		
		function addTag($tag,$addsource="")
		{
			$tid = 0;
			$tag = addslashes(trim($tag));
			$sql = "SELECT TagID, MergedTagID from tag_merged_info where MergedTagName = '$tag' order by ID desc limit 1";
			$qryId = $this->objMysql->query($sql);
			if($arrTmp = $this->objMysql->getRow($qryId))
			{
				$tid = intval($arrTmp['TagID']);
			}
			$this->objMysql->freeResult($qryId);
			
			if(!$tid)
			{
				$sql = "SELECT ID from tag where TagName = '$tag'";
				$qryId = $this->objMysql->query($sql);
				if($arrTmp = $this->objMysql->getRow($qryId))
				{
					$tid = intval($arrTmp['ID']);
				}
			}
			
			if(!$tid)
			{
				$editorname = $_SERVER['PHP_AUTH_USER'] ? $_SERVER['PHP_AUTH_USER'] : $_SERVER["REMOTE_USER"];
				if(empty($addsource)){
					$addsource='Coupon';
				}
				$sql = "INSERT INTO tag_pending (TagName,Status,EditorAdd,AddTime,AddSource) VALUES('$tag','Pending','$editorname',now(),'$addsource')";
				$qryId = $this->objMysql->query($sql);
				$tid = $this->objMysql->getLastInsertId($qryId);
			}
			return $tid;
		}
		
		function AddTagWithAllInfo($tagName, $tagtype, $shortDesc, $longDesc, $blog, $img, 
						$metaTitle, $metakw, $metaDesc, $seoTitle, $alias, $arrCat="", $arrMer="", $allcouponCnt=0, $activeCouponCnt=0)
		{
			$tagName = addslashes(trim($tagName));
			$tagtype = addslashes(trim($tagtype));
			$shortDesc = addslashes(trim($shortDesc));
			$longDesc = addslashes(trim($longDesc));
			$img = addslashes(trim($img));
			$blog = addslashes(trim($blog));
			$metaTitle = addslashes(trim($metaTitle));
			$metakw = addslashes(trim($metakw));
			$metaDesc = addslashes(trim($metaDesc));
			$seoTitle = addslashes(trim($seoTitle));
			$alias = addslashes(trim($alias));
			$allcouponCnt = intval($allcouponCnt);
			$activeCouponCnt = intval($activeCouponCnt);
			
			$sql = "INSERT INTO tag(TagName, TagTypeID, ShortDesc, LongDesc, BlogArticle, Image, MetaTitle, MetaKeyword, MetaDesc,";
			$sql .= " SEOTitle, Alias, AllCouponCnt, ActiveCouponCnt, AddTime) VALUES('$tagName', '$tagtype', '$shortDesc','$longDesc','$blog','$img','$metaTitle','$metakw','$metaDesc',
			'$seoTitle','$alias',$allcouponCnt, $activeCouponCnt, NOW())";
			$qryId = $this->objMysql->query($sql);
			$tagID = $this->objMysql->getLastInsertId($qryId);
			if(is_array($arrCat) && count($arrCat))
			{
				$this->addTagRelationShipByTag($tagID, 'cat', $arrCat);
			}
			
			if(is_array($arrMer) && count($arrMer))
			{
				$this->addTagRelationShipByTag($tagID, 'mer', $arrMer);
			}
			return $tagID;
		}
		
		function addTagRelationShipByTag($tagID, $type, $arrValue)
		{
			if(!$tagID || !$type || !is_array($arrValue) || !count($arrValue))
				return false;
			$insertSql = "Replace into tag_relationship(TagID, `Type`, `Value`, AddTime) VALUES";
			$needInsert = false;
			foreach($arrValue as $v)
			{
				$v = intval($v);
				if(!$v) continue;
				$insertSql .= "($tagID, '$type', $v, NOW()),";
				$needInsert = true;
			}
			if(!$needInsert) return false;
			$insertSql = rtrim($insertSql, ",");
			$this->objMysql->query($insertSql);
			
			//refresh lastchagngetime for tag table
			$sql = "update tag set LastChangetime = Now() where ID  = $tagID ";
			$this->objMysql->query($sql);
		}
		
		function addTagRelationShipByType($value, $type, $arrTag)
		{
			if(!$value || !$type || !is_array($arrTag) || !count($arrTag))
				return false;
			$insertSql = "Replace into tag_relationship(TagID, `Type`, `Value`, AddTime) VALUES";
			$needInsert = false;
			foreach($arrTag as $v)
			{
				$v = intval($v);
				if(!$v) continue;
				$insertSql .= "($v, '$type', $value, NOW()),";
				$needInsert = true;
			}
			if(!$needInsert) return false;
			$insertSql = rtrim($insertSql, ",");
			//echo $insertSql;
			$this->objMysql->query($insertSql);
			
			//refresh lastchagngetime for tag table
			$sql = "update tag set LastChangetime = Now() where ID in (".implode(",", $arrTag).") ";
			$this->objMysql->query($sql);
		}
		
		
		
		function EditTagWithAllInfo($tagID,$tagName,$tagtype, $oldtagtype, $shortDesc, $longDesc, $blog, $img, 
						$metaTitle, $metakw, $metaDesc, $seoTitle, $alias, $arrCat="", $arrMer="", $allcouponCnt=0, $activeCouponCnt=0, $arrTagTypeFields='')
		{
			$tagID = intval($tagID);
			$tagName = addslashes(trim($tagName));
			$tagtype = addslashes(trim($tagtype));
			$shortDesc = addslashes(trim($shortDesc));
			$longDesc = addslashes(trim($longDesc));
			$img = addslashes(trim($img));
			$blog = addslashes(trim($blog));
			$metaTitle = addslashes(trim($metaTitle));
			$metakw = addslashes(trim($metakw));
			$metaDesc = addslashes(trim($metaDesc));
			$seoTitle = addslashes(trim($seoTitle));
			$alias = addslashes(trim($alias));
			$allcouponCnt = intval($allcouponCnt);
			$activeCouponCnt = intval($activeCouponCnt);
			
			$sql = "update tag set TagName = '$tagName',TagTypeID = '$tagtype', ShortDesc = '$shortDesc', LongDesc = '$longDesc', BlogArticle ='$blog',";
			$sql .= " Image = '$img', MetaTitle = '$metaTitle', MetaKeyword = '$metakw', MetaDesc = '$metaDesc',";
			$sql .= " SEOTitle = '$seoTitle', Alias = '$alias' Where ID = $tagID";
			$this->objMysql->query($sql);
			
			$this->objMysql->query("delete from tag_relationship where TagID = $tagID");
			
			if(is_array($arrCat) && count($arrCat))
			{
				$this->addTagRelationShipByTag($tagID, 'cat', $arrCat);
			}
			
			if(is_array($arrMer) && count($arrMer))
			{
				$this->addTagRelationShipByTag($tagID, 'mer', $arrMer);
			}

			// add by ran 2009-08-22 
			// ���Typeû�б仯,��Ϊ��������,�򱣴��������������ݱ�.
			// �����ģ�����Long Description
			if (($tagtype == $oldtagtype) && ($tagtype > 0)){
				// save to specific tag type table
				if(is_array($arrTagTypeFields) && count($arrTagTypeFields))
				{
					$this->UpdateTagTypeValueByTagID($tagtype, $tagID, $arrTagTypeFields);
					$this->UpdateTagLongDescByTagTypeValue($tagtype, $tagID);
				}
			}
			return;
		}
		
		function UpdateTagLongDescByTagTypeValue($tagtype, $tagID){
			$objTpl = new rFastTemplate();

			//get template
			$arrTagTypeInfo = $this->getTagTypeListByTagTypeID($tagtype);
			$strLongDescTemplate = stripslashes($arrTagTypeInfo['longdesctemplate']);
			//
			
			//get tag type feilds
			$arrTagTypeFieldList = $this->getTagTypeFieldListByTagTypeID($tagtype);
			//

			//get tag info 
			$arrTagInfo = $this->getTagAllInfoByTagID($tagID);
			//

			//get tag additional info
			$arrTagAdditionalInfoList = $this->getTagTypeValueByTagID($tagtype,$tagID);
			$arrTagAdditionalInfo = $arrTagAdditionalInfoList[0];
			//

			$objTpl->TEMPLATE['tagtemplate']['string'] = $strLongDescTemplate;
		    $objTpl->TEMPLATE['tagtemplate']['loaded'] = 1;

			$objTpl->assign(array(
				'tag_name' => $arrTagInfo['name'],
				'tag_alias' => $arrTagInfo['alias'],
				'tag_image_url' => $arrTagInfo['img'],
			));

			foreach($arrTagTypeFieldList as $arrTagTypeField){
				if ($arrTagTypeField['fieldtype'] == 'Text'){
					$objTpl->assign(array(
						'tag_'.$arrTagTypeField['fieldname'] => stripslashes($arrTagAdditionalInfo[$arrTagTypeField['fieldname']]),
					));
				}
				elseif ($arrTagTypeField['fieldtype'] == 'List'){
					$strFieldValue = trim(stripslashes($arrTagAdditionalInfo[$arrTagTypeField['fieldname']]));
					if ($strFieldValue != ''){
						$arrListItem = array();
						$strFieldValue = str_replace("\r", "", $strFieldValue);
						$arrListItem = explode("\n", $strFieldValue);

						$i = 0;
						foreach($arrListItem as $v)
						{
							$v = trim($v);
							if(!$v) continue;

							$objTpl->assign(array(
								'tag_'.$arrTagTypeField['fieldname'] => $v,
							));
							$objTpl->parse('OUT', '.block_tag_'.$arrTagTypeField['fieldname'].'_list');
				
							$i++;
							unset($v);
						}
						unset($arrListItem);
						$objTpl->parse('OUT', 'block_have_tag_'.$arrTagTypeField['fieldname'].'_list');
					}
				}
				elseif ($arrTagTypeField['fieldtype'] == 'Number'){
					$objTpl->assign(array(
						'tag_'.$arrTagTypeField['fieldname'] => stripslashes($arrTagAdditionalInfo[$arrTagTypeField['fieldname']]),
					));
				}
				elseif ($arrTagTypeField['fieldtype'] == 'Image'){
					$objTpl->assign(array(
						'tag_'.$arrTagTypeField['fieldname'] => stripslashes($arrTagAdditionalInfo[$arrTagTypeField['fieldname']]),
					));
				}
			}
			$objTpl->parse('OUT', 'tagtemplate');
			$strFilledTemplate = $objTpl->TEMPLATE['tagtemplate']['result'];
			echo $strFilledTemplate;
			//save to db
			$sql = "UPDATE tag SET `LongDesc` = \"".addslashes($strFilledTemplate)."\" WHERE ID = $tagID LIMIT 1";
			echo $sql;
			$this->objMysql->query($sql);
		}
		
		function deleteTag($TagID)
		{
			$TagID = intval($TagID);
			if(!$TagID) return;
			$sql = "select ID, Tag from normalcoupon where  (Tag LIKE '%,{$TagID},%'  OR Tag LIKE '{$TagID},%' OR Tag LIKE '%,{$TagID}' OR Tag = '{$TagID}') ";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qry))
			{
				$couponID = intval($arrTmp['ID']);
				$tagStr = trim($arrTmp['Tag']);
				$arrTag = explode(",", $tagStr);
				$arrNewTag = "";
				foreach($arrTag as $t)
				{
					if(!$t || $t == $TagID) continue;
					$arrNewTag[$t] = "";
				}
				$arrNewTagStr = "";
				if($arrNewTag)
				{
					$arrNewTagStr = implode(",", array_keys($arrNewTag));
				}
				$sql = "update normalcoupon set Tag = '$arrNewTagStr' where ID = $couponID";
				$this->TagOperationLog("Find coupon $couponID w/ to be deleted tag, change: $tagStr -> $arrNewTagStr");
				$this->objMysql->query($sql);
			}
			
			$sql = "delete from tag_relationship where TagID = $TagID";
			$this->TagOperationLog("Remove the relationship from tag_relationship for TagID: $TagID");
			$this->objMysql->query($sql);
			
			$sql = "delete from brand where TagID = $TagID";
			$this->TagOperationLog("Remove from brand for TagID: $TagID");
			$this->objMysql->query($sql);

			$sql = "delete from seasonalcalendar where TagID = $TagID";
			$this->TagOperationLog("Remove from seasonalcalendar for TagID: $TagID");
			$this->objMysql->query($sql);
			
			$sql = "delete from tag_related_tag where TagID = $TagID";
			$this->TagOperationLog("Remove from tag_related_tag for TagID: $TagID");
			$this->objMysql->query($sql);
			
			$sql = "delete from tag where ID = $TagID";
			$this->TagOperationLog("Remove from tag for TagID: $TagID");
			$this->objMysql->query($sql);
			
			return;
		}
		
		function merTag($arrSrcTagID, $dstTagID)
		{
			$dstTagID = intval($dstTagID);
			if(!is_array($arrSrcTagID) || count($arrSrcTagID) < 1)
				return;
			$arrRegexp = array();
			foreach($arrSrcTagID as $srcTag)
			{
				$arrRegexp[] = "(Tag LIKE '%,{$srcTag},%'  OR Tag LIKE '{$srcTag},%' OR Tag LIKE '%,{$srcTag}' OR Tag = '{$srcTag}')";
			}
			$sql = "select ID, Tag from normalcoupon where ".implode(' OR ', $arrRegexp);
			
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qry))
			{
				$couponID = intval($arrTmp['ID']);
				$tagStr = trim($arrTmp['Tag']);
				$arrTag = explode(",", $tagStr);
				$arrNewTag = array();
				foreach($arrTag as $t)
				{
					if(!$t) continue;
					if(in_array($t, $arrSrcTagID))
					{
						$arrNewTag[$dstTagID] = "";
					}
					else 
						$arrNewTag[$t] = "";
				}
				$arrNewTagStr = "";
				if($arrNewTag)
				{
					$arrNewTagStr = implode(",", array_keys($arrNewTag));
				}
				$arrNewTagStr = implode(",", array_keys($arrNewTag));
				$sql = "update normalcoupon set Tag = '$arrNewTagStr' where ID = $couponID";
				$this->TagOperationLog("Find coupon $couponID w/ to be merged tag, change: $tagStr -> $arrNewTagStr");
				$this->objMysql->query($sql);
			}
			
			//////////////get the old relationship and refresh it
			$arrNewTagRelation = array();
			$sql = "select `Type`, `Value` from tag_relationship where TagID in (".implode(",", $arrSrcTagID).") Or TagID = $dstTagID";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qry))
			{
				$tmpType = trim($arrTmp['Type']);
				$tmpVal = intval($arrTmp['Value']);
				$arrNewTagRelation[$tmpType][$tmpVal] = "";
			}
			//remove the current one
			$sql = "delete from tag_relationship where TagID in (".implode(",", $arrSrcTagID).") Or TagID = $dstTagID";
			$this->objMysql->query($sql);
			 
			//add the new one
			foreach($arrNewTagRelation as $k=>$v)
			{
				$this->addTagRelationShipByTag($dstTagID, $k, array_keys($v));
			}
			$this->TagOperationLog("Refresh the relationship from tag_relationship for TagID: $dstTagID");
			///////////////////end

			//get old tag name
			$arrToBeMergedTagName = array();
			$sql = "select ID, TagName from tag where ID in (".implode(",", $arrSrcTagID).")";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qry))
			{
				$arrToBeMergedTagName[intval($arrTmp['ID'])] = trim($arrTmp['TagName']);
			}
			$this->objMysql->freeResult($qry);

			//end
			$sql = "delete from tag where ID in (".implode(",", $arrSrcTagID).")";
			$this->TagOperationLog("Remove from tag for TagID: $dstTagID");
			$this->objMysql->query($sql);
			
			//update the merged info, for front end redirect
			$arrTagIdNeed2Merge = array();
			foreach($arrSrcTagID as $v)
			{
				$arrTagIdNeed2Merge[intval($v)] = isset($arrToBeMergedTagName[intval($v)]) ? $arrToBeMergedTagName[intval($v)] : '';
			}
			$qryId = $this->objMysql->query("select TagID, MergedTagID, MergedTagName from tag_merged_info where TagID in (".implode(",", $arrSrcTagID).")");
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrTagIdNeed2Merge[intval($arrTmp['MergedTagID'])] = trim($arrTmp['MergedTagName']);
			}
			$this->objMysql->freeResult($qryId);
			$sql = "insert into tag_merged_info(TagID, MergedTagID, MergedTagName, AddTime) VALUES ";
			foreach($arrTagIdNeed2Merge as $tid=>$tname)
			{
				$sql .= "($dstTagID, $tid, '".addslashes($tname)."', NOW()),";
			}
			$sql = rtrim($sql, ',');
			$this->objMysql->query($sql);
			$sql = "delete from tag_merged_info where TagID in (".implode(",", $arrSrcTagID).")";
			$this->objMysql->query($sql);
			//end

			return;
		}

		function GetTagsByMergedTagID($_ids,$_return_type)
		{
			$arr_tag_id = array();
			$temp_arr = is_array($_ids) ? $_ids : explode(",",$_ids);
			foreach($temp_arr as $_id)
			{
				$_id = trim($_id);
				if(is_numeric($_id)) $arr_tag_id[$_id] = $_id;
			}
			
			$sql = "select TagID,MergedTagID from tag_merged_info where MergedTagID in (".implode(",",$arr_tag_id).")";
			$arr_1 = $this->objMysql->getRows($sql,"MergedTagID");
			$arr_dest = array();
			foreach($arr_1 as $tag) $arr_dest[$tag["TagID"]] = $tag["TagID"];
			//
			if($_return_type == "allinvolvetagid_string" || $_return_type == "allinvolvetagid_array")
			{
				if(sizeof($arr_1))
				{
					foreach($arr_1 as $tag) $arr_tag_id[$tag["TagID"]] = $tag["TagID"];
					$arr_2 = $this->GetTagsByMergedTagID(array_values($arr_dest),"allinvolvetagid_array");
					foreach($arr_2 as $_id) $arr_tag_id[$_id] = $_id;
				}
				if($_return_type == "allinvolvetagid_array") return array_keys($arr_tag_id);
				else return implode(",",array_keys($arr_tag_id));
			}
			elseif($_return_type == "finaltagid_array" || $_return_type == "finaltagid_string" || $_return_type == "finaltagrelation_array")
			{
				if(sizeof($arr_1))
				{
					foreach($arr_1 as $tag) $arr_tag_id[$tag["MergedTagID"]] = $tag["TagID"];
					$arr_2 = $this->GetTagsByMergedTagID(array_values($arr_dest),"finaltagrelation_array");
					if(sizeof($arr_2))
					{
						foreach($arr_tag_id as $_id_from => $_id_to)
						{
							if(isset($arr_2[$_id_to])) $arr_tag_id[$_id_from] = $arr_2[$_id_to];
						}
					}
				}
				if($_return_type == "finaltagrelation_array") return $arr_tag_id;
				else if($_return_type == "finaltagid_array") return array_values($arr_tag_id);
				else return implode(",",array_values($arr_tag_id));
			}
			die("GetTagsByMergedTagID return_type($_return_type) is wrong");
		}
		
		function GetMergedTagID($tid)
		{
			$sql = "select TagID from tag_merged_info where MergedTagID = '$tid'  limit 1";
			$rtn = $this->objMysql->getFirstRowColumn($sql);
			if(is_numeric($rtn)) return $rtn;
			return 0;
		}
		
		function getTagIDByObjID($objType, $objID)
		{
			$objType = trim($objType) == 'mer' ? 'mer' : 'cat';
			$objID = intval($objID);
			
			$arrTagID = array();
			$sql = "Select TagID from tag_relationship where`Type`= '$objType' and `value` = $objID ";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrTagID[intval($arrTmp['TagID'])] = "";
			}
			$this->objMysql->freeResult($qry);
			return $arrTagID;
		}
		
		/* 
		* get all tagid except self from the same category
		*/
		function getTagIDByObjIDAndTag($tagID)
		{
			$tagID = intval($tagID);
			
			$arrTagID = array();
			$arrObjID = array();
			
			$sql = "SELECT `Value` as v FROM tag_relationship where `Type` = 'cat' and TagID = $tagID";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arrObjID[] = intval($arrTmp['v']);
			}
			$this->objMysql->freeResult($qry);
//			print_r($arrObjID);

			if(count($arrObjID))
			{
				$sql = "SELECT TagID FROM `tag_relationship` WHERE TagID <> '".$tagID."' AND `Type` = 'cat' AND `Value` IN (". implode(',', $arrObjID).")";
				$qry = $this->objMysql->query($sql);
				while($arrTmp = $this->objMysql->getRow($qryId))
				{
					$arrTagID[intval($arrTmp['TagID'])] = "";
				}
				$this->objMysql->freeResult($qry);
			}
//			print_r($arrTagID);
			return $arrTagID;
		}
/*		
		function getTagRelationshipByTagID($tagID) //int or array
		{
			$arrRtn = array();
			if(is_array($tagID) && count($tagID))
			{
				$sql = "select `TagID`, `Type`, `Value` from tag_relationship where TagID in (".implode(",", $tagID).")";
			}
			else
			{
				$tagID = intval($tagID);
				$sql = "select `TagID`, `Type`, `Value` from tag_relationship where TagID = $tagID";
			}
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$type = trim($arrTmp['Type']);
				$value = intval($arrTmp['Value']);
				$tid = intval($arrTmp['TagID']);
				if(!$type || !$value || !$tid) continue;
				$arrRtn[$tid][$type][] = $value;
			}
			$this->objMysql->freeResult($qry);
			return $arrRtn;
		}		
		
	*/	
		/*
		Sort by merchant name
		*/
		function getTagRelationshipByTagID($tagID) //int or array
		{
			$arrRtn = array();
			if(is_array($tagID) && count($tagID))
			{
				//$sql = "select `TagID`, `Type`, `Value` from tag_relationship left u where TagID in (".implode(",", $tagID).")";
				$sql = "select a.`TagID`, a.`Type`, a.`Value`
				 from tag_relationship a
				 left join  normalmerchant  b
				on  a.Value = b.ID 
				 where a.TagID in (".implode(",", $tagID).") order by b.Name";
			}
			else
			{
				$tagID = intval($tagID);
				$sql = "select a.`TagID`, a.`Type`, a.`Value`
				 from tag_relationship a
				 left join  normalmerchant  b
				on  a.Value = b.ID 
				where TagID = $tagID   order by b.Name";
			}
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$type = trim($arrTmp['Type']);
				$value = intval($arrTmp['Value']);
				$tid = intval($arrTmp['TagID']);
				if(!$type || !$value || !$tid) continue;
				$arrRtn[$tid][$type][] = $value;
			}
			$this->objMysql->freeResult($qry);
			return $arrRtn;
		}
		
		function getAllTypeValueFromRelationship($type)
		{
			$arrRtn = array();
			$type = trim($type) == 'mer' ? 'mer' : 'cat';
			$sql = "select distinct a.`Value` from tag_relationship a left join normalmerchant b  on a.Value = b.ID where  a.`Type` = '$type' order by b.Name";
			$qry = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$value = intval($arrTmp['Value']);
				if(!$value) continue;
				$arrRtn[] = $value;
			}
			$this->objMysql->freeResult($qry);
			return $arrRtn;
		}
		
		function getHotTag($cnt)
   		{
   			$arr = array();
   			$sql = "select ID as TagID, TagName, AllCouponCnt as cnt from tag order by AllCouponCnt desc limit $cnt";
   			$rows = $this->objMysql->getRows($sql);
   			foreach($rows as $arrTmp)
   			{
   				$arr[] = array(
					'id' => intval($arrTmp['TagID']),
	   				'name' => trim($arrTmp['TagName']),
   					'couponcnt' => intval($arrTmp['cnt']),
				);
   			}
   			return $arr;
   		}

		function getCouponIDByTagID($tid, $limitStr='', $arrExcludeCouponID="")
		{
			$arr = array();
			if(!$tid) return $arr;
			$orderStr = " ORDER BY CouponID Desc ";
			$strExcludedCoupon = "";
			if(is_array($arrExcludeCouponID) && count($arrExcludeCouponID))
			{
				$strExcludedCoupon = "AND CouponID NOT in (" . implode(",", $arrExcludeCouponID) . ")";
			}
			$groupby = is_numeric($tid) ? "" : "group by CouponID";
			$sql = "select SQL_CALC_FOUND_ROWS CouponID from r_coupontag where TagID in ($tid) $strExcludedCoupon $groupby $orderStr $limitStr";
			
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arr[] = $arrTmp['CouponID'];
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		function getValidCouponIDByTagIDAndCouponID($tid, $arrCouponID)
		{
			$arr = array();
			$sql = "select CouponID from r_coupontag where TagID = ".intval($tid)."  AND CouponID in (".implode(",", $arrCouponID).") ";
			
			$qryId = $this->objMysql->query($sql);
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arr[] = intval($arrTmp['CouponID']);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}

		function getCouponIDByTagIDStr($tagStr, $limitStr='')
		{
			return $this->getCouponIDByTagID($tagStr, $limitStr);
		}

		function getCouponCntByTagID($tid, $arrExcludeCouponID="")
		{
			$tid = intval($tid);
			$sql = "select count(*) as cnt from r_coupontag where TagID = ".intval($tid);
			if(is_array($arrExcludeCouponID) && count($arrExcludeCouponID))
			{
				$sql .= " AND CouponID NOT in (".implode(",", $arrExcludeCouponID).")";
			}
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$cnt = intval($arrTmp['cnt']);
			$this->objMysql->freeResult($qryId);
			return $cnt;
		}
		
		function getTagNameByID($tid)
		{
			$arrTmp = $this->getTagListByLimitStr("", "ID = ".intval($tid));
			if(isset($arrTmp[0]['name'])) return $arrTmp[0]['name'];
			return '';
		}
		
		function getTagSEOInfoByID($tid)
		{
			$res = array();
			$tid = intval($tid);

			$arrTmp = $this->getTagAllInfoByTagID($tid);
			if(count($arrTmp))
			{
				$blogname = trim($arrTmp['blog']);
				$res['sd'] = trim($arrTmp['shortdesc']);
				$res['ld'] = trim($arrTmp['longdesc']);
				$res['img'] = trim($arrTmp['img']);
				$res['metaTitle'] = trim($arrTmp['metaTitle']);
				$res['metakw'] = trim($arrTmp['metakw']);
				$res['metadesc'] = trim($arrTmp['metadesc']);
				$res['seotitle'] = trim($arrTmp['seotitle']);
				$res['alias'] = trim($arrTmp['alias']);
				$res['bname'] = "";
				$res['bcontent'] = "";
		
				if($blogname)
				{
					$sql = "select post_title, post_content from blog_posts where post_name = '".addslashes($blogname)."'";
					$qryId = $this->objMysql->query($sql);
					$arrTmp = $this->objMysql->getRow($qryId);
					if(count($arrTmp))
					{
						$res['bname'] = trim($arrTmp['post_title']);
						$res['bcontent'] = trim($arrTmp['post_content']);
					}
					$this->objMysql->freeResult($qryId);
				}
			}
			return $res;
		}
	
		function TagOperationLog($msg)
		{
			global $PHP_AUTH_USER;
			$editorname = $PHP_AUTH_USER ? $PHP_AUTH_USER : $_SERVER["REMOTE_USER"];
			$log = $editorname."\t".date("Y-m-d H:i:s")."\t".$msg."\n";
			error_log($log, 3, LOG_LOCATION."tagOperation.log");
		}
		
		function getTagInfoByTagNames($_tags,$_allowId=false)
		{
			$arr_return = array(
				"existing" => array(),
				"new" => array(),
				"wrong" => array(),
			);
			if(empty($_tags)) return $arr_return;

			$arr_tag_id = array();
			$arr_tag_name = array();
			$arr_tag_name_sql = array();

			$arr_tmp = explode(",",$_tags);
			foreach($arr_tmp as $k => $v)
			{
				$v = trim($v);
				if($v == "") continue;
				
				if($_allowId && is_numeric($v)) $arr_tag_id[$v] = $v;
				else
				{
					$arr_tag_name[strtolower($v)] = array("id" => 0,"name" => $v);
					$arr_tag_name_sql[] = "'" . mysql_real_escape_string($v) . "'";
				}
			}
			
			//checking id
			if(sizeof($arr_tag_id) > 0)
			{
				$str_cond = "`ID` in (" . implode(",",$arr_tag_id) . ")";
				$arr_tag = $this->getTagListByLimitStr("",$str_cond,"");
				foreach($arr_tag as $k => $v)
				{
					$arr_return["existing"][$v["id"]] = $v["name"];
				}

				foreach($arr_tag_id as $id)
				{
					if(!isset($arr_return["existing"][$id]))
						$arr_return["wrong"][] = $id;
				}
			}
			
			//checking name
			if(sizeof($arr_tag_name) > 0)
			{
				$str_cond = "`TagName` in (" . implode(",",$arr_tag_name_sql) . ")";
				$arr_tag = $this->getTagListByLimitStr("",$str_cond,"");
				foreach($arr_tag as $k => $v)
				{
					$tag_name = strtolower($v["name"]);
					if(isset($arr_tag_name[$tag_name])) $arr_tag_name[$tag_name]["id"] = $v["id"];
				}
				
				foreach($arr_tag_name as $k => $v)
				{
					if($v["id"] == 0) $arr_return["new"][] = $v["name"];
					else $arr_return["existing"][$v["id"]] = $v["name"];
				}
			}

			return $arr_return;
		}//end fun
				
		function addOrUpdateRelatedTags($_tagid,$_tags)
		{
			$checkResult = $this->getTagInfoByTagNames($_tags);
			if(sizeof($checkResult["new"]) > 0)
			{
				$this->quickAddTags($checkResult["new"]);
				$checkResult = $this->getTagInfoByTagNames($_tags);
			}
			
			$arr_field = array();
			$arr_field['TagID'] = $_tagid;
			$arr_field['TagName'] = "''";
			$arr_field['RelatedTagIDs'] = "'" . implode(",",array_keys($checkResult["existing"])) . "'";
			$arr_field['RelatedTagNames'] = "'" . mysql_real_escape_string(implode(",",$checkResult["existing"])) . "'";
			$arr_field['AddTime'] = "now()";
			$arr_field['LastChangeTime'] = "now()";
			
			$existing = $this->getRelatedTags($_tagid,"all");
			if(sizeof($existing) > 0)
			{
				
				if(sizeof($checkResult["existing"]) == 0)
				{
					//delete
					$sql = "delete from tag_related_tag where TagID = '$_tagid'";
				}
				else
				{
					//update
					unset($arr_field['TagID']);
					unset($arr_field['AddTime']);
					$arr_update = array();
					foreach($arr_field as $k => $v) $arr_update[] = "$k = $v";
					$sql = "update tag_related_tag set " . implode(",",$arr_update) . " where TagID = '$_tagid'";
				}
			}
			else
			{
				//insert
				if(sizeof($checkResult["existing"]) == 0) return 0;
				$sql = "INSERT INTO tag_related_tag (".implode(",",array_keys($arr_field)).") VALUES (" . implode(",",$arr_field) . ")";
			}

			$qryId = $this->objMysql->query($sql);
			return sizeof($checkResult["existing"]);
		}//end fun
		
		function getTagPendingListByLimitStr($limitStr="", $whereStr="", $orderStr=" TagName ASC ", $onlyRtnId=false)
		{
			$arr = array();
			$whereStr = $whereStr ? " WHERE $whereStr " : "";
			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
			$sql = "select ID, TagName, Status, EditorAdd, AddTime, EditorApprove, ApproveTime, AddSource ";
			$sql .= " from tag_pending $whereStr $orderStr $limitStr";
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				if($onlyRtnId)
				{
					$arr[$i] = intval($arrTmp['ID']);
				}
				else
				{
					$arr[$i]['id'] = intval($arrTmp['ID']);
					$arr[$i]['name'] = trim($arrTmp['TagName']);
					$arr[$i]['status'] = trim($arrTmp['Status']);
					$arr[$i]['editoradd'] = trim($arrTmp['EditorAdd']);
					$arr[$i]['addtime'] = trim($arrTmp['AddTime']);
					$arr[$i]['editorapprove'] = trim($arrTmp['EditorApprove']);
					$arr[$i]['approvetime'] = trim($arrTmp['ApproveTime']);
					$arr[$i]['addsource'] = trim($arrTmp['AddSource']);
				}
				$i++;

				unset($arrTmp);
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function getTagPendingInfoByTagNames($_tags,$_allowId=false)
		{
			$arr_return = array(
				"existing" => array(),
				"new" => array(),
				"wrong" => array(),
			);
			if(empty($_tags)) return $arr_return;

			$arr_tag_id = array();
			$arr_tag_name = array();
			$arr_tag_name_sql = array();

			$arr_tmp = explode(",",$_tags);
			foreach($arr_tmp as $k => $v)
			{
				$v = trim($v);
				if($v == "") continue;
				
				if($_allowId && is_numeric($v)) $arr_tag_id[$v] = $v;
				else
				{
					$arr_tag_name[strtolower($v)] = array("id" => 0,"name" => $v);
					$arr_tag_name_sql[] = "'" . mysql_real_escape_string($v) . "'";
				}
			}
			
			//checking id
			if(sizeof($arr_tag_id) > 0)
			{
				$str_cond = "`ID` in (" . implode(",",$arr_tag_id) . ")";
				$arr_tag = $this->getTagPendingListByLimitStr("",$str_cond,"");
				foreach($arr_tag as $k => $v)
				{
					$arr_return["existing"][$v["id"]] = $v["name"];
				}

				foreach($arr_tag_id as $id)
				{
					if(!isset($arr_return["existing"][$id]))
						$arr_return["wrong"][] = $id;
				}
			}
			
			//checking name
			if(sizeof($arr_tag_name) > 0)
			{
				$str_cond = "`TagName` in (" . implode(",",$arr_tag_name_sql) . ")";
				$arr_tag = $this->getTagPendingListByLimitStr("",$str_cond,"");
				foreach($arr_tag as $k => $v)
				{
					$tag_name = strtolower($v["name"]);
					if(isset($arr_tag_name[$tag_name])) $arr_tag_name[$tag_name]["id"] = $v["id"];
				}
				
				foreach($arr_tag_name as $k => $v)
				{
					if($v["id"] == 0) $arr_return["new"][] = $v["name"];
					else $arr_return["existing"][$v["id"]] = $v["name"];
				}
			}

			return $arr_return;
		}//end fun
		
		function addOrUpdateRelatedTagsPending($_tagid,$_tags)
		{
			$checkResult = $this->getTagInfoByTagNames($_tags);
			if(sizeof($checkResult["new"]) > 0)
			{
				$this->quickAddTags($checkResult["new"]);
				$checkResult = $this->getTagInfoByTagNames($_tags);
			}
			
			$arr_field = array();
			$arr_field['TagID'] = $_tagid;
			$arr_field['TagName'] = "''";
			$arr_field['RelatedTagIDs'] = "'" . implode(",",array_keys($checkResult["existing"])) . "'";
			$arr_field['RelatedTagNames'] = "'" . mysql_real_escape_string(implode(",",$checkResult["existing"])) . "'";
			$arr_field['AddTime'] = "now()";
			$arr_field['LastChangeTime'] = "now()";
			
			$existing = $this->getRelatedTags($_tagid,"all");
			if(sizeof($existing) > 0)
			{
				
				if(sizeof($checkResult["existing"]) == 0)
				{
					//delete
					$sql = "delete from tag_related_tag where TagID = '$_tagid'";
				}
				else
				{
					//update
					unset($arr_field['TagID']);
					unset($arr_field['AddTime']);
					$arr_update = array();
					foreach($arr_field as $k => $v) $arr_update[] = "$k = $v";
					$sql = "update tag_related_tag set " . implode(",",$arr_update) . " where TagID = '$_tagid'";
				}
			}
			else
			{
				//insert
				if(sizeof($checkResult["existing"]) == 0) return 0;
				$sql = "INSERT INTO tag_related_tag (".implode(",",array_keys($arr_field)).") VALUES (" . implode(",",$arr_field) . ")";
			}

			$qryId = $this->objMysql->query($sql);
			return sizeof($checkResult["existing"]);
		}//end fun

		function quickAddTags($_arr_tagnames,$addsource="")
		{
			$arr_return = array();
			foreach($_arr_tagnames as $newtagname)
			{
				//$this->AddTagWithAllInfo($newtagname,'0',"","","","","","","","","","","");
				$tagId = $this->addTag($newtagname,$addsource);
				$arr_return[$tagId] = $newtagname;
			}
			return $arr_return;
		}//end fun
		
		function getRelatedTags($_from,$_return_type="first",$_filter="")
		{
			if(is_array($_from)) $arr_in = $_from;
			else $arr_in = explode(",",$_from);
			
			if(is_array($_filter)) $arr_filter = $_filter;
			else $arr_filter = explode(",",$_filter);

			$arr_return = array();
			$all_id = array();
			if(substr($_return_type,-10) == "withsource")
			{
				foreach($arr_in as $id) $all_id[$id] = $id;
			}
			
			$sql = "select * from tag_related_tag where TagID in (" . implode(",",$arr_in) . ")";
			$qryId = $this->objMysql->query($sql);
			while($row = $this->objMysql->getRow($qryId))
			{
				$TagID = $row["TagID"];
				if($_return_type == "first") return $row;
				elseif(substr($_return_type,0,2) == "id")
				{
					$RelatedTagIDs = $row["RelatedTagIDs"];
					$arr_temp = explode(",",$RelatedTagIDs);
					foreach($arr_temp as $id)
					{
						if($id == "") continue;
						if(!is_numeric($id)) continue;
						if(!empty($arr_filter) && in_array($id,$arr_filter)) continue;
						if(in_array($id,$arr_in)) continue;
						$all_id[$id] = $id;
					}
				}
				else
				{
					$arr_return[$TagID] = $row;
				}
			}
			$this->objMysql->freeResult($qryId);
			if(substr($_return_type,0,8) == "idstring") return implode(",",$all_id);
			elseif(substr($_return_type,0,7) == "idarray") return $all_id;
			else return $arr_return;
		}//end fun
		/*
   		add start tom,2010-10-19
   		unassigncat
   		*/
		function getTagMerchantList($tagId, $limit=""){
   			$sql = "select b.ID, b.Name from tag_relationship a, normalmerchant b
					where a.Type = 'mer'
					and a.Value = b.ID
					and a.TagId = '$tagId' order by b.Name $limit";
			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			$arr = array();
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   			
   		}		
   		

   		function getTagCategoryList($tagId, $limit=""){

   			$sql = "select b.ID, b.Name from tag_relationship a, normalcategory b
					where a.Type = 'cat'
					and a.Value = b.ID
					and a.TagId = '$tagId' order by b.Name $limit";
			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			$arr = array();
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['name'] = trim($arrTmp['Name']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   			
   		}

		function removeTagRelationShipByType($value, $type, $arrTag)
		{
			if(!$value || !$type || !is_array($arrTag) || !count($arrTag))
				return false;
			$insertSql = "delete from tag_relationship where TagId in(". implode(",", $arrTag) .") and Value = '". $value ."' and Type='". $type ."'";
			$this->objMysql->query($insertSql);

			//refresh lastchagngetime for tag table
			$sql = "update tag set LastChangetime = Now() where ID in (".implode(",", $arrTag).") ";
			$this->objMysql->query($sql);
		}
		
   		//add end tom,2010-10-19
   		
   		
   		
		
	}//end class
	
	
	
}
?>
