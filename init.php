<?php 
ini_set('memory_limit','2048M');
set_time_limit(0);
date_default_timezone_set('PRC');
//error_reporting(0);
header('Content-Type:text/html;charset=utf-8');
define('ROOT_PATH', str_replace("\\",'/',dirname(__FILE__)));
require_once(ROOT_PATH . '/lib/const.inc.php');

if(file_exists(ROOT_PATH . '/config/config.php')){
	define('CONFIG_PATH',ROOT_PATH . '/config/');
}
$config = require_once(CONFIG_PATH.'/config.php');
$domain_tld = $config['tld'];

require_once(ROOT_PATH . '/lib/common.fun.php');
require_once(ROOT_PATH . '/lib/phpQuery.class.php');
require_once (ROOT_PATH . '/lib/db.class.php');

foreach ($config['db'] as $once => $cfg){
    $db[$once] = new db($once);
}
