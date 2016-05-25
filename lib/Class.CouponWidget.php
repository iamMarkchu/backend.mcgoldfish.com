<?
if (!defined("__CLASS_COUPONWIDGET__"))
{
	define("__CLASS_COUPONWIDGET__", 1);

	class CouponWidget extends ExtMerchant
	{
		protected $_name="couponwidget";

		public function __contruct($objMysql){
			parent::ExtMerchant($objMysql);
		}

		public function checkWidgetRows($where){
			return parent::checkTableRows($where);
		}

		public function updateWidgetInfos($infos="",$where){
			return parent::updateTableInfos($infos,$where);
		}

		public function setWidgetInfos($infos=""){
			return parent::setTableInfos($infos);
		}

		public function getWidgetInfos($select="",$where="",$groupby="",$orderby="",$limit=""){
			return parent::getTableInfos($select,$where,$groupby,$orderby,$limit);
		}

		public function getWidgetResult($id,$CustomerKey,$coupontypeid,$widgetsizeid,$fixid,$couponNum){
			global $objMysql;
			$objTpl = new rFastTemplate(INCLUDE_ROOT."tpl/");
			$objTpl->define(array( 'couponitem' => 'couponwidget_result.tpl'));

			$sizelist =
			array(1=>"widgetspecification1",2=>"widgetspecification2",3=>"widgetspecification3",4=>"widgetspecification4");

			$size = (int)$widgetsizeid;
			$couponType = (int)$coupontypeid;
			$fixID = (int)$fixid;
			$couponNum = (int)$couponNum;

			//echo $size.'-'.$couponType.'-'.$fixID.'-'.$couponNum;
			$sql = "";
			$url = "";

			switch($couponType){
				case 1:
					$sql ="SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName,normalcategory.Name AS ctName ";
					$sql .= "FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) LEFT JOIN normalcategory ON(normalcoupon.CategoryID = normalcategory.ID)";
					$sql .= " WHERE normalcoupon.CategoryID='".$fixID."'";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.AddTime DESC LIMIT 0, ".$couponNum;
					break;
				case 2:
					$sql ="SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName,normalcategory.Name AS ctName  ";
					$sql .= "FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) LEFT JOIN normalcategory ON(normalcoupon.CategoryID = normalcategory.ID)";
					$sql .= " WHERE normalcoupon.CategoryID='".$fixID."'";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND  normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.ClickCnt DESC LIMIT 0, ".$couponNum;
					break;
				case 3:
					$sql ="SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= "FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE normalcoupon.MerchantID='".$fixID."'";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.AddTime DESC LIMIT 0, ".$couponNum;
					break;
				case 4:
					$sql ="SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= "FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE normalcoupon.MerchantID='".$fixID."'";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.ClickCnt DESC LIMIT 0, ".$couponNum;
					break;
				case 5:
					$sql = "SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= " FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE normalcoupon.Tag LIKE '".$fixID.",%' OR normalcoupon.Tag = '".$fixID."' OR normalcoupon.Tag LIKE '%,".$fixID.",%' OR normalcoupon.Tag LIKE '%,".$fixID."' ";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.AddTime DESC LIMIT 0, ".$couponNum;
					break;
				case 6:
					$sql = "SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= " FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE normalcoupon.Tag LIKE '".$fixID.",%' OR normalcoupon.Tag = '".$fixID."' OR normalcoupon.Tag LIKE '%,".$fixID.",%' OR normalcoupon.Tag LIKE '%,".$fixID."' ";
					$sql .= " AND (normalcoupon.ExpireTime = '0000-00-00 00:00:00' OR normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.ClickCnt DESC LIMIT 0, ".$couponNum;
					break;
				case 7:
					$sql = "SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= " FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE (normalcoupon.ExpireTime = '0000-00-00 00:00:00' or normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.AddTime DESC LIMIT 0, ".$couponNum;
					break;
				case 8:
					$sql ="SELECT normalcoupon.ID AS ID, normalcoupon.Title AS Title,normalcoupon.ImgUrl AS ImgUrl,normalcoupon.Code AS Code,normalmerchant.Name AS mcName ";
					$sql .= " FROM normalcoupon LEFT JOIN normalmerchant ON(normalcoupon.MerchantID=normalmerchant.ID) ";
					$sql .= " WHERE (normalcoupon.ExpireTime = '0000-00-00 00:00:00' or normalcoupon.ExpireTime >= '".date('Y-m-d')."') ";
					$sql .= " AND normalcoupon.StartTime <= NOW() AND normalcoupon.IsActive = 'YES'";
					$sql .= " ORDER BY normalcoupon.ClickCnt DESC LIMIT 0, ".$couponNum;
					break;
				default:
					break;
			}
			if( !empty($sql) ){
				$qryId = $objMysql->query($sql);
				$info_html = "";

				$isempty = 1;
				while($arrTmp = $objMysql->getRow($qryId)){
					switch($couponType){
						case 1:
						case 2:
							$url = get_rewrited_url("category",$arrTmp["ctName"],$fixID);
							break;
						case 3:
						case 4:
							$url = get_rewrited_url("merchant",$arrTmp["mcName"],$fixID);
							break;
						case 5:
						case 6:
							$objTag = new Tag($objMysql);
							$tagName = $objTag->getTagNameByID($fixID);
							$url = get_rewrited_url("tag",$tagName,$fixID);
							break;
						case 7:
							$url = get_rewrited_url("newcoupon");
							break;
						case 8:
							$url = get_rewrited_url("hotcoupon");
							break;
					}
					$url .= "&mktsrc=cw_".$CustomerKey."#".$arrTmp["ID"]."";

					$info_html .= '<li class="section"> ';

					switch($size){
						case 1:
							$info_html .= '<table width="158" border="0" cellspacing="0" cellpadding="0"><tr>';
							$info_html .= '<td width="17" valign="top">'.($arrTmp["Code"]!=''?'<img src="'.LINK_ROOT.'/image/coupongreen.gif" alt="coupon"  class="widget_img"/>':'<img src="'.LINK_ROOT.'/image/dealblue.gif" alt="coupon"  class="widget_img"/>').'</td>';
							$info_html .= '<td width="141"><a href="'.$url.'" class="widget_blue">'.$arrTmp["Title"].'</a><span class="widgetmerchant"> - '.$arrTmp["mcName"].'</span></td>';
							$info_html .= '</tr></table>';
							break;
						case 2:
						case 4:
							$ispic = 0;
							if( !empty($arrTmp["ImgUrl"]) )
							{
								$imgfile = substr(INCLUDE_ROOT,0,-1).$arrTmp["ImgUrl"];
								if(file_exists($imgfile))
								{
									list($width, $height, $type, $attr) = @getimagesize($imgfile);
									if($width && $height)
									{
										$height = floor(60 / $width * $height);
										$ispic = 1;
									}
								}
								
							}
							$info_html .= '<table width="108" border="0" cellspacing="0" cellpadding="0"><tr>';
							$info_html .= '<td width="17" valign="top">'.($arrTmp["Code"]!=''?'<img src="'.LINK_ROOT.'/image/coupongreen.gif" alt="coupon"  class="widget_img"/>':'<img src="'.LINK_ROOT.'/image/dealblue.gif" alt="coupon"  class="widget_img"/>').'</td>';
							$info_html .= '<td width="91"><a href="'.$url.'" class="widget_blue">'.$arrTmp["Title"].'</a><span class="widgetmerchant"> - '.$arrTmp["mcName"].'</span></td>';
							$info_html .= '</tr>';
							$info_html .= '<tr><td colspan="2" align="center">'.($ispic==1?'<img width="60" height="'.$height.'" src="'.LINK_ROOT.$arrTmp["ImgUrl"].'"/>':'').'</td></tr>';
							$info_html .= '</table>';
							break;
						case 3:
							$info_html .= ' <table width="140" border="0" cellspacing="0" cellpadding="0"><tr>';
							$info_html .= '<td width="140"><a href="'.$url.'" class="widget_blue">'.$arrTmp["Title"].'</a><span class="widgetmerchant"> - '.$arrTmp["mcName"].'</span> </td></tr>';
							$info_html .= '</table>';

							break;
					}
					
					$info_html .= '</li>';
					$isempty = 0;

				}

				
				if( $isempty == 1 ){
					$Title = "No active coupons & deals at this time. Please get more information from couponsnapshot.com";
					$url = LINK_ROOT;
					switch($size){
						case 1:
							$width = "";
							break;
						case 2:
							$info_html = '<li style="width:108px; text-align:left; padding:29px 0px 3px 0px; margin:0px;" ><a href="'.$url.'" class="widgetblue">'.$Title.'</a></li>';
							break;
						case 3:
							$info_html = '<li style="width:430px; padding:5px 0px 0px 8px; margin:0px;"><a href="'.$url.'" class="widgetblue">'.$Title.'</a></li>';
							break;
						case 4:
							$info_html = '<li style="width:108px; text-align:left; padding:69px 0px 3px 0px; margin:0px;" ><a href="'.$url.'" class="widgetblue">'.$Title.'</a></li>';
							break;
					}
				}
				$objMysql->freeResult($qryId);

				$info = str_replace(array('{*content*}','{*LINK_ROOT*}','{*SITE_DOMAIN*'),array($info_html,LINK_ROOT,substr(SITE_DOMAIN,1)),file_get_contents(INCLUDE_ROOT."tpl/".$sizelist[$size].".tpl"));

				

			}

			$objTpl->assign(array('url_js'=>$CustomerKey."-".$id."-".(int)$widgetsizeid,
							'link_path' => LINK_ROOT,
							'html' => $info,
							'size' => $size,
							'site_domain' => substr(SITE_DOMAIN,1)
						));

			$objTpl->parse('OUT', "url_js_resource".(int)$widgetsizeid);
			$objTpl->parse('OUT', "url2_js_resource".(int)$widgetsizeid);
			$objTpl->TEMPLATE['url_js_resource']['result'] = "";
			$objTpl->TEMPLATE['url2_js_resource']['result'] = "";
			$objTpl->parse('OUT', "verify_page");
			$html = $objTpl->TEMPLATE['verify_page']['result'];
			
			return htmlentities($html,ENT_QUOTES);
		}

	}
}
?>