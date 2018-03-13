<?php 
ini_set('memory_limit','2048M');
set_time_limit(0);
date_default_timezone_set('PRC');
//error_reporting(0);
header('Content-Type:text/html;charset=utf-8');
define('ROOT_PATH', str_replace("\\",'/',dirname(__FILE__)));
define('RESOURCE_PATH', ROOT_PATH.'/resource/');
require(RESOURCE_PATH . 'const.php');

if(file_exists(ROOT_PATH . '/config/config.php')){
	define('CONFIG_PATH',ROOT_PATH . '/config/');
}
$config = require(CONFIG_PATH.'config.php');
$domain_tld = $config['tld'];

require(RESOURCE_PATH . 'common.php');
require(RESOURCE_PATH . 'phpQuery.php');
require('vendor/autoload.php');

require(RESOURCE_PATH . 'Loader.php');
spl_autoload_register('Loader::autoload');

foreach ($config['db'] as $once => $cfg){
    $db[$once] = new medoo($once);
}
