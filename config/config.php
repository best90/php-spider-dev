<?php
if(!defined('ROOT_PATH')) {
	exit();
}else{
	return array(
		'db' => require_once(CONFIG_PATH.'/db.cfg.php'),		
		'memcache' => file_exists(CONFIG_PATH.'/mem.cfg.php') ? require(CONFIG_PATH.'/mem.cfg.php') : '',
		'redis' => file_exists(CONFIG_PATH.'/redis.cfg.php') ? require(CONFIG_PATH.'/redis.cfg.php') : '',
        'tld' => array('com','ad','ae','aero','af','ag','ai','al','am','an','ao','aq','ar','as','asia','at','au','aw','az','ba','bb','bd','be','bf','bg','bh','bi','biz','bj','bm','bn','bo','br','bs','bt','bv','bw','by','bz','ca','cat','cc','cf','cg','ch','ci','ck','cl','cm','cn','co','cq','cr','cu','cv','cx','cy','cz','de','dj','dk','dm','do','dz','ec','edu','ee','eg','eh','es','et','ev','fi','fj','fk','fm','fo','fr','ga','gb','gd','ge','gf','gh','gi','gl','gm','gn','gov','gp','gr','gt','gu','gw','gy','hk','hm','hn','hr','ht','hu','id','ie','il','in','info','int','io','iq','ir','is','it','jm','jo','job','jp','ke','kg','kh','ki','km','kn','kp','kr','kw','ky','kz','la','lb','lc','li','lk','lr','ls','lt','lu','lv','ly','ma','mc','md','me','mg','mh','mil','ml','mm','mn','mo','mobi','mp','mq','mr','ms','mt','mv','mw','mx','my','mz','na','name','nc','ne','net','nf','ng','ni','nl','no','np','nr','nt','nu','nz','om','org','pa','pe','pf','pg','ph','pk','pl','pm','pn','pr','pro','pt','pw','py','qa','re','ro','ru','rw','sa','sc','sd','se','sg','sh','si','sj','sk','sl','sm','sn','so','sr','st','su','sy','sz','tc','td','tf','tg','th','tj','tk','tm','tn','to','tp','tr','travel','tel','tt','tv','tw','tz','ua','ug','uk','us','uy','va','vc','ve','vg','vn','vu','wf','ws','xxx','ye','yu','za','zm','zr','zw'),
	);
}


?>
