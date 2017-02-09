<?php
function db($once){
    return DB::instance($once);
}

/**
 * 转化文本为UTF-8格式
 * @param   string  $text   要转化的字符串
 * @return  string
*/
function toUtf8($text,$ignore = false){
    if(is_array($text)){
        foreach($text as $k=>$v){
            $text[$k] = toUtf8($text[$k],$ignore);
        }
        return $text;
    }else{
        $charset = mb_detect_encoding($text,array('UTF-8','ASCII','EUC-CN','CP936','BIG-5','GB2312','GBK'));
        if ($charset != 'UTF-8' && !empty($charset)){
            @$text = mb_convert_encoding($text, "UTF-8", $charset);
        }else{
            @$text = mb_convert_encoding($text, "UTF-8", 'auto');
        }
        
        if($ignore) $text = iconv('UTF-8','UTF-8//IGNORE',$text);
        return preg_replace ( '/(<meta\s+.+?content=".+?charset=)(.+?)("\s?\/?\s*>)/i', "\\1UTF-8\\3", $text, 1 );
    }
}

//读取文件
function read($file){
    if($fp=@fopen($file,"rb")){
        clearstatcache();
        $filesize=filesize($file);
        if($filesize>0){
            $data=fread($fp,$filesize);
        }else{
            $data=false;
        }
        fclose($fp);
        return $data;
    }else{
        return false;
    }
}

//截取字符串
function cutStr($string,$start,$end,$encode = 'utf8'){
    if(empty($string)) return false;
    $s1 = mb_strpos($string,$start,0,$encode)+ mb_strlen($start,$encode);
    if($s1 === false) return false;
    $s2 = mb_strpos($string,$end,$s1,$encode);
    if($s2 === false) return false;
    return trim(mb_substr($string,$s1,$s2-$s1,$encode));
}

//解析URL信息
function parseUrl($url){
    $url = toUtf8($url);
    global $domain_tld;
    if(strpos($url,'://') ==false){
        $url = 'http://'.trim($url);
    }
    
    $info = parse_url($url);

    if(!isset($info['host'])){
        return 'URL不合法';
    }

    $result = array(
        'scheme' => 'http',
        'site' => '',
        'host' => '',
        'prefix' => '',
        'infix' => '',
        'tld' => '',
        'port' => '',
        'is_ip' => '',
        'path' => '',
        'query' => '',
        'fragment' => '',
        'extension' => ''
    );

    $result['scheme'] = isset($info['scheme']) ? $info['scheme']:'http';
    $result['site'] = $info['host'];
    $arr = explode('.',$result['site']);
    $result['is_ip'] = is_numeric(str_replace('.','',$result['site'])) ;
    if(!$result['is_ip']){
        $tld = array_pop($arr);
        if(in_array($tld,$domain_tld) && count($arr)>0){
            if($tld == 'cn'){
                $tld_temp = array_pop($arr);
                if(in_array($tld_temp.'.'.$tld,array('com.cn','net.cn','gov.cn','edu.cn','org.cn')) && count($arr)>0){
                    $tld = $tld_temp.'.'.$tld;
                }else{
                    $arr[] = $tld_temp;
                }
            }
        
            $result['tld'] = $tld;
            $result['host'] = array_pop($arr).'.'.$tld;
            $result['prefix'] = implode('.',$arr);
            $result['infix'] = str_replace('.'.$result['tld'],'',$result['host']);
        }else{
            $result['site'] = '';
        }
    }

    $result['port'] = isset($info['port']) ? $info['port']:'';
    $result['path'] = isset($info['path']) ? $info['path']:'';
    $result['query'] = isset($info['query']) ? $info['query']:'';
    $result['fragment'] = isset($info['fragment']) ? $info['fragment']:'';

    if(isset($info['path']) && !empty($info['path'])){
        $path = pathinfo($result['path']);
        @$result['extension'] = $path['extension'];
    }
    
    foreach ($result as $key => $value) {
        if($value == ''){
            unset($result[$key]);
        }
    }
    return $result;
}

function userAgent(){
    $userAgent = array(
        0 => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.11 TaoBrowser/2.0 Safari/536.11',
        1 => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.71 Safari/537.1 LBBROWSER',
        2 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; LBBROWSER)',
        3 => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E; LBBROWSER)',
        4 => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 LBBROWSER',
        5 => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)',
        6 => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        7 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        8 => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 SE 2.X MetaSr 1.0',
        9 => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SV1; QQDownload 732; .NET4.0C; .NET4.0E; SE 2.X MetaSr 1.0)',
        10 => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:16.0) Gecko/20121026 Firefox/16.0',
        11 => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0',
        12 => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b13pre) Gecko/20110307 Firefox/4.0b13pre',
        13 => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
        14 => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
        15 => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
        16 => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16',
        17 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)',
        18 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
        19 => 'Mozilla/5.0 (X11; U; Linux x86_64; zh-CN; rv:1.9.2.10) Gecko/20100922 Ubuntu/10.10 (maverick) Firefox/3.6.10',
        20 => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11',
        21 => 'Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
        22 => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; CIBA; .NET CLR 2.0.50727)',
        23 => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11',
        24 => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)',
        25 => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.1)',
        26 => 'Mozilla/4.0 (compatible; GoogleToolbar 5.0.2124.2070; Windows 6.0; MSIE 8.0.6001.18241)',
        27 => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; EasyBits GO v1.0; InfoPath.1; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
        28 => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)',
        29 => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)',
        30 => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)',
        31 => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)',
        32 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.57.2',
        33 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/601.5.17',
        34 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8) AppleWebKit/536.25 (KHTML, like Gecko) Version/6.0 Safari/536.25',
    );
        
    return $userAgent[array_rand($userAgent,1)];
}


/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为true 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @return void|string
 */
function dump($var, $echo = true, $label = null){
    $label = (null === $label) ? '' : rtrim($label) . ':';
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
    if (IS_CLI) {
        $output = PHP_EOL . $label . $output . PHP_EOL;
    } else {
        if (!extension_loaded('xdebug')) {
            $output = htmlspecialchars($output, ENT_QUOTES);
        }
        $output = '<pre>' . $label . $output . '</pre>';
    }
    if ($echo) {
        echo ($output);
        return null;
    } else {
        return $output;
    }
}

function debug($var){
    dump($var);
    die();
}