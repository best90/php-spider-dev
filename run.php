#!/usr/local/php/bin/php
<?php
require('./init.php');

$param = (isset($argv[1]) && !empty($argv[1])) ? trim($argv[1]) : '';

if(strpos($param, '/')){
    $param = explode('/', $param);
    $task = lcfirst($param[0]);
    $action = lcfirst($param[1]);
    $file = ROOT_PATH .'/run/'.$task.'.class.php';

    if(count($param) > 2){
        $tmp_param = array_slice($param, 2);

        $i = 0;
        while ($i < count($tmp_param)){
            try{
                if(isset($tmp_param[$i+1])){
                    $_GET[trim($tmp_param[$i])] = trim($tmp_param[$i+1]);
                }else{
                    throw new Exception('param \''.$tmp_param[$i].'\' value is missing.');
                }
            }catch (Exception $e) {
                echo $e->getMessage().EOL;
            }

            $i += 2;
        }
    }
}else{
    $task = lcfirst($param);
    $file = ROOT_PATH .'/run/'.$task.'.php';
}

if(file_exists($file)){
	require($file);

    if(isset($action)){
        $task = ucfirst($task);
        $class = new $task();

        try{
            if(method_exists($class, $action)){
                if(isset($_GET['loop'])){
                    $loop = 0;
                    while ($loop < intval($_GET['loop'])){
                        $class->$action();
                        $loop ++;
                    }
                }else{
                    $class->$action();
                }
            }else{
                throw new Exception('action is not found in \''.$task.'\'.');
            }
        }catch (Exception $e){
            echo $e->getMessage().EOL;
        }
    }
}else{
	echo "ERROR TASK : ".lcfirst($task);
}
