<?php
if (!defined('ROOT_PATH'))  die('Hacking attempt');
define('EOL',PHP_EOL);
define('TAB',"\t");
define('BS'," ");

define('CACHE_PATH',ROOT_PATH.'/cache/');
define('DATA_PATH',ROOT_PATH.'/data/');
define('TEMP_PATH',ROOT_PATH."/temp/");
define('SQL_LOG_PATH',TEMP_PATH."/sql/");

define('IS_CGI', strpos(PHP_SAPI, 'cgi') === 0 ? 1 : 0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
define('IS_MAC', strstr(PHP_OS, 'Darwin') ? 1 : 0);
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('NOW_TIME', $_SERVER['REQUEST_TIME']);
define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);