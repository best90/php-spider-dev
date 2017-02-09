<?php
    use Ares333\CurlMulti\Core;

    class Spider_site{
        public $curl;

        public function __construct(){
            $this->curl = new Core();
        }

        public function alexa(){
            $db=new PDO('mysql:dbname=site;host=127.0.0.1','root','root');
            $db->exec("set names utf8");

            $this->curl->maxThread = 1;

            $cache = CACHE_PATH . '/site/alexa/';
            if (! file_exists( $cache )) {
                mkdir($cache);
            }

            $id = 0;
            $res = $db->query("SELECT id,url FROM site WHERE id>".$id." ORDER BY id ASC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);

            while(count($res) > 0) {
                while(count($res) > 0) {
                    $ip = mt_rand(101,120).'.'.mt_rand(50,250).'.'.mt_rand(10,250).'.'.mt_rand(10,250);
                    $re = array_shift($res);

                    if(!$this->filterUrl($re['url'])) continue;
                    $info = parseUrl($re['url']);
                    if(!isset($info['host'])) continue;

                    $this->curl->add ( array (
                            'url' => 'http://alexa.chinaz.com/default.aspx?domain='.$info['host'].'&upd=false',
                            'opt' => array (
                                CURLOPT_HEADER => false,
                                CURLOPT_HTTPHEADER => array('X-FORWARDED-FOR:'.$ip.'','CLIENT-IP:'.$ip.''),
                                CURLOPT_REFERER => 'http://alexa.chinaz.com/',
                                CURLOPT_USERAGENT => userAgent(),
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_FOLLOWLOCATION => 0, 
                                CURLOPT_SSL_VERIFYPEER => false,
                            ),
                            'args' => array (
                                    'file' => $cache.$re['id'].'.html'
                            )
                    ), array($this,'cbProcess'));
                    
                }

                $this->curl->start ();

                $id = $re['id'];
                $res = $db->query("SELECT id,url FROM site WHERE id>".$id." ORDER BY id ASC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
                sleep(mt_rand(1,5));
            }
        }

        public function whois(){
            $db=new PDO('mysql:dbname=site;host=127.0.0.1','root','root');
            $db->exec("set names utf8");

            $this->curl->maxThread = 1;

            $cache = CACHE_PATH . '/site/whois/';
            if (! file_exists( $cache )) {
                mkdir($cache);
            }

            $id = 0;
            $res = $db->query("SELECT id,url FROM site WHERE id>".$id." ORDER BY id ASC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);

            while(count($res) > 0) {
                while(count($res) > 0) {
                    $ip = mt_rand(101,120).'.'.mt_rand(50,250).'.'.mt_rand(10,250).'.'.mt_rand(10,250);
                    $re = array_shift($res);

                    if(!$this->filterUrl($re['url'])) continue;
                    $info = parseUrl($re['url']);
                    if(!isset($info['host'])) continue;

                    $this->curl->add ( array (
                            'url' => 'http://whois.chinaz.com/?DomainName='.$info['host'],
                            'opt' => array (
                                CURLOPT_HEADER => false,
                                CURLOPT_HTTPHEADER => array('X-FORWARDED-FOR:'.$ip.'','CLIENT-IP:'.$ip.''),
                                CURLOPT_REFERER => 'http://whois.chinaz.com/',
                                CURLOPT_USERAGENT => userAgent(),
                                //CURLOPT_PROXY => 'http://120.87.149.143:8088',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_FOLLOWLOCATION => 1, 
                                CURLOPT_SSL_VERIFYPEER => false,
                            ),
                            'args' => array (
                                    'ip' => $ip,
                                    'file' => $cache.$re['id'].'.html'
                            )
                    ), array($this,'cbProcess'));
                    
                }

                $this->curl->start ();

                $id = $re['id'];
                $res = $db->query("SELECT id,url FROM site WHERE id>".$id." ORDER BY id ASC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
                sleep(mt_rand(1,5));
            }
        }

        public function cbProcess($r, $args) {
            echo $args['ip'];
            file_put_contents($args['file'], $r['content']);
            echo 'crawl '.$args ['file'] . " ok\n";
            sleep(mt_rand(5,20));
            flush();
        }

        //过滤URL
        private function filterUrl($url){
            if(empty($url)) return false;
            if(strpos($url, 'weibo.com/u') !== false || strpos($url, '无') !== false || strpos($url, 't.qq.com') !== false) return false;
            if(strpos($url, 'app.weibo.com') !== false || strpos($url, 'lagou.com/gongsi') !== false || strpos($url, 'mp.weixin.qq.com') !== false) return false;
            if(strpos($url, 'buluo.qq.com') !== false || strpos($url, 'baike.baidu.com') !== false || strpos($url, 'baike.haosou.com') !== false) return false;
            if(strpos($url, 'baike.so.com') !== false || strpos($url, 'baike.sogou.com') !== false || strpos($url, '@') !== false) return false;
            if(strpos($url, 'taobao.com') !== false || strpos($url, 'tmall.com') !== false) return false;
            if(strpos($url, 'app.qq.com') !== false || strpos($url, 'tieba.baidu.com') !== false || strpos($url, 'pan.baidu.com') !== false) return false;

            return true;
        }
    }