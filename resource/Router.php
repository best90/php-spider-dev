<?php
namespace resource;

class Router
{
    private $request = [];
    protected $process;
    protected $task;
    protected $action;
    public $body = [];

    public function __construct()
    {
        $this->request = $_SERVER['argv'];
        $process_name = isset($this->request[0]) ? $this->request[0] : '';
        if (strpos($process_name, '\\')) {
            $path = explode('\\', $process_name);
            $process_name = end($path);
        }
        $this->process = trim($process_name);

        $params = isset($this->request[1]) && !empty($this->request[1]) ? trim($this->request[1]) : '';
        $params = trim($params, '/');

        $config = $this->loadConfig();
        $alias = $config[$this->process];
        $params = isset($alias[$params]) ? $alias[$params] : $params;
        $this->parse($params);
    }

    /**
     * 任务别名
     * @return mixed
     */
    protected function loadConfig()
    {
        $config = require(CONFIG_PATH. 'task.php');
        return $config;
    }

    /**
     * 解析路径
     * @param $params
     */
    protected function parse($params)
    {
        if(strpos($params, '/')) {
            $params = explode('/', $params);
            list($task_name, $action_name) = array_slice($params, 0, 2);
            if(count($params) > 2){
                $tmp_param = array_slice($params, 2);

                $i = 0;
                while ($i < count($tmp_param)) {
                    if(isset($tmp_param[$i+1])){
                        $this->body[trim($tmp_param[$i])] = trim($tmp_param[$i+1]);
                    }else{
                        exit('The param \''.$tmp_param[$i].'\' value is missing.');
                    }
                    $i += 2;
                }
            }
            $this->task = lcfirst(trim($task_name));
            if (empty($action_name)) exit('The action must be need.');
            $this->action = lcfirst(trim($action_name));
        } else {
            if (empty($params)) exit('The task must be need.');
            $this->task = lcfirst($params);
            $this->action = 'run';
        }
    }

    /**
     * 获取任务信息
     * @return array
     */
    public function getTaskProcess()
    {
        return [
            'process' => $this->getProcess(),
            'task' => $this->getTask(),
            'action' => $this->getAction(),
            'body' => $this->body
        ];
    }

    /**
     * 获取运行的脚本
     * @return mixed|string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * 获取任务名
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * 获取任务动作
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}