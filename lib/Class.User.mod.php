<?php
/*
 * FileName: Class.User.php
 * Author: Lee
 * Create Date: 2006-10-18
 * Package: package_name
 * Project: package_name
 * Remark: 
*/
if (!defined("__MOD_CLASS_USER__"))
{
   define("__MOD_CLASS_USER__",1);
   
   class User
   {
   		var $objMysql;
   		
   		function User($objMysql)
   		{
   			$this->objMysql = $objMysql;
   		}
   		
   		function getUserCount($whereStr="")
   		{
   			$total = 0;
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$sql = "select count(*) as cnt from user $whereStr";
   			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			$this->objMysql->freeResult($qryId);
   			$total = intval($arrTmp['cnt']);
   			return $total;
   		}
   		
   		function getUserListByLimitStr($limitStr="", $whereStr="", $orderStr="")
   		{
   			$arr = array();
   			$whereStr = $whereStr ? " WHERE $whereStr " : "";
   			$orderStr = $orderStr ? " ORDER BY $orderStr " : "";
   			$sql = "select ID, Email, Password, NickName, RegisterTime, LastLoginTime, LoginTimes, Status, RegKey, " .
   					"LastChangeTime from user $whereStr $orderStr $limitStr";
   			$qryId = $this->objMysql->query($sql);
   			$i = 0;
   			while($arrTmp = $this->objMysql->getRow($qryId))
   			{
   				$arr[$i]['id'] = intval($arrTmp['ID']);
   				$arr[$i]['email'] = trim($arrTmp['Email']);
   				$arr[$i]['password'] = trim($arrTmp['Password']);
   				$arr[$i]['nickname'] = trim($arrTmp['NickName']);
   				$arr[$i]['lastlogintime'] = trim($arrTmp['LastLoginTime']);
   				$arr[$i]['registertime'] = trim($arrTmp['RegisterTime']);
   				$arr[$i]['logintimes'] = intval($arrTmp['LoginTimes']);
   				$arr[$i]['status'] = trim($arrTmp['Status']);
   				$arr[$i]['regkey'] = trim($arrTmp['RegKey']);
   				$arr[$i]['lastchangetime'] = trim($arrTmp['LastChangeTime']);
   				$i++;
   			}
   			$this->objMysql->freeResult($qryId);
   			return $arr;
   		}
   		
		function getUserById($userId)
		{
			$userId = intval($userId);
   			$arr = $this->getUserListByLimitStr("", " ID = $userId ");
			return $arr[0];
		}
		
		function getUserByEmail($mail)
		{
			$mail = addslashes(trim($mail));
   			$arr = $this->getUserListByLimitStr("", " Email = '$mail'");
			return $arr[0];
		}
		
		function updatePasswordByEmail($mail, $pass)
		{
			$mail = addslashes(trim($mail));
			$pass = addslashes(trim($pass));
			$sql = "update user set Password = '$pass' where Email = '$mail'";
			$this->objMysql->query($sql);
			return;
		}
		
		function getActiveUserByUserID($userId)
		{
			$userId = intval($userId);
			$arr = $this->getUserListByLimitStr("", " ID = $userId and Status = 'ACTIVE' ");
			return $arr;
		}
		
		function getAcitiveUserCnt()
		{
			$cnt = $this->getUserCount(" Status = 'ACTIVE' ");
			return $cnt;
		}
		
		function addUser($email, $password, $nickname, $regkey)
		{
			$email = addslashes(trim($email));
			$password = addslashes(trim($password));
			$nickname = addslashes(trim($nickname));
			$regkey = addslashes(trim($regkey));
			
			$sql = "INSERT INTO user (Email, Password, NickName, RegisterTime, LastLoginTime, LoginTimes, Status, RegKey) VALUES(" .
   					"'$email','$password', '$nickname', '".date("Y-m-d H:i:s")."', '', 0, 'NEW', '$regkey')";
   			$qryId = $this->objMysql->query($sql);
   			return $this->objMysql->getLastInsertId($qryId);
		}
		
		function getUserPassword($userId)
		{
			$userId = intval($userId);
   			$arr = $this->getUserListByLimitStr("", " ID = $userId ");
			return $arr[0]['password'];
	    }
		
		function activeUser($userId)
		{
			$userId = intval($userId);
   			$arr = $this->objMysql->query("update user set Status = 'Active' where ID = $userId");
		}
		
		function IsUniqueEmail($email, $exceptUserId = 0)
	   {
			$email = addslashes(trim($email));
			$exceptUserId = intval($exceptUserId);
			$isUnique = false;
			if($exceptUserId)
		   {
				$cnt = $this->getUserCount(" Email = '$email' and ID <> $exceptUserId ");
				if($cnt == 0)
			   {
					$isUnique = true;
			   }
		   }
		   else
		   {
				$cnt = $this->getUserCount(" Email = '$email' ");
				if($cnt == 0)
			   {
					$isUnique = true;
			   }
		   }
		   return $isUnique;
	   }

		function updateUserLoginInfo($userId)
		{
			$userId = intval($userId);
			$sessionID =  intval($_COOKIE['U_ID']);
			$sql = "update user set LoginTimes = LoginTimes + 1, LastLoginTime = '".date("Y-m-d H:i:s")."' where ID = $userId";
			$this->objMysql->query($sql);
			$sql = "insert into `userlogininfo`(UserID, SessionID) values ($userId, $sessionID) ";
			$this->objMysql->query($sql);
			return;
		}
		
	   function updateUserStatus($userId, $status)
	   {
			//status should in 'ACTIVE', 'NEW', 'INACTIVE'
			$userId = intval($userId);
			$sql = "update user set Status = '$status' where ID = $userId";
			$this->objMysql->query($sql);
			return;
	   }
	   
	   function updateUserPassAndName($userId, $name, $pass='')
	   {
	   		$sql = "update user set NickName = '".addslashes($name)."'";
	   		if($pass) $sql .= ", Password = '".md5($pass)."'";
	   		$sql .= " WHERE ID = $userId";
	   		$qryId = $this->objMysql->query($sql);
	   }
		
		function getUserAlertInfo($userId)
	   {
			$userId = intval($userId);
			$sql = "select MerchantList, Freq from couponalert where UserID = $userId";
			$qryId = $this->objMysql->query($sql);
   			$arrTmp = $this->objMysql->getRow($qryId);
   			return $arrTmp;
	   }

	   function setUserAlert($userId, $merlist, $freq)
	   {
			$userId = intval($userId);
			$merlist = addslashes(trim($merlist));
			$freq = $freq == 'DAILY' ? 'DAILY' : 'WEEKLY';
			$sql = "select count(*) as cnt from couponalert where UserID = $userId";
			$qryId = $this->objMysql->query($sql);
			$arrTmp = $this->objMysql->getRow($qryId);
			$cnt = isset($arrTmp['cnt']) ? intval($arrTmp['cnt']) : 0;
			if(!$cnt)//update
			{
				$sql = "insert into couponalert (UserID, MerchantList, Freq)" .
					"values($userId, '$merlist', '$freq')";
			}
			else // add a new record
			{
				$sql = "update couponalert set MerchantList = '$merlist', Freq = '$freq' where UserID = $userId";
			}
			$this->objMysql->query($sql);
			return;
	   }
	   
   }
}
?>
