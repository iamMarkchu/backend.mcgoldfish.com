<?php

/**
 * SEMLib.Cmd.class.php
 *-------------------------
 *
 * Lib command class.
 * This class encapsulate command line which running cron programe in command line.
 * @deprecated File deprecated in Release 2.0.0
 */

if ( !defined("SEMLIB_CMD_CLASS_PHP") ){
   define("SEMLIB_CMD_CLASS_PHP", 1);
   
class Cmd {
	private $strHelp = "";
	private $strOpt = "";
	
	/*
	 * Constructer.
	 * @param Array $arrParameter, array format array('$runScript'=>array(
	 * 											'$parameter1'=>array('cmd'=>command parameter if need, 'h'=>help content),
	 * 											'$parameter2'=>array('h'=>help content),))
	 */
    function Cmd($arrParameter) {
    	if (!is_array($arrParameter) || count($arrParameter) != 1)
    		throw new Exception('Parameter error!\n');
    	
    	$this->parseCmd($arrParameter);
    }
    
    /**
     * show help string.
     */
    public function showHelp() {
    	echo $this->strHelp;
    	exit;
    }
    
    /*
     * get parameters.
     * @return array $opts option parameter
     */
    public function getParamete() {
    	return getopt($this->strOpt);
    }
    
    private function parseCmd($arrParameter) {
    	$strCmd = "";
    	$strHelp = "";
    	$strOpt = "";
    	foreach ($arrParameter as $cmd => $arrParam) {
    		$strCmd .= "{$cmd} ";
    		foreach ($arrParam as $param => $arrParDetail) {
    			if (isset($arrParDetail['cmd'])) {
    				$strCmd .= "[-{$param} {$arrParDetail['cmd']}] ";
    				$strOpt .= "{$param}:";
    			} else {
    				$strCmd .= "[-{$param}] ";
    				$strOpt .= "{$param}";
    			}
    			$strHelp .= "\t-{$param}\t{$arrParDetail['h']}\n";
    		}
    	}
    	
    	$strCmd .= "[-h] ";
    	$strOpt .= "h";
    	$strHelp .= "\t-h\tshow help\n";
    	$this->strHelp = $strCmd."\n".$strHelp;
    	$this->strOpt = $strOpt;
    }
}

}//end define
?>