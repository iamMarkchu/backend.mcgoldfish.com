<?php
if( !defined("CLASS_MAILER_PHP") ) {
    define("CLASS_MAILER_PHP", "YES");
	    
    class Mailer {
    	var $objStats;
    	
    	function Mailer($objStats)
    	{
    		$this->objStats = $objStats;
    	}
    	
		function sendmail_edm(&$_info)
		{
			$mailSender = "http://edm.megainformationtech.com/sendmail.php";
			$ch = curl_init($mailSender);
			curl_setopt($ch, CURLOPT_URL,$mailSender);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_NOBODY, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, "sendmail_edm");
			curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$_info);
			$pagecontent = curl_exec($ch);
			$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($curl_code != 200) return false;
			if(substr($pagecontent,0,1) != "1") return false;
			return true;
		}
		
    	function sendmail($to, $subject, $content, $type="", $from="", $uid="")
    	{
    		$from = trim($from);
			if(!$from) $from = "no-reply@".trim(SITE_DOMAIN, ".");

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: $from\r\n";
		
			if(@mail($to,$subject,wordwrap($content,70), $headers))
			{
				$status = 'send';
				$this->objStats->setEmailLog($to,$status,$type,$subject,$headers,$content,$uid);
				return true;
			}
			return false;
    	}
    	
    	function sendmail_smtp($to, $sender, $subject, $body, $uid="", $emailtype="", $emailcontenttype="HTML")
    	{
    		include_once(INCLUDE_ROOT."lib/Class.SMTP.php");
			$objSmtpMailer =   new SmtpMailer(SMTP_HOST,SMTP_PORT, true,SMTP_USER, SMTP_PASS,$sender);
			$objSmtpMailer->debug = false;
			$send=$objSmtpMailer->sendmail($to, $sender, $subject, $body, $emailcontenttype);
			if($send==1){
				$this->objStats->setEmailLog($to,'send',$emailtype,$subject,$sender,$body,$uid);
				return true;
			}
			return false;
    	}
    	
    	//'$email, SITE_FULL_NAME."<".SMTP_ALERT_SENDER.">", $mailSubject, $mainContent, $emailUniqueID, "couponalertconfirm'
    	
    	function sendmail_EmailDirect($to, $sender, $subject, $body, $uid="", $emailtype="", $emailcontenttype="HTML", $encode = "iso-8859-1")
    	{
    	
			$sent = TRUE;
			$mail_from = $this->getAddresAndName($sender);
			
			if($encode == "iso-8859-1"){
				$subject = iconv("iso-8859-1", "utf-8", $subject);
				$body = iconv("iso-8859-1", "utf-8", $body);
			}
			
			$TO = explode(",", $to);
			
			//include_once("Class.EmailDirect.php");
			include_once(INCLUDE_ROOT."lib/Class.EmailDirect.php");
			if(!defined("LOG_LOCATION")){
				define("LOG_LOCATION",INCLUDE_ROOT."logs/");
			}
			$EmailDirectObj = new EmailDirect(SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, $mail_from["address"]);

			$res = $EmailDirectObj->send($TO, $mail_from["address"], $subject, $body, $emailtype, $emailcontenttype, $mail_from["name"]);

			if(is_array($res)){
				$i = 0;
				foreach ($res as $rcpt_to){
					if($rcpt_to["ResultStatus"] == "Success"){
						$this->objStats->setEmailLog($to,'send',$emailtype,$subject,$sender,$body,$uid);
						return true;
					}else{
						return false;
					}
				}
			 	
			}else{
				return false;
			}

    	}
    	function getAddresAndName($address){
    		$res = array();
    		$index = stristr($address, "<");
    		if($index === false){
    			$res["name"] = "";
    			$res["address"] = $address;
    			return $res;
    		}else{
    			$pos = strpos($address, "<");
    			$res["name"] = substr($address, 0, $pos);
    			$res["address"] = substr($index, 1, strlen($index ) -2 );
    			return $res;
    		}
    	}
    	
    }
}
?>
