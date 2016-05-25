<?php
if( !defined("CLASS_EMAILDIRECT_MAILER_PHP") ) {
	define("CLASS_EMAILDIRECT_MAILER_PHP", "YES");
	/*
	* 	Send mails by EmailDirect
	*	
	*/
	class EmailDirect{
		//wsdl
		private $clinet;
		private $encode = "";
		private $AccountName = "";
		private $Password = "";
		
		//smpt 
		private $managerEmail = "jimmy@couponsnapshot.com,ikezhao@megainformationtech.com";
		private $sender = "";
		private $smtp_host = "";
		private $smtp_port = "";
		private $smtp_user = "";
		private $smtp_pass = "";
		public  $sendToManagerSwitch = "YES";
		
		//logs
		public $error_log = "EmailDerect_error_log.txt";
		public $error_times = "EmailDerect_error_times.txt";
		
		function __construct($smtpserver, $port, $smtpuser, $smtppwd, $sender, $encode = "utf-8")
		{
			
			$wsdlAddress = "http://dev.emaildirect.com/v1/api.asmx?WSDL";
			$wsdlUser = "MegaInfo";
			$wsdlPass = "m3GaInf0";
			
			$this->AccountName 	= $wsdlUser;
			$this->Password 	= $wsdlPass;
			$this->sender 		= $sender;
			$this->smtp_host 	= $smtpserver;
			$this->smtp_port 	= $port;
			$this->smtp_user 	= $smtpuser;
			$this->smtp_pass 	= $smtppwd;
			try{
				$this->client = new SoapClient($wsdlAddress,array('encoding'=>$encode));
				return true;
			}catch (Exception $e){
				try{
					sleep(5);
					$this->client = new SoapClient($wsdlAddress,array('encoding'=>$encode));
					return true;
				}catch (Exception $e){
					try{
						sleep(20);
						$this->client = new SoapClient($wsdlAddress,array('encoding'=>$encode));
						return true;
					}catch (Exception $e){
						try{
							sleep(30);
							$this->client = new SoapClient($wsdlAddress,array('encoding'=>$encode));
							return true;
						}catch (Exception $e){
							$errorMsg = $e->getMessage();
							$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . $errorMsg . "\n" , 'a');
							//count error times
							$times = $this->readFailCount();
							//Continuous 100 times send error,send a mail to manager by SMTP
							if($times >= 100 && $this->sendToManagerSwitch == "YES"){
								$bodyString = "Emaildirect service or network may have problems, 
												Continuous 100 times send failed, 
												see details please check the log file!  ";
								$subjectString = "mail system error";
								$emailUniqueID = md5(uniqid(rand(), true));
								$res = $this->sendmail_smtp($this->managerEmail,$this->sender, $subjectString, $bodyString , $emailUniqueID);
								if($res == false){
									$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "failed send to manager (" . $this->managerEmail . ")\n" , 'a');
								}
								$this->writeLineToFile(LOG_LOCATION.$this->error_times, "times=0" . "\n" , 'w');
								return $errorMsg;
							}
							$this->writeLineToFile(LOG_LOCATION.$this->error_times, "times=" . (int)($times + 1) . "\n" , 'w');
							return $errorMsg;
						}
					}
				}
			}
			return false;
		}	
		
		
		public function send($mailTo, $fromEmail="", $subject="", $body = "", $RelaySendCategoryName="default", $bodyType="Text", $fromName = "", $Tags=array("string"=>""))
		{
			try{
				$RelaySendCategoryID = "0";
				if(is_array($mailTo)){
					$mailToObj = array();
					foreach ($mailTo as $key=>$values){
						$RelaySendToVar = (object)array("ToEmail"=>$values,
							"ToName" =>$values,
							"Variables"=>""
								);
						$mailToObj[] = $RelaySendToVar;
					}
				}else{
					$mailToObj = array();
					$RelaySendToVar = (object)array("ToEmail"=>$mailTo,
						"ToName" =>"",
						"Variables"=>""
							);
					$mailToObj[] = $RelaySendToVar;
				}
				
				$para = array(
						"Creds"=>array(
								"AccountName"=>$this->AccountName,
								"Password"=>$this->Password,
								"Enc"=>""),
						"RelaySendCategoryID"=>$RelaySendCategoryID,
						"ToEmailVars"=>$mailToObj,
						"FromEmail"=>$fromEmail,
						"FromName" => $fromName,
						"CreativeID"=> "0",
						"Subject" => $subject,
						"TrackLinks" => "",
						"Tags" => $Tags,
						"AddToDatabase"=>"",
						"SourceID" => "",
						"Force" => ""
					);
					$body = $this->addTracking($body);
					if($bodyType == "HTML" ){
						$para["BodyHTML"] = $body;

					}else{
						$para["BodyText"] = $body;
					}
					
					if(isset($_SESSION["CategoryList"])){
						if(isset($_SESSION["CategoryList"][SID_PREFIX."_".$RelaySendCategoryName])){
							$RelaySendCategoryID = $_SESSION["CategoryList"][SID_PREFIX."_".$RelaySendCategoryName];
						}else{
							$RelaySendCategoryID = $_SESSION["CategoryList"][SID_PREFIX."default"];
						}
					}else{
						$res = $this->GetCategories();
						if(is_array($res)){
							$_SESSION["CategoryList"] = $res;
							if(isset($_SESSION["CategoryList"][SID_PREFIX."_".$RelaySendCategoryName])){
								$RelaySendCategoryID = $_SESSION["CategoryList"][SID_PREFIX."_".$RelaySendCategoryName];
							}else{
								$RelaySendCategoryID = $_SESSION["CategoryList"][SID_PREFIX."_default"];
							}
						}
					}
					$para["RelaySendCategoryID"] = $RelaySendCategoryID;
					
					if($RelaySendCategoryID == "0"){
						$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "Failed send to:" . $values->Email . " (". "Can not get RelaySendCategoryID" .")\n" , 'a');
						
						return "Can not get RelaySendCategoryID";
					}
					try{
						$res = $this->client->RelaySend_SendEmailVars($para);
		//				$temp = $this->client->__getLastRequestHeaders();
		//				$temp = $this->client->__getLastRequest();
					}catch (Exception $e){
						sleep(5);
						try{
							$res = $this->client->RelaySend_SendEmailVars($para);
						}catch (Exception $e){
							sleep(20);
							try{
								$res = $this->client->RelaySend_SendEmailVars($para);
							}catch (Exception $e){
								sleep(30);
								try{
									$res = $this->client->RelaySend_SendEmailVars($para);	
								}catch(Exception $e){
									throw new Exception($e->getMessage());
								}
							}
						}
					}
					$returnArray = array();
					if(isset($res->RelaySend_SendEmailVarsResult->RelaySendReceipt)){
						if(is_array($res->RelaySend_SendEmailVarsResult->RelaySendReceipt)){
							
							foreach ($res->RelaySend_SendEmailVarsResult->RelaySendReceipt as $key => $values){
								
								$returnArray[$key] = array("Email" 				=> $values->Email,
														   "RealySendReceipt" 	=> $values->RealySendReceipt,
														   "ResultStatus" 		=> $values->ResultStatus);
								if($values->ResultStatus != "Success"){
									$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "Failed send to:" . $values->Email . " (". $values->ResultStatus .")\n" , 'a');
								}
							}
						}else{
				
								$values = $res->RelaySend_SendEmailVarsResult->RelaySendReceipt;
								$returnArray[0] = array("Email" 			=> $values->Email,
												    "RealySendReceipt" 	=> $values->RealySendReceipt,
												    "ResultStatus" 		=> $values->ResultStatus);
								if($values->ResultStatus != "Success"){
									$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "Failed send to:" . $values->Email . " (". $values->ResultStatus .")\n" , 'a');
								}
						}
					}else{
						$returnArray = "error";
						$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "Failed send to(" . $mailTo[0] . ")\n" , 'a');
					}
					$this->writeLineToFile(LOG_LOCATION.$this->error_times, "times=" . 0 . "\n" , 'w');
					return $returnArray;
					
				}catch(Exception $e){
					
					$errorMsg = $e->getMessage();
					$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . $errorMsg . "\n" , 'a');
					//count error times
					$times = $this->readFailCount();
					//Continuous 100 times send error,send mail to manager
					if($times >= 100 && $this->sendToManagerSwitch == "YES"){
						$bodyString = "Emaildirect service or network may have problems, 
							Continuous 100 times send failed, 
							see details please check the log file! ";
						$emailUniqueID = md5(uniqid(rand(), true));
						$res = $this->sendmail_smtp($this->managerEmail,$this->sender,"mail system error", $bodyString, $emailUniqueID);
						if($res == false){
							$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "failed send to manager (" . $this->managerEmail . ")\n" , 'a');
						}
						$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "send mail to manager (" . $this->managerEmail . ")\n" , 'a');
						$this->writeLineToFile(LOG_LOCATION.$this->error_times, "times=0" . "\n" , 'w');
						return $errorMsg;
					}
					$this->writeLineToFile(LOG_LOCATION.$this->error_times, "times=" . (int)($times + 1) . "\n" , 'w');
					
					return $errorMsg;
				}
			}
			
			public function writeLineToFile($fileName, $writeString, $writeMode='a')
			{
				try{
					@error_log($writeString,3,$fileName);
					return true;
				}catch(Exception $e){
					return $e->getMessage();
				}
			}
			
			public function readFailCount()
			{
				if(!file_exists(LOG_LOCATION.$this->error_times)){
					return 0;
				}
				$timesFile = file(LOG_LOCATION.$this->error_times);
				$count = 0;
				if(count($timesFile) == 0){
					return 0;
				}else{
					$count = explode("=",$timesFile[0]);
					$count = (int)$count[1];
					return $count;
				}
			}
			//wsdl error ,Mail to manager by SMTP 
			function sendmail_smtp($to, $sender, $subject, $body, $uid="", $emailcontenttype="HTML")
			{
				include_once(INCLUDE_ROOT."lib/Class.SMTP.php");
				$objSmtpMailer =   new SmtpMailer($this->smtp_host,$this->smtp_port, true,$this->smtp_user, $this->smtp_pass,$this->sender);
				$objSmtpMailer->debug = false;
				$send=$objSmtpMailer->sendmail($to, $sender, $subject, $body, $emailcontenttype);
				if($send==1){
					$this->writeLineToFile(LOG_LOCATION.$this->error_log, date("Y-m-d H:i:s").": " . "send message to manager(" . $to . ")\n" , 'a');
					return true;
				}
				return false;
			}
		public	function addTracking($body){
				if(stristr(str_replace(' ', '', $body), "</body>") === false){
					$body = $body . '<img src="[Link_Impression]" width="5" height="5" alt="" />';
				}else{
					$body = str_replace("</body>",'<img src="[Link_Impression]" width="5" height="5" alt="" />'."</body>" , $body);
				}
				return $body;
			}
			
		public function GetCategories(){
			try{
				$categoryListArray = array();
				$para = array(
					"Creds"=>array(
						"AccountName"=>$this->AccountName,
						"Password"=>$this->Password,
						"Enc"=>"")
				);
				$res = $this->client->RelaySend_GetCategories($para);
				if(isset($res->RelaySend_GetCategoriesResult->Element)){
					$categoryListObj = $res->RelaySend_GetCategoriesResult->Element;
					
					if(is_array($categoryListObj)){
						foreach ($categoryListObj as $key=>$value){
							$categoryListArray[$value->ElementName] = $value->ElementID;
						}
					}
				}
				return $categoryListArray;
			}catch (Exception $e){
				return $e->getMessage();
			}
		}		
		
	}
}


?>