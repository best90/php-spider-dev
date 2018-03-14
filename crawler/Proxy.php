<?php

namespace crawler;

use resource\core\Core;

class Proxy{
    public $curl;

    public function __construct(){
        $this->curl = new Core();
    }

    //抓取IP代理
    public function get(){
       $this->curl->maxThread = 1;

        $cache = CACHE_PATH . '/proxy/';
        if (! file_exists( $cache )) {
            mkdir($cache);
        }

        $this->curl->add ( array (
                'url' => 'http://ip.zdaye.com/',
                'opt' => array (
                    CURLOPT_HEADER => false,
                    CURLOPT_USERAGENT => userAgent(),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                ),
                'args' => array (
                        'file' => $cache.'index.html'
                )
        ), array($this,'cbProcess'));

        $this->curl->start ();
    }

    public function cbProcess($r, $args) {
        file_put_contents($args['file'], $r['content']);
        sleep(mt_rand(1,10));
        flush();
    }
}