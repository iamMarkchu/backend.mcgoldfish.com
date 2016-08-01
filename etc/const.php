<?php
if(!defined("__CONST__"))
{
	define("__CONST__", 1);

	//define("TIME_ZONE", "Asia/PRC");
	date_default_timezone_set(TIME_ZONE);

	define('INCLUDE_ROOT', dirname(dirname(__FILE__)) . "/");
	define("FRONT_ROOT", "/app/site/savingstory.com/web/");
	define("BACKEND_ROOT", "/app/site/backend.discountsstory.com/web/");
	
	define("BASE_DB_HOST", "localhost");
	define("BASE_DB_USER", "root");
	define("BASE_DB_PASS", "root");
	define("BASE_DB_NAME", "chukui_base");
	define("MYSQL_ENCODING","utf8");
}




