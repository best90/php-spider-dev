<?php

namespace crawler;

use resource\core\Core;
use resource\core\Base;

class Crawler{
    public $curl;
    public $cache;

    public function __construct(){
        $this->curl = new Core();
        $this->curl->cbInfo = array (
                new Base (),
                'cbCurlInfo'
        );

        if(method_exists($this,'_init')) $this->_init();
    }

    public function setCache($cache){
        $this->cache = $cache;
    }
}