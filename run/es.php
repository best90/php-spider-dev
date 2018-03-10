<?php
    class Es{
        protected $db;
        public $hosts;
        protected $client;

        public function __construct(){
            $this->db = new \PDO('mysql:dbname=nuts_tool;host=127.0.0.1', 'root', 'root');
            $this->db->exec("SET NAMES UTF8");

            $this->hosts = [
                'http://127.0.0.1:9200/'
                //'http://192.168.16.71:9200/'
            ];
            $this->client = Elasticsearch\ClientBuilder::create()->setHosts($this->hosts)->build();
        }

        public function sync(){
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $table = isset($_GET['table']) ? trim($_GET['table']) : die("table param is not found.");

            $params = [
                'index' => 'nuts_tool'
            ];

            if(!$this->client->indices()->exists($params)) {
                $params['body'] =  [
                        'settings' => [
                            'number_of_shards' => 2,
                            'number_of_replicas' => 0
                        ]
                    ];

                $this->client->indices()->create($params);
            }

            $res = $this->db->query("SELECT * FROM ".$table." WHERE id > ".$id." limit 500")->fetchAll(PDO::FETCH_ASSOC);
            while (count($res) > 0){
                while (count($res) > 0){
                    $re = array_shift($res);

                    $indexParams = [
                        'index' => 'nuts_tool',
                        'type' => $table,
                        'id' => $re['id'],
                        'body' => $re
                    ];
                    //debug($indexParams);
                    $response = $this->client->index($indexParams);
                    dump($response);
                }

                $id = $re['id'];
                $res = $this->db->query("SELECT * FROM ".$table." WHERE id > ".$id." limit 500")->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        public function search(){
            $type = isset($_GET['type']) ? trim($_GET['type']) : die("type param is not found.");
            $word = isset($_GET['word']) ? trim($_GET['word']) : die("word param is not found.");

            $params = [
                'index' => 'nuts_tool',
                'type' => $type,
                'body' => [
                    'query' => [
                        'prefix' => [
                            'project_name' => $word
                        ]
                    ]
                ]
            ];

            $results = $this->client->search($params);
            dump($results);
        }
    } 