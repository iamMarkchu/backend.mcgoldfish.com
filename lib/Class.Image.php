<?php
/*
 * FileName: Class.Image.php
 * Author: Lee
 * Create Date: 2006-9-19
 * Package: package_name
 * Project: internalTool
 * Remark: 
*/
if (!defined("__CLASS_IMAGE__"))
{
   define("__CLASS_IMAGE__",1);
   class Image
	{
		var $imageTypeDefine = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG',  4 => 'SWF',
								 5 => 'PSD', 6 => 'BMP',  7 => 'TIFF(intel byte order)',  8 => 'TIFF(motorola byte order)',
								 9 => 'JPC', 10 => 'JP2',  11 => 'JPX',  12 => 'JB2',  13 => 'SWC', 
								 14 => 'IFF',   15 => 'WBMP', 16 => 'XBM');

		function getImageInfo($img)
		{
			$imgInfo = getimagesize($img);
			if($imgInfo)
			{
				$imgWidth  = $imgInfo[0];
				$imgHeight = $imgInfo[1];
				$tmpStr    = $imgInfo[2];
				if(isset($this->imageTypeDefine[$tmpStr]))
				{
					$imgType = $this->imageTypeDefine[$tmpStr];
				}
				else
				{
					$imgType = "unknown";
				}
				$imgSize = filesize($img);
				return array('w'=>$imgWidth, 'h'=>$imgHeight, 'type'=>$imgType, 'size'=>$imgSize);
			}
			else
			{
				return array();
			}
		}

		function getZoomSize($srcW,$srcH,$dstW,$dstH)
		{	
			$devideW = $devideH = 0;
			$tmp     = 1;
			if ($srcW > $dstW) 
			{
				$devideW = $dstW == 0 ? 0 : $srcW / $dstW;
			}
			if ($srcH > $dstH)
			{
				$devideH = $dstH == 0? 0 : $srcH / $dstH;
			}
			if($devideW > $devideH && $devideW > 1)
			{
				$tmp = $devideW;
			}
			elseif($devideH > $devideW && $devideH > 1)
			{
				$tmp = $devideH;
			}
			else 
			{
				return array($dstW, $dstH);
			}
			$dstW  = $srcW / $tmp;
			$dstH  = $srcH / $tmp;
			return array($dstW, $dstH);
        }

        function imageZoom($srcFile, $imgTpl, $dstFile='')
        {
        	$tpl = $imgTpl;
        	$arrTmp = GetImageSize($tpl);
        	$dstW = $arrTmp[0];
        	$dstH = $arrTmp[1];

            $data = GetImageSize($srcFile);

            switch ($data[2]) {
                case 1:
					$im = @ImageCreateFromGIF($srcFile);
					break;
                case 2:
					$im = @ImageCreateFromJPEG($srcFile);
					break;
                case 3:
					$im = @ImageCreateFromPNG($srcFile);
					break;
				default:
					//die("can not create source image resource");
					return false;
					break;
            }

            $srcW = ImageSX($im);
            $srcH = ImageSY($im);
            $newsize = $this->getZoomSize($srcW,$srcH,$dstW,$dstH);
            $dstW    = $newsize[0];
            $dstH    = $newsize[1];
            
			$ni = imagecreatefromjpeg($tpl);
			
			if(($dstW > $srcW) && ($dstH > $srcW)) //combine the image
			{
				imagecopymerge($ni, $im, $dstW/2-$srcW/2, $dstH/2-$srcH/2, 0, 0, $srcW, $srcH, 100); //the same as imageCopy();
			}
			else
			{
				if (function_exists("imagecopyresampled"))
				{
					imagecopyresampled($ni, $im, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
				}
				else
				{
					ImageCopyResized ($ni, $im, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
				}
			}
            if($dstFile == "")
			{
				header("Content-type: image/jpeg");
				imagejpeg($ni);
			}
            else
			{
				imagejpeg($ni,$dstFile);
			}
            ImageDestroy ($ni);
            ImageDestroy ($im);
        }
    }
}

//$img = 'F:/workspace/youquanduo/merchantImg/14/20061022132801_58f65d1afb54fd8b95cddb3be0770314.gif';
//$oImageResize = new Image();
//$imgInfo = $oImageResize->getImageInfo($img);
////$oImageResize->imageZoom($img,"F:/workspace/youquanduo/prodImg/small150_75Tpl.jpg",'F:/workspace/youquanduo/merchantImg/a9/20061021222643_96b424bd71f5a2f783c1a50742fbc9a9_new.gif');
//$oImageResize->imageZoom($img,"F:/workspace/youquanduo/prodImg/small150_75Tpl.jpg",'');

/* 
* ���ܣ�PHPͼƬˮӡ (ˮӡ֧��ͼƬ������) 
* ������ 
* $groundImage ����ͼƬ������Ҫ��ˮӡ��ͼƬ����ֻ֧��GIF,JPG,PNG��ʽ�� 
* $waterPos ˮӡλ�ã���10��״̬��0Ϊ���λ�ã� 
* 1Ϊ���˾���2Ϊ���˾��У�3Ϊ���˾��ң� 
* 4Ϊ�в�����5Ϊ�в����У�6Ϊ�в����ң� 
* 7Ϊ�׶˾���8Ϊ�׶˾��У�9Ϊ�׶˾��ң� 
* $waterImage ͼƬˮӡ������Ϊˮӡ��ͼƬ����ֻ֧��GIF,JPG,PNG��ʽ�� 
* $waterText ����ˮӡ������������ΪΪˮӡ��֧��ASCII�룬��֧�����ģ� 
* $textFont ���ִ�С��ֵΪ1��2��3��4��5��Ĭ��Ϊ5�� 
* $textColor ������ɫ��ֵΪʮ��������ɫֵ��Ĭ��Ϊ#FF0000(��ɫ)�� 
* 
* ע�⣺Support GD 2.0��Support FreeType��GIF Read��GIF Create��JPG ��PNG 
* $waterImage �� $waterText ��ò�Ҫͬʱʹ�ã�ѡ����֮һ���ɣ�����ʹ�� $waterImage�� 
* ��$waterImage��Чʱ������$waterString��$stringFont��$stringColor������Ч�� 
* ��ˮӡ���ͼƬ���ļ����� $groundImage һ���� 
* ���ߣ�longware @ 2004-11-3 14:15:13 
*/
//$img = "test_resize.jpeg";
//imageWaterMark($img, 4, "", "www.nail-auto.com");

function imageWaterMark($groundImage,$waterPos=0,$waterImage="",$waterText="",$textFont=5,$textColor="#CCCCCC") 
{ 
	$isWaterImage = FALSE; 
	$formatMsg = "�ݲ�֧�ָ��ļ���ʽ������ͼƬ����������ͼƬת��ΪGIF��JPG��PNG��ʽ��"; 

	//��ȡˮӡ�ļ� 
	if(!empty($waterImage) && file_exists($waterImage)) 
	{ 
		$isWaterImage = TRUE; 
		$water_info = getimagesize($waterImage); 
		$water_w = $water_info[0];//ȡ��ˮӡͼƬ�Ŀ� 
		$water_h = $water_info[1];//ȡ��ˮӡͼƬ�ĸ� 

		switch($water_info[2])//ȡ��ˮӡͼƬ�ĸ�ʽ 
		{ 
			case 1:
				$water_im = imagecreatefromgif($waterImage);
				break; 
			case 2:
				$water_im = imagecreatefromjpeg($waterImage);
				break; 
			case 3:
				$water_im = imagecreatefrompng($waterImage);
				break; 
			default:
				die($formatMsg); 
		} 
	} 

	//��ȡ����ͼƬ 
	if(!empty($groundImage) && file_exists($groundImage)) 
	{ 
		$ground_info = getimagesize($groundImage); 
		$ground_w = $ground_info[0];//ȡ�ñ���ͼƬ�Ŀ� 
		$ground_h = $ground_info[1];//ȡ�ñ���ͼƬ�ĸ� 

		switch($ground_info[2])//ȡ�ñ���ͼƬ�ĸ�ʽ 
		{ 
			case 1:
				$ground_im = imagecreatefromgif($groundImage);
				break; 
			case 2:
				$ground_im = imagecreatefromjpeg($groundImage);
				break; 
			case 3:
				$ground_im = imagecreatefrompng($groundImage);
				break; 
			default:
				die($formatMsg); 
		} 
	} 
	else 
	{ 
		die("��Ҫ��ˮӡ��ͼƬ�����ڣ�"); 
	} 

	//ˮӡλ�� 
	if($isWaterImage)//ͼƬˮӡ 
	{ 
		$w = $water_w; 
		$h = $water_h; 
		$label = "ͼƬ��"; 
	} 
	else//����ˮӡ 
	{ 
		$temp = imagettfbbox(ceil($textFont*2.5),0,"./arial.ttf",$waterText);//ȡ��ʹ�� TrueType ������ı��ķ�Χ 
		$w = $temp[2] - $temp[6]; 
		$h = $temp[3] - $temp[7]; 
		unset($temp); 
		$label = "��������"; 
	} 
	if( ($ground_w<$w) || ($ground_h<$h) ) 
	{ 
		echo "��Ҫ��ˮӡ��ͼƬ�ĳ��Ȼ���ȱ�ˮӡ".$label."��С���޷�����ˮӡ��"; 
		return; 
	} 
	switch($waterPos) 
	{ 
		case 0://��� 
			$posX = rand(0,($ground_w - $w)); 
			$posY = rand(0,($ground_h - $h)); 
			break; 
		case 1://1Ϊ���˾��� 
			$posX = 0; 
			$posY = 0; 
			break; 
		case 2://2Ϊ���˾��� 
			$posX = ($ground_w - $w) / 2; 
			$posY = 0; 
			break; 
		case 3://3Ϊ���˾��� 
			$posX = $ground_w - $w; 
			$posY = 0; 
			break; 
		case 4://4Ϊ�в����� 
			$posX = 0; 
			$posY = ($ground_h - $h) / 2; 
			break; 
		case 5://5Ϊ�в����� 
			$posX = ($ground_w - $w) / 2; 
			$posY = ($ground_h - $h) / 2; 
			break; 
		case 6://6Ϊ�в����� 
			$posX = $ground_w - $w; 
			$posY = ($ground_h - $h) / 2; 
			break; 
		case 7://7Ϊ�׶˾��� 
			$posX = 0; 
			$posY = $ground_h - $h; 
			break; 
		case 8://8Ϊ�׶˾��� 
			$posX = ($ground_w - $w) / 2; 
			$posY = $ground_h - $h; 
			break; 
		case 9://9Ϊ�׶˾��� 
			$posX = $ground_w - $w; 
			$posY = $ground_h - $h; 
			break; 
		default://��� 
			$posX = rand(0,($ground_w - $w)); 
			$posY = rand(0,($ground_h - $h)); 
			break; 
	} 

	//�趨ͼ��Ļ�ɫģʽ 
	imagealphablending($ground_im, true); 

	if($isWaterImage)//ͼƬˮӡ 
	{ 
		imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w,$water_h);//����ˮӡ��Ŀ���ļ� 
	} 
	else//����ˮӡ 
	{ 
		if( !empty($textColor) && (strlen($textColor)==7) ) 
		{ 
			$R = hexdec(substr($textColor,1,2)); 
			$G = hexdec(substr($textColor,3,2)); 
			$B = hexdec(substr($textColor,5)); 
		} 
		else 
		{ 
			die("ˮӡ������ɫ��ʽ����ȷ��"); 
		} 
		imagestring($ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B)); 
	} 

	//����ˮӡ���ͼƬ 
	@unlink($groundImage); 
	switch($ground_info[2])
	{ 
		case 1:
			imagegif($ground_im,$groundImage);
			break; 
		case 2:
			imagejpeg($ground_im,$groundImage);
			break; 
		case 3:
			imagepng($ground_im,$groundImage);
			break; 
		default:
			break;
	} 

	if(isset($water_info)) unset($water_info); 
	if(isset($water_im)) imagedestroy($water_im); 
	unset($ground_info); 
	imagedestroy($ground_im); 
}

?>