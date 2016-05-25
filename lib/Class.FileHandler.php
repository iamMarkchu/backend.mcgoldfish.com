<?php
class FileHandler
{
	// local file system method
	// rename,mkdir,rmdir,chmod,generateTempDir

    function rename($_file_1,$_file_2)
	{
    	return @rename($_file_1,$_file_2);
	}

	function mkdir($_dir,$_mod=0755)
	{
		return @mkdir($_dir,$_mod);
	}

	function rmdir($_dir)
	{
		return @rmdir($_dir);
	}

	function chmod($_dir, $_mod=0755)
	{
		return @chmod($_dir, $_mod);
	}

	function is_writeable($_file)
	{
		return is_writeable($_file);
	}
	
	function checkmkdir($_dir,$_mod=0775)
	{
		if(!is_dir($_dir)) {mkdir($_dir);chmod($_dir,$_mod);}
		return is_writeable($_dir);
	}
	
	function mv($_file_1,$_file_2)
	{
		if (empty($_file_1) || !$this->is_file($_file_1) || !$this->file_exists($_file_1)) 
			return false;
		
		$targetPath = dirname($_file_2);
		if(!$this->is_dir($targetPath)) {
			$this->mkdir($targetPath);
		}
		
		return @rename($_file_1,$_file_2);
	}

	function generateUnique()
	{
		$sDate = date("Ymd");
		$sTime = date("His");
		$sUSec = $this->getUSec();
		srand();
		$sRand = rand(1000,9999);
		return md5($sDate . $sTime . $sUSec . $sRand);
	}

	function scandir_ex($_dir,$_pattern="",$_order_by="")
	{
		$arr_filename = $this->scandir($_dir);
		if(empty($arr_filename)) return array();
		if($_pattern)
		{
			foreach($arr_filename as $k => $filename)
			{
				if(! preg_match($_pattern,$filename)) unset($arr_filename[$k]);
			}
		}
		
		$arr_return = $this->fill_file_stat($_dir,$arr_filename);
		if($_order_by) $this->sortFileList($arr_return,$_order_by);
		return $arr_return;
	}
	
	function fill_file_stat($_dir,$_list)
	{
		$arr_return = array();
		foreach($_list as $k => $filename)
		{
			$filepath = $_dir . DIRECTORY_SEPARATOR . $filename;
			$arr_statinfo = stat($filepath);
			if($arr_statinfo === false) continue;
			$arr_statinfo["FileName"] = $filename;
			$arr_statinfo["FilePath"] = $filepath;
			$arr_statinfo["FileSize"] = filesize($filepath);
			$arr_statinfo["CreateDate"] = date("Y-m-d H:i:s",$arr_statinfo["ctime"]);
			$arr_statinfo["LastUpdateDate"] = date("Y-m-d H:i:s",$arr_statinfo["mtime"]);
			$arr_return[$k] = $arr_statinfo;
		}
		return $arr_return;
	}
	
	function generateTempDir($_startDir="",$_retry=0)
	{
		//note: Temp Dir must be located at __TEMPORARY_PATH
		//ex. /....../RootPath/tmp/20070716/142354.29878.3875
		if($_startDir == "") $_startDir = __TEMPORARY_PATH;
		$sDate = date("Ymd");
		$sTime = date("His");
		$sUSec = $this->getUSec();
		srand();
		$sRand = rand(0,9999);
		$sTmpPath = $_startDir . DIRECTORY_SEPARATOR . $sDate;
		if(!is_dir($_startDir) || !is_writeable($_startDir)) return false;

		if(!is_dir($sTmpPath))
		{
			@mkdir($sTmpPath,0777);
		}

		$sTmpPath .= DIRECTORY_SEPARATOR . "$sTime" . "." . $sUSec . "." . $sRand;
		if(is_dir($sTmpPath))
		{
			if($_retry == 2) return false;
			return $this->generateTempDir($_startDir,++$_retry);
		}

		if(@mkdir($sTmpPath,0777))
		{
			//succ
			@chmod($sTmpPath,0777);
			return $sTmpPath;
		}

		if($_retry == 2) return false;
		return $this->generateTempDir($_startDir,++$_retry);
	}

	function clearTempDir($_dir)
	{
		if($this->is_dir($_dir))
		{
			$dirname = basename($_dir);
			if(preg_match("/^[0-9]{6}\.[0-9]+\.[0-9]{4}$/",$dirname))
			{
				@exec("rm -rf $_dir");
			}
		}
	}

	function unzip($_file,$_type)
	{
		if(!$this->file_exists($_file)) return false;
		$filename = basename($_file);
		$filenameesc = escapeshellarg(basename($_file));
		$dir = dirname($_file);
		if(!is_writeable($dir)) return false;
		if(!chdir($dir)) return false;
		$cmd = "";
		switch($_type)
		{
			case "TGZ":
				$cmd = "tar zxf " . $filenameesc;
				break;
			case "TAR":
				$cmd = "tar xf " . $filenameesc;
				break;
			case "ZIP":
				$cmd = "unzip " . $filenameesc;
				break;
			case "GZ":
				$origFilename = $filename;
				if($pos = strrpos($origFilename,".")) 
					$origFilename = substr($origFilename,0,$pos);
				$origFilename = escapeshellarg($origFilename);
				$cmd = "gunzip -c " . $filenameesc . " > $origFilename";
				break;
		}
		if($cmd == "") return false;
		return exec($cmd);
	}

	function zip($_file)
	{
		if(!$this->file_exists($_file)) return false;
		$dir = dirname($_file);
		$filename = basename($_file);
		if(!chdir($dir)) return false;
		if(!$this->is_writeable($dir)) return false;
		$newfile = $_file . ".zip";
		$cmd = "zip $newfile $filename";
		exec($cmd);
		if(!$this->file_exists($newfile)) return false;
		return $newfile;
	}

	function disk_free_space($_dir)
	{
		return disk_free_space($_dir);
	}

	function disk_total_space($_dir)
	{
		return disk_total_space($_dir);
	}

	function is_file($_file)
	{
		return @is_file($_file);
	}

	//local file system and remote method
	// copy,unlink,filesize,file_exists
	
	function getAutoName($_file,$_retry=0)
	{
		if($_retry == 0) $detectFile = $_file;
		else
		{
			if($pos = strrpos($_file,"."))
			{
				$detectFile = substr($_file,0,$pos) . "_" . $_retry . substr($_file,$pos);
			}
			else $detectFile = $_file . "_" . $_retry;
			
		}
		if($this->file_exists($detectFile)) return $this->getAutoName($_file,(int)($_retry + 1));
		else return $detectFile;
	}

	function copy($_file_1,$_file_2,$_overwrite=0)
	{
		if($_overwrite == 2)
		{
			//auto rename
			$_file_2 = $this->getAutoName($_file_2);
			$_overwrite = 0;
		}
		else if($this->file_exists($_file_2))
		{
			if($_overwrite == 1) $this->unlink($_file_2);
			else return false;
		}
		$res = @copy($_file_1,$_file_2);
		if($res == true) return $_file_2;
		else return false;
	}

	function unlink($_file)
	{
		return @unlink($_file);
	}

	function filesize($_file)
	{
		switch($this->getUriType($_file))
		{
			case "FTP":
				return $this->getFtpFileSize($_file);
				break;
			default:
				return @filesize($_file);
		}
	}

	function is_dir($_dir)
	{
		switch($this->getUriType($_dir))
		{
			case "FTP":
				return $this->isFtpDir($_dir);
				break;
			default:
				return @is_dir($_dir);
		}
	}

	function file_exists($_file)
	{
		switch($this->getUriType($_file))
		{
			case "FTP":
				$filesize = $this->filesize($_file);
				if($filesize === false) return false;
				return true;
				break;
			default:
				return @file_exists($_file);
		}
	}


	function scandir($_dir)
	{
		switch($this->getUriType($_dir))
		{
			case "FTP":
				return $this->getFtpFileList($_dir);
				break;
			default:
				if(!is_dir($_dir)) return array();
				$dh = opendir($_dir);
				if(!$dh) return array();

				$arrRet = array();
				while(false !== ($filename = readdir($dh)))
				{
					if($filename == "." || $filename == "..") continue;
					if(is_file($_dir . DIRECTORY_SEPARATOR . $filename))
					{
						$arrRet[] = $filename;
					}
				}
				closedir($dh);
				return $arrRet;
		}
	}

	//Ftp

	function getFtpConnection(&$_arr)
	{
		$conn_id = ftp_connect($_arr["Host"]); 
		$login_result = ftp_login($conn_id, $_arr["User"], $_arr["Password"]);
		if ((!$conn_id) || (!$login_result)) return false;
		if(!ftp_pasv($conn_id,true)) ftp_pasv($conn_id,false);
		return $conn_id;
	}
	
	function isFtpDir($_dir)
	{
		$arrParseResult = $this->parseUri($_dir,"FTP");
		if(empty($arrParseResult)) return false;
		$conn_id = $this->getFtpConnection($arrParseResult);
		if (!$conn_id) return false;

		$res = @ftp_chdir($conn_id, $arrParseResult["Path"]);
		ftp_close($conn_id);
		return $res;
//		$res = ftp_size($conn_id, $arrParseResult["Path"]);
//		ftp_close($conn_id);
//		if ($res == -1) return true;
//		return false;
	}

	function getFtpFileList($_dir)
	{
		$arrParseResult = $this->parseUri($_dir,"FTP");
		if(empty($arrParseResult)) return false;
		$conn_id = $this->getFtpConnection($arrParseResult);
		if (!$conn_id) return false;
		
		//if(! @ftp_chdir($conn_id, $arrParseResult["Path"])) return false;
		$res = ftp_nlist($conn_id, $arrParseResult["Path"]);
		//$res = ftp_nlist($conn_id, "../");
		foreach($res as $k => $v)
		{
			if(ftp_size($conn_id,$v) == -1)
			{
				//dir
				unset($res[$k]);
			}
			else $res[$k] = basename($v);
		}
    	ftp_close($conn_id);
		return $res;
	}

	function getFtpLineInfo(&$_line)
	{
		$regex = "/^([^ ]+) +[^ ]+ +[^ ]+ +[^ ]+ +([^ ]+) +([^ ]+ +[^ ]+ +[^ ]+) +(.+)$/";
		if(!preg_match($regex,$_line,$items)) return array();

		$itemType = "";
		switch(substr($items[1],0,1))
		{
			case "d":
				$itemType = "directory";
				break;
			case "l":
				$itemType = "link";
				break;
			case "-":
				$itemType = "file";
				break;
			case "+":	// it's something on an anonftp server
				break;
		}
		if($itemType == "") return array();
		
		return array(
			"Type" => $itemType,
			"FileSize" => $items[2],	//Byte
			"LastUpdateDate" => date("Y-m-d H:i:s",strtotime($items[3])),
			"FileName" => $items[4],
			"Attributes" => $items[1],
		);
	}
	
	function cmpFtpFileInfo(&$arr_1,&$arr_2)
	{
		if($this->CmpFileInfoDirection == "asc") return strcmp($arr_1[$this->CmpFileInfoFieldName],$arr_2[$this->CmpFileInfoFieldName]);
		else return strcmp($arr_2[$this->CmpFileInfoFieldName],$arr_1[$this->CmpFileInfoFieldName]);
	}
	
	function sortFileList(&$_list,$_sortby)
	{
		$arr_def_1 = array("time");
		$arr_def_2 = array("asc","desc");
		$_sortby = strtolower($_sortby);
		@list($tp,$di) = explode("_",$_sortby);
		if(!isset($di) || !$di) $di = "asc";
		switch($_sortby)
		{
			case "time_asc":
				$this->CmpFileInfoFieldName = "LastUpdateDate";
				$this->CmpFileInfoDirection = "asc";
				break;
			case "time_desc":
				$this->CmpFileInfoFieldName = "LastUpdateDate";
				$this->CmpFileInfoDirection = "desc";
			case "name_asc":
				$this->CmpFileInfoFieldName = "FileName";
				$this->CmpFileInfoDirection = "asc";
				break;
			case "name_desc":
				$this->CmpFileInfoFieldName = "FileName";
				$this->CmpFileInfoDirection = "desc";
				break;
			default:
				mydie("die: wrong sortby: $_sortby");
		}
		uasort($_list,array($this,"cmpFtpFileInfo"));
	}
	
	function getFtpRawList($_dir,$_type="",$_pattern="",$_sortby="")
	{
		$arrParseResult = $this->parseUri($_dir,"FTP");
		if(empty($arrParseResult)) return false;
		$conn_id = $this->getFtpConnection($arrParseResult);
		if (!$conn_id) return false;
		if(! @ftp_chdir($conn_id, $arrParseResult["Path"])) return false;
		//note: 
		///if(ftp_systype($conn_id) != "UNIX") return false;
		
		@$contents = ftp_rawlist($conn_id,"");
		if(!$contents) return false;
		foreach($contents as $line)
		{
			$info = $this->getFtpLineInfo($line);
			if(!isset($info["Type"])) continue;
			if($_pattern && !preg_match($_pattern,$info["FileName"])) continue;
			$arrRet[$info["Type"]][] = $info;
		}
    	ftp_close($conn_id);
		
		if($_sortby)
		{
			foreach($arrRet as $_tp => &$_list_tp)
			{
				$this->sortFileList($_list_tp,$_sortby);
			}
		}

		if($_type != "")
		{
			if(isset($arrRet[$_type])) return $arrRet[$_type];
			else return array();
		}
		return $arrRet;
	}

	// getFtpFileSize
	function getFtpFileSize($_file)
	{
		$arrParseResult = $this->parseUri($_file,"FTP");
		if(empty($arrParseResult)) return false;
		$conn_id = $this->getFtpConnection($arrParseResult);
		if (!$conn_id) return false;
		$res = ftp_size($conn_id, $arrParseResult["Path"]);
		ftp_close($conn_id);
		if ($res == -1) return false;
		return $res;
	}

	function saveFtpFile($_file,$_local_file)
	{
		$arrParseResult = $this->parseUri($_file,"FTP");
		if(empty($arrParseResult)) return false;
		$conn_id = $this->getFtpConnection($arrParseResult);
		if (!$conn_id) return false;
		if(! @ftp_chdir($conn_id, $arrParseResult["dirname"])) return false;
		$res = @ftp_get($conn_id,$_local_file,$arrParseResult["basename"], FTP_BINARY);
		ftp_close($conn_id);
		return $res;
	}

	
	//others:
	function getDirById($_rootPath,$_id)
	{
		//ex. $_id = 3005, $_rootPath = /data ; return: /data/3
		return($_rootPath . DIRECTORY_SEPARATOR . floor($_id / 1000));
	}

	function getUriType($_uri)
	{
		$pos = strpos($_uri,"://");
		if($pos === false) return "FILE";
		return(ltrim(strtoupper(substr($_uri,0,$pos))));
	}
	

	function parseUri($_uri,$_uriType)
	{
		$arrRet = array(
			"User" => "",
			"Password" => "",
			"Host" => "",
			"Path" => "",
			"Header" => "",
			"dirname" => "",
			"basename" => "",
		);
		switch($_uriType)
		{
			case "FTP":
				//ex. ftp://user:pass@www.test.com/test/aaa.txt
				if(preg_match("/^ftp:\/\/([^\/:]+):([^\/:]+)@([^\/:]+)(.*)/i",$_uri, $matches))
				{
					$arrRet = array(
						"User" => $matches[1],
						"Password" => $matches[2],
						"Host" => $matches[3],
						"Path" => $matches[4],
						"Header" => "ftp://" . $matches[1] . ":" . $matches[2] . "@" . $matches[3],
					);

					if($arrRet["Path"])
					{
						$path_parts = pathinfo($arrRet["Path"]);
						foreach($path_parts as $k => $v) $arrRet[$k] = $v;
					}
				}
				break;
		}
		
		return $arrRet;
	}


	function getZipFileType($_file,$_method="")
	{
		if($_method == "")		//by extension
		{
			$arrValidZipType = array(
				"TGZ" => array(".tgz",".tar.gz"),
				"TAR" => array(".tar"),
				"ZIP" => array(".zip"),
				"GZ" => array(".gz"),
			);

			foreach($arrValidZipType as $type => $arrExt)
			{
				foreach($arrExt as $ext)
				{
					$lenExt = strlen($ext);
					if(strtolower(substr($_file,0 - $lenExt)) == $ext)
					{
						if(strlen($_file) - strlen($ext) > 0) return $type;
					}
				}
			}
		}
		else if($_method == "ByHeader")
		{
			//toDo
		}
		return "";
	}

	function getMicroTime()
	{
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec);
    }

	function getUSec()
	{
		list($usec, $sec) = explode(" ",microtime());
		return (substr($usec,2));
    }

	function parseFileName($_file)
	{
		/*
		$path_parts["dirname"] . "\n";
		echo $path_parts["basename"] . "\n";
		echo $path_parts["extension"] . "\
		*/
		$path_parts = pathinfo($_file);
		if($path_parts["extension"] != "")
		{
			$path_parts["prefix"] = substr($path_parts["basename"], 0, -1 - strlen($path_parts["extension"]));
		}
		else
		{
			$path_parts["prefix"] = $path_parts["basename"];
		}
		
		//$path_parts["prefix"] = rtrim($path_parts["prefix"],".");
		return $path_parts;
	}
	
	function stripLastDirectorySeparator(&$_dir)
	{
		return rtrim($_dir,"\\/");
	}

}//end class
?>