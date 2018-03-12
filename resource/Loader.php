<?php

class Loader
{
    public static $vendor_map = [
        'crawler' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'crawler',
        'resource' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'resource',
        'worker' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'worker'
    ];

    public static function autoload($class)
    {
        $file = self::findClassFile($class);
        if (file_exists($file)) {
            static::includeFile($file);
        }
    }

    private static function findClassFile($class)
    {
        $vendor = substr($class, 0, strpos($class, '\\'));
        $vendor_dir = static::$vendor_map[$vendor];
        $file_path = substr($class, strlen($vendor)) . '.php';
        return strtr($vendor_dir . $file_path, '\\', DIRECTORY_SEPARATOR);
    }

    private static function includeFile($file)
    {
        if (is_file($file)) {
            require $file;
        }
    }
}