<?php

namespace worker;

class Proxy{
    public $db;
    public $once;              //控制线程是否单独运行 false/true
    public $interval = 10;     //更新代理时间间隔
    public $limit_num = 100;   //获取代理ip数量
    public $time = 600;        //获取代理ip时间限制  600秒以内

    //代理IP API
    public $url = ['http://api.zdaye.com/?api=201606131435452798&gb=4'];

    public function __construct($once = true){
        $this->db = new \PDO('mysql:dbname=proxy;host=127.0.0.1','root','root');
        $this->db->exec("set names utf8");

        $this->once = isset($once) ? $once : true;
        if(!$this->once) $this->run();
    }

    //自动更新代理ip
    public function autoRun(){
        while (true) {
            $proxy = $this->getIpList();
            $this->syncToDB($proxy);
            $this->status();
            sleep($this->interval);
        }
    }

    //单独更新代理ip
    public function run(){
        $proxy = $this->getIpList();
        $this->syncToDB($proxy);
    }

    //获得代理IP
    public function getProxy(){
        $sql = "SELECT proxy FROM proxy WHERE status = 1 AND update_time >=".(time() - $this->time)." ORDER BY valid_counter DESC LIMIT ".$this->limit_num;
        $proxy = $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        return $proxy;
    }

    //代理ip同步到数据库
    public function syncToDB($data){
        if(!$data) die("PROXY DATA IS NULL \r\n");

        $sql = "INSERT INTO proxy(proxy,status,update_time) VALUES(:proxy,:status,:time) ON DUPLICATE KEY UPDATE status=VALUES(status),update_time=VALUES(update_time)";
        $st = $this->db->prepare($sql);

        $status = 1;
        $update_time = time();
        foreach ($data as $proxy) {
            $st->bindParam(':proxy', $proxy);
            $st->bindParam(':status', $status);
            $st->bindParam(':time', $update_time);

            $st->execute();
        }
    }

    //API获取代理ip
    private function getIpList(){
        if(!$this->url) die("PROXY API IS NULL \r\n");

        $data = [];
        foreach ($this->url as $target) {
            $res = file_get_contents($target);
            $proxy = explode("\r\n", $res);
            $data = array_merge($data,$proxy);
        }

        return $data;
    }

    //代理ip池状态
    public function status(){
        $sql = "SELECT COUNT( * ) AS num,`status` FROM proxy WHERE update_time >=".(time() - $this->time)." GROUP BY  `status`";
        $res = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $total = 0;
        echo 'Date : '.date('Y-m-d H:i:s')."\r\n";
        echo "-------Proxy Status-------\r\n";
        foreach ($res as $re) {
            $total += $re['num'];
            if($re['status'] == 1) echo "vaild proxy number : ".$re['num']."\r\n";
            if($re['status'] == 0) echo "unvaild proxy number : ".$re['num']."\r\n";
        }
        echo $this->time."s has ".$total."\r\n";
    }

    //随机取一条代理
    public function getOneProxy(){
        $proxy = $this->getProxy();
        echo $proxy[array_rand($proxy,1)];
    }
}