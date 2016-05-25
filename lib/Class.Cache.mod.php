<?php
if(!defined("__MOD_CLASS_CACHE__"))
{
	define("__MOD_CLASS_CACHE__",1);
	class Cache
	{
		var $cacheDir = "";
		var $cachename = "";
		var $cacheExpSeconds = CACHE_EXP_TIME;
		var $cacheFile = "";
		var $cacheLogFile = "";
		var $memcache = null;

		function Cache($cachename, $cacheDir="./data/", $cacheExpSeconds="")
		{
			if(defined("MEM_CACHE_SERVER_LIST") && defined("DEBUG_MODE") && DEBUG_MODE === false)
			{
				$this->memcache = new Memcache;
				$server_port = defined("MEM_CACHE_PORT") ? MEM_CACHE_PORT : 11211;
				$server_list = explode("|",MEM_CACHE_SERVER_LIST);
				foreach($server_list as $server)
				{
					$server = trim($server);
					if(!$server) continue;
					$this->memcache->addServer($server, $server_port);
				}
			}
		
			if($this->isKeywordCache($cachename)) $cachename = $this->getKeywordCacheName($cachename);
			$this->cachename = $cachename;
			/*
			$this->cacheDir = $cacheDir;
			if($cacheExpSeconds > 0)
			$this->cacheExpSeconds = $cacheExpSeconds;
			$subDir = $cacheDir."cache_".substr($cachename, 0, 2)."/";
			if(!is_dir($subDir))
			{
			mkdir($subDir, 0777, true);
			}
			$this->cacheFile = $subDir."cache_".$cachename.".dat";
			$this->cacheLogFile = $cacheDir."cachelog.txt";
			*/
			$this->cacheLogFile = $cacheDir."cachelog.txt";
		}
		
		function isKeywordCache($cachename)
		{
			return (substr($cachename,2,9) == "_keyword_");
		}
		
		function getKeywordCacheName($cachename)
   		{
   			$cachename = urlencode(strtolower($cachename));
			
			$pattern = '/[^:0-9a-z\\._-]/';
			$replacement = '_';
			return preg_replace($pattern, $replacement, $cachename);
   		}
   		
		function initialCache()
		{
			ob_start();
		}

		function endCache($rtnRes=true)
		{
			$content = ob_get_contents();
			ob_end_clean();
			$modtimestamp = "<!-- last mod time:".date("Y-m-d H:i:s")." -->\n";
			$rtnRes = true;
			if($this->needCache())
			{
				//$tmpCacheFile = $this->cacheFile.".".md5(uniqid(rand(), true));
				//@file_put_contents($tmpCacheFile, $modtimestamp.$content);
				//rename($tmpCacheFile, $this->cacheFile);
				$content_time = time();
				$need_compress = (strlen($content) > 256) ? MEMCACHE_COMPRESSED : false;
				$this->memcache->set($this->cachename . ":" . SID_PREFIX . "c", $content, $need_compress, $this->cacheExpSeconds + 86400) or $rtnRes = false;
				$this->memcache->set($this->cachename . ":" . SID_PREFIX . "t", $content_time, $need_compress, $this->cacheExpSeconds + 86400) or $rtnRes = false;
				// or die ("Failed to save data at the server");
				if($rtnRes) $this->setLog('generate mem cache');
				else $this->setLog('generate mem cache failed');
		   }
		   
		   if($rtnRes) return $content;
		   else return;
		}

		function needCache()
		{
			if(!$this->memcache) return false;
			global $g_ShortHttpHost;
			if(isset($g_ShortHttpHost) && $g_ShortHttpHost == "www") return true;
			return false;
		}
		
		function getCacheTime()
		{
			if(!$this->memcache) return "";
			$res = $this->memcache->get($this->cachename . ":" . SID_PREFIX . "t");
			if($res === false) return "";
			return $res;
		}
		
		function expireCache()
		{
			if($this->getCacheTime())
			{
				$this->memcache->delete($this->cachename . ":" . SID_PREFIX . "c");
				$this->memcache->delete($this->cachename . ":" . SID_PREFIX . "t");
			}
		}
		
	   function getCache()
	   {
			if(!$this->memcache) return "";
			if(isset($_REQUEST["forcerefresh"]) && $_REQUEST["forcerefresh"]) return "";
			$res = $this->memcache->get($this->cachename . ":" . SID_PREFIX . "c");
			if($res === false || empty($res))
			{
				$this->setLog('no mem cache');
				return "";
			}

			/*
			$res = "";
			if(file_exists($this->cacheFile))
			{
				$lastmodtime = date("Y-m-d H:i:s", filemtime($this->cacheFile));
				$cmd = "head -1 ".$this->cacheFile;
				if($fp = popen($cmd, "r"))
				{
					$line = trim(fgets($fp));
					fclose($fp);
					$pattern = "/^<!-- last mod time:(.*?) -->$/i";
					if(preg_match($pattern, $line, $arrMatch))
					{
						$lastmodtime = trim($arrMatch[1]);
					}
				}
				if(((time() - strtotime($lastmodtime)) <  $this->cacheExpSeconds))
				{
					$res = file_get_contents($this->cacheFile);
					$this->setLog('hit cache');
				}
			   else
			   {
				   unlink($this->cacheFile);
				   $this->setLog('remove cache');
			   }
			}
			else
				$this->setLog('no cache');
			*/
			$this->setLog('hit mem cache');
			return $res;
		}

		function setLog($type)
		{
			if(CACHE_FUNC_DEBUG_MODE)
			{
				$logline = date("Y-m-d H:i:s")."\t{$type}\t".$this->cachename."\n";
				error_log($logline, 3, $this->cacheLogFile);
			}
		}
	}
}
?>