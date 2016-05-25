<?php

/*
 * FileName: Class.Community.mod.php
 * Author: Luke
 * Create Date: 2011-09-04
 * Package: package_name
 * Project: package_name
 * Remark: 
 */
if (!defined("__MOD_CLASS_COMMUNITY__")) {
    define("__MOD_CLASS_COMMUNITY__", 1);

    class Community {

        var $objMysql;

        function Community($objMysql) {
            $this->objMysql = $objMysql;
        }

        function getUserListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID`,`Email`,`Password`,`FirstName`,`LastName`,`Gender`,`Birthday`,`BackupEmail`,`Describe`,`Address`,`Country`,`Phone`,`Twitter`,`Facebook`,`Status`,`Point`,`LockedPoint`,`LastLoginTime`,`TotalLoginTimes`,`TotalCouponShared`,`TotalCouponApproved`,`TotalPointsEarned`,`AddTime`,`LastChangeTime` , `InActiveReason` ,`Token`, `OpenIDType`, `OpenID`";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_user $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getUserAccountListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID`,`UserID`,`ExchangeTypeID`,`AccountHolderName`,`Account`, `AddTime` ,`LastChangeTime` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_user_account $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getUserMessageCount($whereStr="") {
            $total = 0;
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $sql = "select count(*) as cnt from community_message $whereStr";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['cnt']);
            return $total;
        }

        function getUserMessageListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID`,`FromUserID`,`ToUserID`,`Message`, `AddTime` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_message $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getCouponApprovalCount($whereStr="") {
            $total = 0;
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $sql = "select count(*) as cnt from community_coupon_approval $whereStr";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['cnt']);
            return $total;
        }

        function getCouponApprovalListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID` , `CouponID` , `MerchantID` , `CategoryID` , `UserID` , `Title` , `Code` , `Remark` , `Url` , `Tag` , `StartTime` , `EndTime` , `ExpireTime` , `ImgUrl` , `Savings`, `SavingsType` , `Status` , `DiscountType` , `ApprovalDate` , `DisapprovedReason` , `AddTime` , `LastChangeTime`, `EditorName` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_coupon_approval $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getEarnedPointCount($whereStr="") {
            $total = 0;
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $sql = "select count(*) as cnt from community_earned_point $whereStr";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['cnt']);
            return $total;
        }

        function getEarnedPointSum($whereStr="") {
            $total = 0;
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $sql = "select sum(Point) as sum from community_earned_point $whereStr";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['sum']);
            return $total;
        }

        function getEarnedPointListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID`,`UserID`,`CouponID`,`Point`,`Balance`,`Reason`,`AddTime`,`Type` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_earned_point $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getMerchantExceptionsListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : "`ID`,`MerchantID`,`MerchantName`,`Reason`,`Operator`, `AddTime` ,`LastUpdateTime` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_merchant_Exceptions $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getExchangedPointCount($whereStr="") {
            $total = 0;
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $sql = "select count(*) as cnt from community_exchanged_point $whereStr";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['cnt']);
            return $total;
        }

        function getExchangedPointListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : " `ID` , `UserID` , `Point` , `ExchangeTypeID` , `Remark` , `Status` , `IgnoreReason` , `AddTime` , `LastChangeTime` ,`Balance` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_exchanged_point $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getExchangedTypeListByLimitStr($limitStr="", $whereStr="", $orderStr="", $returnIdOnly=false, $foundrows=false) {
            $whereStr = $whereStr ? " WHERE $whereStr " : "";
            $orderStr = $orderStr ? " ORDER BY $orderStr " : "";

            $fields = $returnIdOnly ? "`ID`" : " `ID` , `Name`, `Type` , `Remark` , `IsActive`, `Point` ";

            if ($foundrows)
                $fields = "SQL_CALC_FOUND_ROWS $fields";
            $sql = "select $fields from community_exchange_type $whereStr $orderStr $limitStr";

            $keyname = $returnIdOnly ? "ID" : "";
            $rows = $this->objMysql->getRows($sql, $keyname, $foundrows);
            if ($foundrows)
                $this->FOUND_ROWS = $this->objMysql->FOUND_ROWS;
            if ($returnIdOnly)
                return array_keys($rows);
            return $rows;
        }

        function getCouponApprovalListGroupByDateCount($whereStr="") {
            $total = 0;
            $sql = "SELECT count(*) as cnt FROM (SELECT COUNT(*) AS c, DATE_FORMAT(`AddTime`,'%Y-%m-%d') AS dfa  FROM `community_coupon_approval` WHERE $whereStr GROUP BY dfa) AS a1 LEFT JOIN (SELECT COUNT(*) AS ac, DATE_FORMAT(`AddTime`,'%Y-%m-%d') AS adfa  FROM `community_coupon_approval` WHERE $whereStr AND `Status`='Approved' GROUP BY adfa) AS a2 ON a1.dfa = a2.adfa";
            $qryId = $this->objMysql->query($sql);
            $arrTmp = $this->objMysql->getRow($qryId);
            $this->objMysql->freeResult($qryId);
            $total = intval($arrTmp['cnt']);
            return $total;
        }

        function getCouponApprovalListGroupByDate($limitStr="", $whereStr="") {
            $sql = "SELECT * FROM (SELECT COUNT(*) AS c, DATE_FORMAT(`AddTime`,'%Y-%m-%d') AS dfa  FROM `community_coupon_approval` WHERE $whereStr GROUP BY dfa) AS a1 LEFT JOIN (SELECT COUNT(*) AS ac, DATE_FORMAT(`AddTime`,'%Y-%m-%d') AS adfa  FROM `community_coupon_approval` WHERE $whereStr AND `Status`='Approved' GROUP BY adfa) AS a2 ON a1.dfa = a2.adfa order by dfa desc $limitStr";
            return $this->objMysql->getRows($sql);
        }

        function fillPointInfoByDate($userID,&$arrCoupon) {
            $arrPointDate = array();
            foreach ($arrCoupon as &$theCoupon) {
                $theCoupon["Point"] = 0;
                $arrPointDate[$theCoupon["dfa"]] = $theCoupon["dfa"];
            }

            if (sizeof($arrPointDate)) {
                $sql = "SELECT SUM(p.Point) AS pcount,DATE_FORMAT(c.`AddTime`,'%Y-%m-%d') AS pdate FROM community_earned_point AS p,community_coupon_approval AS c WHERE p.UserID=$userID and p.CouponID=c.ID AND DATE_FORMAT(c.`AddTime`,'%Y-%m-%d') IN ('" . implode("','", $arrPointDate) . "') GROUP BY pdate";
                $arrPoint = $this->objMysql->getRows($sql, "pdate");
                foreach ($arrCoupon as &$theCoupon) {
                    $cid = $theCoupon["dfa"];
                    if (isset($arrPoint[$cid])) {
                        $theCoupon["Point"] = $arrPoint[$cid]["pcount"];
                    }
                }
            }
        }

        function fillMerchantInfo(&$arrCoupon) {
            $arrMerchantId = array();
            foreach ($arrCoupon as &$theCoupon) {
                if (!isset($theCoupon["MerchantID"]))
                    continue;
                $arrMerchantId[$theCoupon["MerchantID"]] = $theCoupon["MerchantID"];
            }

            if (sizeof($arrMerchantId)) {
                $sql = "select id,name,logo from normalmerchant where ID in (" . implode(",", $arrMerchantId) . ")";
                $arrMerchant = $this->objMysql->getRows($sql, "id");
                foreach ($arrCoupon as &$theCoupon) {
                    $merid = $theCoupon["MerchantID"];
                    if (isset($arrMerchant[$merid])) {
                        $theCoupon["MerchantName"] = $arrMerchant[$merid]["name"];
                        $theCoupon["MerchantLogo"] = $arrMerchant[$merid]["logo"];
                    } else {
                        $theCoupon["MerchantName"] = "";
                        $theCoupon["MerchantLogo"] = "";
                    }
                }
            }
        }

        function fillPointInfo(&$arrCoupon) {
            $arrPointId = array();
            foreach ($arrCoupon as &$theCoupon) {
                $arrPointId[$theCoupon["ID"]] = $theCoupon["ID"];
            }

            if (sizeof($arrPointId)) {
                $sql = "select UserID,CouponID,Point from community_earned_point where CouponID in (" . implode(",", $arrPointId) . ")";
                $arrPoint = $this->objMysql->getRows($sql, "CouponID");
                foreach ($arrCoupon as &$theCoupon) {
                    $cid = $theCoupon["ID"];
                    if (isset($arrPoint[$cid])) {
                        $theCoupon["Point"] = $arrPoint[$cid]["Point"];
                    } else {
                        $theCoupon["Point"] = 0;
                    }
                }
            }
        }

        function fillUserInfo(&$arrCoupon) {
            $arrUserId = array();
            foreach ($arrCoupon as &$theCoupon) {
                $arrUserId[$theCoupon["FromUserID"]] = $theCoupon["FromUserID"];
                $arrUserId[$theCoupon["ToUserID"]] = $theCoupon["ToUserID"];
            }

            if (sizeof($arrUserId)) {
                $sql = "select ID,FirstName,LastName from community_user where ID in (" . implode(",", $arrUserId) . ")";
                $arrUser = $this->objMysql->getRows($sql, "ID");
                foreach ($arrCoupon as &$theCoupon) {
                    $fuid = $theCoupon["FromUserID"];
                    $tuid = $theCoupon["ToUserID"];
                    if (isset($arrUser[$fuid])) {
                        $theCoupon["FromUserFirstName"] = $arrUser[$fuid]["FirstName"];
                        $theCoupon["FromUserLastName"] = $arrUser[$fuid]["LastName"];
                    } else {
                        $theCoupon["FromUserFirstName"] = "";
                        $theCoupon["FromUserLastName"] = "";
                    }
                    if (isset($arrUser[$tuid])) {
                        $theCoupon["ToUserFirstName"] = $arrUser[$tuid]["FirstName"];
                        $theCoupon["ToUserLastName"] = $arrUser[$tuid]["LastName"];
                    } else {
                        $theCoupon["ToUserFirstName"] = "";
                        $theCoupon["ToUserLastName"] = "";
                    }
                }
            }
        }

        function getUserById($userId) {
            $userId = intval($userId);
            $arr = $this->getUserListByLimitStr("", " ID = $userId ");
            return $arr[0];
        }

        function getUserByEmail($mail) {
            $mail = addslashes(trim($mail));
            $arr = $this->getUserListByLimitStr("", " Email = '$mail' and OpenIDType='none' ");
            return $arr[0];
        }

        function addUser($email, $password, $firstname, $lastname, $token) {
            $email = addslashes($email);
            $token = addslashes($token);
            $password = addslashes($password);
            $firstname = addslashes($firstname);
            $lastname = addslashes($lastname);
            $sql = "INSERT INTO community_user (`Email`,`Password`,`FirstName`,`LastName`,`Status`,`InActiveReason` ,`Token`, `AddTime`, `LastChangeTime`) VALUES(" . "'$email','$password', '$firstname', '$lastname','Checkingemail' ,'' ,'$token', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')";
            $qryId = $this->objMysql->query($sql);
            return $this->objMysql->getLastInsertId($qryId);
        }

        function addUserByOpenID($type, $openid, $email="", $password="", $firstname="", $lastname="") {
            $type = addslashes($type);
            $openid = addslashes($openid);
            $email = addslashes($email);
            $password = addslashes($password);
            $firstname = addslashes($firstname);
            $lastname = addslashes($lastname);
            if(!empty($openid)){
	            $sql = "INSERT INTO community_user (`Email`, `Password`, `FirstName`, `LastName`, `Status`, `OpenIDType`, `OpenID`, `AddTime`, `LastChangeTime`) VALUES('$email', '$password', '$firstname', '$lastname', 'Active', '$type', '$openid', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')";
	            $qryId = $this->objMysql->query($sql);            
	            return $this->objMysql->getLastInsertId($qryId);
            }else{
            	return 0;
            }
        }

        function addExchangedPoint($userid, $Point, $ExchangeTypeID, $Remark,$Balance) {
            $userid = intval($userid);
            $Point = intval($Point);
            $ExchangeTypeID = intval($ExchangeTypeID);
            $Remark = addslashes($Remark);

            $sql = "INSERT INTO community_exchanged_point ( `UserID` , `Point` , `ExchangeTypeID` ,`Remark` , `Balance` , `Status` , `AddTime`, `LastChangeTime`) VALUES(" . "'$userid' , '$Point' , '$ExchangeTypeID' , '$Remark' , '$Balance', 'Pending', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')";
            $qryId = $this->objMysql->query($sql);
            return $this->objMysql->getLastInsertId($qryId);
        }

        function addCouponApprovalAccount($userId, $MerchantID, $CategoryID, $Title, $Code, $Remark, $Url, $Tag, $StartTime, $EndTime, $ImgUrl, $Savings, $SavingsType, $DiscountType) {
            $userId = intval($userId);
            $MerchantID = intval($MerchantID);
            $CategoryID = intval($CategoryID);
            $Title = addslashes($Title);
            $Code = addslashes($Code);
            $Remark = addslashes($Remark);
            $Url = addslashes($Url);
            $Tag = addslashes($Tag);
            $StartTime = addslashes($StartTime);
            $EndTime = addslashes($EndTime);
            $ImgUrl = addslashes($ImgUrl);
            $Savings = addslashes($Savings);
            $SavingsType = addslashes($SavingsType);
            $DiscountType = addslashes($DiscountType);

            $sql = "INSERT INTO community_coupon_approval (`CouponID` , `MerchantID` , `CategoryID` , `UserID` , `Title` , `Code` , `Remark` , `Url` , `Tag` , `StartTime` , `EndTime` , `ExpireTime` , `ImgUrl` , `Savings` , `SavingsType` , `Status` , `DiscountType` , `AddTime`, `LastChangeTime`) VALUES('0','$MerchantID','$CategoryID','$userId','$Title','$Code','$Remark','$Url','$Tag','$StartTime','$EndTime','$EndTime','$ImgUrl','$Savings','$SavingsType','Pending','$DiscountType','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
            $qryId = $this->objMysql->query($sql);
            return $this->objMysql->getLastInsertId($qryId);
        }

        function addUserPaypalAccount($userId, $name, $account) {
            $userId = intval($userId);
            $name = addslashes($name);
            $account = addslashes($account);

            $arr = $this->getUserAccountListByLimitStr(" limit 1 ", " `UserID` = $userId and `ExchangeTypeID` = 'Paypal' ");
            if (count($arr) > 0) {
                $sql = "update community_user_account set UserID='$userId' , `ExchangeTypeID` = 'Paypal' , `AccountHolderName` = '$name' , `Account`= '$account', `AddTime`=' " . date("Y-m-d H:i:s") . "', `LastChangeTime`=' " . date("Y-m-d H:i:s") . "' where ID = '" . $arr[0]["ID"] . "'";
                $this->objMysql->query($sql);
                return $arr[0]["ID"];
            } else {
                $sql = "INSERT INTO community_user_account (`UserID`,`ExchangeTypeID`,`AccountHolderName`,`Account`, `AddTime`, `LastChangeTime`) VALUES(" . "'$userId','Paypal','$name', '$account','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
                $qryId = $this->objMysql->query($sql);
                return $this->objMysql->getLastInsertId($qryId);
            }
        }

        function addUserMessage($FromUserID, $ToUserID, $Message) {
            $FromUserID = intval($FromUserID);
            $ToUserID = intval($ToUserID);
            $Message = addslashes($Message);
            $sql = "INSERT INTO community_message (`FromUserID`,`ToUserID`,`Message`, `AddTime` ) VALUES(" . "'$FromUserID','$ToUserID', '$Message','" . date("Y-m-d H:i:s") . "')";
            $qryId = $this->objMysql->query($sql);
            return $this->objMysql->getLastInsertId($qryId);
        }

        function updateCouponApproval($id, $userArray) {
            $arr_update = array();
            foreach ($userArray as $k => $v)
                $arr_update[] = " `" . trim($k) . "` = '" . addslashes(trim($v)) . "' ";
            $sql = "update community_coupon_approval set " . implode(",", $arr_update) . ",`LastChangeTime`='" . date("Y-m-d H:i:s") . "' where ID = '$id'";
            $this->objMysql->query($sql);
            return;
        }

        function updateUser($userId, $userArray) {
            $arr_update = array();
            foreach ($userArray as $k => $v)
                $arr_update[] = " `" . trim($k) . "` = '" . addslashes(trim($v)) . "' ";
            $sql = "update community_user set " . implode(",", $arr_update) . " where ID = '$userId'";
            $this->objMysql->query($sql);
            return;
        }

    }

}
?>