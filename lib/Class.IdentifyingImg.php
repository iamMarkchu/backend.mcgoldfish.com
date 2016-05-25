<?php
/*
 * FileName: Class.IdentifyingImg.php
 * Author: Lee
 * Create Date: 2006-9-14
 * Package: package_name
 * Project: project_name
 * Remark: 
*/
if (!defined("__CLASS_IDENTIFYINGIMG__"))
{
   define("__CLASS_IDENTIFYINGIMG__",1);
   
   class IdentifyingImg
   {
   		private $width;
   		private $height;
   		private $len;
   		private $noise;
   		private $bgcolor;
   		private $noisenum;
   		private $border;
   		private $bordercolor;
   		private $img = null;
   		
   		public function __Construct($width=45, $height=22, $len=4, $bgcolor="#ffffff", $noise=true, $noisenum=60, $border=false, $bordercolor="#cccccc")
		{
	   		$this->width = $width;
	   		$this->height = $height;
	   		$this->len = $len;
	   		$this->bgcolor = $bgcolor;
	   		$this->noisenum = $noisenum;
	   		$this->noise = $noise;
	   		$this->border = $border;
	   		$this->bordercolor = $bordercolor;
	   		//setCookie('verifycode', "", 0, "/", SITE_DOMAIN);
			$_SESSION["verifycode"] = "";
		}
   		
   		public function showImg()
   		{
   			$this->img = imageCreate($this->width, $this->height);
   			$back = $this->getcolor($this->bgcolor);
   			imageFilledRectangle($this->img, 0, 0, $this->width, $this->height, $back);
   			$size = $this->width/$this->len;
   			if($size>$this->height) $size=$this->height;
   			$left = ($this->width-$this->len*($size+$size/10))/$size;
   			$textall = array_merge(range('A','Z'));
   			shuffle($textall);
   			$code = "";
   			for ($i=0; $i<$this->len; $i++)
   			{
			    $tmptext = rand(0, 25);
				$randtext = $textall[$tmptext];
			    $code .= $randtext;
			}
			$textColor = imageColorAllocate($this->img, 0, 0, 0);
			imagestring($this->img, $size, 5, 5, $code, $textColor);
			if($this->noise == true)
			{
				$this->setnoise();	
			}
			//setCookie('verifycode', md5(strtolower($code)), 0, "/", SITE_DOMAIN);
			$_SESSION['verifycode'] = md5(strtolower($code));
			$this->bordercolor = $this->getcolor($this->bordercolor); 
			if($this->border==true)
			{
				imageRectangle($this->img, 0, 0, $this->width-1, $this->height-1, $this->bordercolor);
			}	
			header("Content-type: image/png");
			imagePng($this->img);
			imagedestroy($this->img);
   		}
		
		private function getcolor($color)
		{
		     $color = eregi_replace ("^#","",$color);
		     $r = $color[0].$color[1];
		     $r = hexdec ($r);
		     $b = $color[2].$color[3];
		     $b = hexdec ($b);
		     $g = $color[4].$color[5];
		     $g = hexdec ($g);
		     $color = imagecolorallocate ($this->img, $r, $b, $g); 
		     return $color;
		}
		
		private function setnoise()
		{
			for ($i=0; $i<$this->noisenum; $i++)
			{
				$randColor = imageColorAllocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255));  
				imageSetPixel($this->img, rand(0, $this->width), rand(0, $this->height), $randColor);
			} 
		}
   }
}
?>
