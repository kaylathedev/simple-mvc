<?php

class Config
{

    public static function writeCache($name, $value)
    {
        $data = json_decode(file_get_contents(APP . DS . 'tmp' . DS . 'config-cache.json'));
        $data->$name = $value;
        file_put_contents(APP . DS . 'tmp' . DS . 'config-cache.json', json_encode($data));
    }

    public static function readCache($name, $defaultValue = null)
    {
        $data = json_decode(file_get_contents(APP . DS . 'tmp' . DS . 'config-cache.json'));
        if (isset($data->$name)) {
            return $data->$name;
        }

        return $defaultValue;
    }

    /*
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Registry();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
    }
    */
    
    private static $data = array();
    private static $routes = array();
    
    public static function get($key, $default = null)
    {
        if (isset(Config::$data[$key])) {
            return Config::$data[$key];
        }
        return $default;
    }
    
    public static function set($key, $value)
    {
        Config::$data[$key] = $value;
    }

    public static function route($before, $after)
    {
        Config::$routes[$before] = $after;
    }

    public static function getAllRoutes()
    {
        return Config::$routes;
    }

}
