<?php

namespace resource;

class Debug
{
    const IS_STOP = true;

    /**
     * 是否ajax请求
     * @return bool
     */
    private static function isAjax()
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || isset($_GET['_isAjax']) || isset($_POST['_isAjax']);
        return $isAjax ? true : false;
    }

    /**
     * 调试基础版
     * @param $data
     */
    public static function basic($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * 调试进阶版
     * @param $data
     * @param $isStop
     */
    public static function advance($data, $isStop = self::IS_STOP)
    {
        $trace = (new \Exception())->getTrace()[0];
        echo '<br>文件号：'.$trace['file'].':'.$trace['line'];
        self::basic($data);
        self::stop($isStop);
    }

    /**
     * 调试高级版
     * @param $data
     * @param $isStop
     */
    public static function senior($data, $isStop = self::IS_STOP) {
        if (self::isAjax()) {
            self::ajax($data, $isStop);
        }else{
            self::advance($data, $isStop);
        }
    }

    /**
     * ajax调试
     * @param $data
     * @param $isStop
     */
    public static function ajax($data, $isStop = self::IS_STOP)
    {
        $trace = (new \Exception())->getTrace()[0];
        header('Content-type:application/json;charset=utf-8');
        $json = json_encode([
            'file' => $trace['file'],
            'line' => $trace['line'],
            'dataString' => var_export($data, true),
            'data' => $data
        ]);
        echo $json;
        self::stop($isStop);
    }

    /**
     * 断点
     * @param $isStop
     */
    private static function stop($isStop = self::IS_STOP)
    {
        $isStop && exit;
    }
}