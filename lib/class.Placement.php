<?
if (!defined("class.Placement.php"))
{
	define("class.Placement.php",1);
	
	class Placement
	{
		public $objMysql;
		public $arrPlacementConstFile = array();
		
		public function __construct($objMysql)
		{
			$this->objMysql = $objMysql;
			$this->arrPlacementConstFile = array(
				'homepage_top_hotsearch' => SITE_ROOT . "const/const_homepage_top_hotsearch.php",
				'homepage_middle_featuredmerchant' => SITE_ROOT . "const/const_homepage_middle_featuredmerchant.php",
				'homepage_leftsidebar_merchantstores' => SITE_ROOT . "const/const_homepage_leftsidebar_merchantstores.php",
				'homepage_middle_toptags' => SITE_ROOT . "const/const_homepage_middle_toptags.php",
				'insidepages_leftsidebar_couponbystores' => SITE_ROOT . "const/const_insidepages_leftsidebar_couponbystores.php",
				'homepage_middle_popularcoupons' => SITE_ROOT . "const/const_homepage_middle_popularcoupons.php",
				'categorypage_top_merchantlogo' => SITE_ROOT . "const/const_categorypage_top_merchantlogo.php",
				'categorypage_featuredcoupon' => SITE_ROOT . "const/const_categorypage_featuredcoupon.php",
				'tagpage_top_merchantlogo' => SITE_ROOT . "const/const_tagpage_top_merchantlogo.php",
				'tagpage_featuredcoupon' => SITE_ROOT . "const/const_tagpage_featuredcoupon.php",
				'bdpage_top_merchantlogo' => SITE_ROOT . "const/const_bdpage_top_merchantlogo.php",
				'bdpage_featuredcoupon' => SITE_ROOT . "const/const_bdpage_featuredcoupon.php",
				
				'topbarlink' => SITE_ROOT . "const/const_topbarlink.php",
				'footerlink_line1' => SITE_ROOT . "const/const_footerlink_line1.php",
				'footerlink_line2' => SITE_ROOT . "const/const_footerlink_line2.php",
				'footerlink_line3' => SITE_ROOT . "const/const_footerlink_line3.php",
				
//				'merpage_topcoupon' => "noconstfile",
//				'merpage_featuredcoupon' => "noconstfile",
			);
		}

		function getPlacementConstFilePath($_placement)
		{
			if(!isset($this->arrPlacementConstFile[$_placement]))
			{
				die("undefined placement: $_placement\n");
			}
			return $this->arrPlacementConstFile[$_placement];
		}

		function isEmptyData(&$arr,$n=2)
		{
			if($n < 1) $n = 1;
			if($n > 5) $n = 5;

			$arr['Item1'] = trim($arr['Item1']);
			$arr['Item2'] = trim($arr['Item2']);
			$arr['Item3'] = trim($arr['Item3']);
			$arr['Item4'] = trim($arr['Item4']);
			$arr['Item5'] = trim($arr['Item5']);

			for($i=1;$i<=$n;$i++)
			{
				if($arr['Item' . $i] == '')
				{
					echo "Empty Data!\n";
					return true;
				}
			}
			return false;
		}
		
		function generateConstFileContent($tp,$varname1,$varname2="")
		{
			$content = '';
			switch($tp)
			{
				case "oneitem":
					$arrId = array();
					while($arr = mysql_fetch_array($this->queryid))
					{
						if($this->isEmptyData($arr,1)) continue;
						$arrId[] = $arr['Item1'];
					}
					$content .= '$' . $varname1 . ' = array(';
					$content .= implode(",",$arrId);
					$content .= ");\n";
					break;
				case "twoitem":
					$arrLine = array();
					while($arr = mysql_fetch_array($this->queryid))
					{
						if($this->isEmptyData($arr,2)) continue;
						if(!isset($arrLine[$arr['Item1']])) $arrLine[$arr['Item1']] = array();
						$arrLine[$arr['Item1']][] = $arr['Item2'];
					}
					
					$content .= '$' . $varname1 . " = array(\n";
					
					foreach($arrLine as $k => $v)
					{
						if(is_numeric($k))
						{
							$content .=  "$k => array(";
						}
						else
						{
							$content .=  "'" . addslashes($k) . "'" . " => array(";
						}
						
						$content .= implode(",",$v);
						$content .= "),\n";
					}
					$content .= ");\n";
					break;
				default:
					die("");
			}
			return $content;
		}
		
		function SavePlacementToConstFile($Placement)
		{
			$strFileName = $this->getPlacementConstFilePath($Placement);
			if(stripos($strFileName,SITE_ROOT) === false) return;
			$sql = "SELECT * FROM placement WHERE PlacementName = '".addslashes($Placement)."' AND (ExpireDate = '0000-00-00' OR DATE_ADD(ExpireDate,INTERVAL 1 DAY) > NOW()) ORDER BY `Order` ASC";
			$this->queryid = $qry = $this->objMysql->query($sql);
			$strFileContent = "<?php \n";
			switch ($Placement)
			{
				case 'homepage_top_hotsearch':
					while($arr = mysql_fetch_array($qry))
					{
						if($this->isEmptyData($arr)) continue;
						$strFileContent .= '$g_arrSpecialKW[\''.addslashes($arr['Item1']).'\'] = LINK_ROOT."'.$arr['Item2'].'";' ."\n";
					}
					break;
				case 'homepage_middle_featuredmerchant':
					while($arr = mysql_fetch_array($qry))
					{
						if($this->isEmptyData($arr)) continue;
						$strFileContent .= '$g_arrFeatureMer[\''.$arr['Item1'].'\'] = '.$arr['Item2'].';' ."\n";
		
						$arrFeaturedCoupon = array();
						if ($arr['Item3'] != '') $arrFeaturedCoupon[] = $arr['Item3'];
						if ($arr['Item4'] != '') $arrFeaturedCoupon[] = $arr['Item4'];
						if ($arr['Item5'] != '') $arrFeaturedCoupon[] = $arr['Item5'];
						
						if(sizeof($arrFeaturedCoupon) > 0)
						{
							$strFileContent .= '$g_arrFeatureMer_Coupon[\''.$arr['Item1'].'\'] = array(' . implode(",",$arrFeaturedCoupon) . ');' ."\n";
						}
					}
					break;
				case 'homepage_leftsidebar_merchantstores':
					$strFileContent .= $this->generateConstFileContent("oneitem","g_sidebar_arrFeatureMer");
					break;
				case 'homepage_middle_toptags':
					$strFileContent .= $this->generateConstFileContent("oneitem","g_arrFeatureTag");
					break;
				case 'insidepages_leftsidebar_couponbystores':
					$strFileContent .= $this->generateConstFileContent("oneitem","g_mainpage_sidebar_arrFeatureMer");
					break;
				case 'homepage_middle_popularcoupons':
					$strFileContent .= $this->generateConstFileContent("oneitem","g_specialHottestcoupon");
					break;
				case 'categorypage_top_merchantlogo':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredMerLogoInCatPage");
					break;
				case 'categorypage_featuredcoupon':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredCouponInCatePage");
					break;
				case 'tagpage_top_merchantlogo':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredMerLogoInTagPage");
					break;
				case 'tagpage_featuredcoupon':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredCouponInTagPage");
					break;
				case 'bdpage_top_merchantlogo':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredMerLogoInBDPage");
					break;
				case 'bdpage_featuredcoupon':
					$strFileContent .= $this->generateConstFileContent("twoitem","g_featuredCouponInBDPage");
					break;
				// header and footer tags link
				case 'topbarlink':
				case 'footerlink_line1':
				case 'footerlink_line2':
				case 'footerlink_line3':
					$i=0;
					while($arr = mysql_fetch_array($qry))
					{
						if($this->isEmptyData($arr)) continue;
						if(strpos($arr['Item3'],'http://') === false && strpos($arr['Item3'],'javascript') === false) $arr['Item3'] = LINK_ROOT.$arr['Item3'];
						$strFileContent .= '$g_'.$Placement.'['.$i.'] = array(\''.addslashes($arr['Item1']).'\', \''.addslashes($arr['Item2']).'\', \''.addslashes($arr['Item3']).'\', \''.addslashes($arr['Item4']).'\');'."\n";
						$i++;
					}
					break;					
			}
			mysql_free_result($qry);
			$strFileContent .= "?>";
			
			//change by jimmy @ 2010-05-20
			$tmpFile = dirname($strFileName)."/tmp_".md5(uniqid(rand().time(), true));
			if(file_exists($tmpFile)) unlink($tmpFile);
			if(file_put_contents($tmpFile, $strFileContent) === false)
			{
				alert("No permission to write $tmpFile!");
				@unlink($tmpFile);
				exit;
			}
			//syntax check
			exec("php -l ".escapeshellarg($tmpFile), $arrOutput);
			if(stripos($arrOutput[0], "No syntax errors detected") === 0) //no syntax error
			{
				rename($tmpFile, $strFileName);
				alert("Generate ".basename($strFileName)." successfully!");
			}
			else
			{
				alert("Very bad, syntax error detected, generate ".basename($strFileName)." failed, keep the old one!");
				unlink($tmpFile);
				echo "<br><br>";
				echo nl2br(htmlspecialchars($strFileContent));
				exit;
			}
		}//end function 

		function ResetAllPlacementConstFile(){
			$sql = " DELETE FROM placement WHERE ExpireDate <> '0000-00-00' AND ExpireDate < CURDATE() ";
			$this->objMysql->query($sql);
		
			foreach($this->arrPlacementConstFile as $placement => $file)
			{
				$this->SavePlacementToConstFile($placement);
			}
		}
		
		function ResetAllPlacementDate(){
			$sql = " DELETE FROM placement WHERE ExpireDate <> '0000-00-00' AND ExpireDate < CURDATE() ";
			$this->objMysql->query($sql);
		}
		
		
		function getPlacementByPlacementNameANDItem1($strPlacementName,$item1){
			$return_str="";
			$sql = "SELECT * FROM placement WHERE PlacementName = '$strPlacementName' AND Item1 = '$item1' ORDER BY `Order` ASC ";
			$qry = $this->objMysql->query($sql);
			while ($arrInfo = mysql_fetch_array($qry)){
				if($strPlacementName=="categorypage_top_merchantlogo"){						
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">"
									."<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>"
									."<td width='60%'><input type=text class='isNum' name='MerchantID_".$arrInfo['ID']."' id='MerchantID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>"
									."<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>"
										."<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>"
									."</td>"
									."<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&CategoryID=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>"
									."</tr>";
				}elseif($strPlacementName=="categorypage_featuredcoupon"){
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">
									<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>
									<td width='60%'><input type=text class='isNum' name='CouponID_".$arrInfo['ID']."' id='CouponID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>
									<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>
										<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>
									</td>
									<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&CategoryID=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>
									</tr>";					
				}elseif($strPlacementName=="tagpage_top_merchantlogo"){
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">
									<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>
									<td width='60%'><input type=text class='isNum' name='MerchantID_".$arrInfo['ID']."' id='MerchantID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>
									<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>
										<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>
									</td>
									<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&TagID=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>
									</tr>";					
				}elseif($strPlacementName=="tagpage_featuredcoupon"){
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">
									<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>
									<td width='60%'><input type=text class='isNum' name='CouponID_".$arrInfo['ID']."' id='CouponID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>
									<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>
										<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>
									</td>
									<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&TagID=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>
									</tr>";					
				}elseif($strPlacementName=="bdpage_top_merchantlogo"){
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">
									<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>
									<td width='60%'><input type=text class='isNum' name='CouponID_".$arrInfo['ID']."' id='CouponID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>
									<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>
										<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>
									</td>
									<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&keywordid=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>
									</tr>";					
				}elseif($strPlacementName=="bdpage_featuredcoupon"){
					$return_str.="<tr bgcolor='#FFFFFF' onMouseOver=\"this.style.backgroundColor='#FBF0E3';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\">
									<td width='10%'><input type=text class='isNum' name='Order_".$arrInfo['ID']."' id='Order_".$arrInfo['ID']."' size=10 value='".$arrInfo['Order']."'></td>
									<td width='60%'><input type=text class='isNum' name='CouponID_".$arrInfo['ID']."' id='CouponID_".$arrInfo['ID']."' size=70 value='".$arrInfo['Item2']."'></td>
									<td width='20%'><input type='text' name='ExpireDate_".$arrInfo['ID']."' id='ExpireDate_".$arrInfo['ID']."' value='".$arrInfo['ExpireDate']."' size=10>
										<input NAME='btexpireDate_".$arrInfo['ID']."' type='button' ID='btexpireDate_".$arrInfo['ID']."' onclick=\"calendar(document.getElementById('ExpireDate_".$arrInfo['ID']."'), document.getElementById('ExpireDate_".$arrInfo['ID']."'))\" value='calendar'>
									</td>
									<td width='7%'><a href='#' onClick=\"DeleteConfirm('placement_ctrl.php?action=dodel".$strPlacementName."&keywordid=".$item1."&ID=".$arrInfo['ID']."', true)\">Delete</a></td>
									</tr>";					
				}
		
			}
			
			return $return_str;
		}			
		
		function getPlacementByPlacementName($strPlacementName){
			$arr = array();
			$return_str="";
			$sql="SELECT * FROM placement WHERE PlacementName = '$strPlacementName' ORDER BY `Order`";
			$qryId = $this->objMysql->query($sql);
			$qryId = $this->objMysql->query($sql);
			$i = 0;
			while($arrTmp = $this->objMysql->getRow($qryId))
			{
				$arr[$i]['id'] = intval($arrTmp['ID']);
				$arr[$i]['item1'] = trim($arrTmp['Item1']);
				$arr[$i]['item2'] = trim($arrTmp['Item2']);
				$arr[$i]['item3'] = trim($arrTmp['Item3']);
				$arr[$i]['item4'] = trim($arrTmp['Item4']);
				$arr[$i]['item5'] = trim($arrTmp['Item5']);
				$arr[$i]['order'] = trim($arrTmp['Order']);
				$arr[$i]['expireDate'] = trim($arrTmp['ExpireDate']);
				$arr[$i]['addTime'] = trim($arrTmp['AddTime']);
				$arr[$i]['addEditor'] = trim($arrTmp['AddEditor']);
				$arr[$i]['approvalEditor'] = trim($arrTmp['ApprovalEditor']);
				$arr[$i]['lastUpdateTime'] = trim($arrTmp['LastUpdateTime']);
				$i++;
			}
			$this->objMysql->freeResult($qryId);
			return $arr;
		}
		
		function addIndexPlacement($PlacementName,$Item1,$Item2,$Item3,$Item4,$AddEditor,$Order){			
			$sql="INSERT INTO placement (PlacementName,Item1,Item2,Item3,Item4,AddEditor,`Order`,AddTime,LastUpdateTime)VALUES('$PlacementName','$Item1','$Item2','$Item3','$Item4','$AddEditor','$Order','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')";
			$this->objMysql->query($sql);
		}
		
		function updateIndexPlacement($ID,$Item1,$Item2,$Item3,$Item4,$AddEditor,$Order){
			$sql="UPDATE placement SET Item1='$Item1',Item2='$Item2',Item3='$Item3',Item4='$Item4',AddEditor='$AddEditor',`Order`='$Order',LastUpdateTime='".date('Y-m-d H:i:s')."' WHERE ID=$ID";
			$this->objMysql->query($sql);		
		}
	}
}
?>