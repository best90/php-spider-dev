<?php
namespace resource;

class Client
{
    protected $process;

    public function run()
    {
        $router = new Router();
        $task = ucfirst($router->getTask());
        $action = $router->getAction();
        $this->process = $router->getProcess();
        $namespace = $this->process == 'work' ? 'worker' : 'crawler';
        $class = $namespace.'\\'.$task;
        $class = new $class();

        try{
            if(method_exists($class, $action)){
                if ($this->process == 'crawl') {
                    $dir_name = strpos($task, '_') ? end(explode('_', $task)) : lcfirst($task);

                    $cache_dir = CACHE_PATH.$dir_name.'/';
                    $cache = $cache_dir.$action.'/';
                    if(! file_exists( $cache_dir)) mkdir($cache_dir);
                    if(! file_exists( $cache )) mkdir($cache);
                    if(method_exists($class, 'setCache')) $class->setCache($cache);
                }

                if(isset($router->body['loop'])){
                    $loop = 0;
                    while ($loop < intval($router->body['loop'])){
                        $class->$action();
                        $loop ++;
                    }
                }else{
                    $class->$action();
                }
            }else{
                exit('action is not found in \''.$task.'\'.');
            }
        }catch (\Exception $e){
            echo $e->getMessage().EOL;
        }
    }

    public function crawlHandler()
    {

    }

    public function workHandler()
    {

    }
}