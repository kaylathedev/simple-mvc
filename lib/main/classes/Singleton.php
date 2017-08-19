<?php

if (!function_exists('get_called_class')) {
    function get_called_class()
    {
        $bt = debug_backtrace();
        $l = 0;
        do {
            $l++;
            $lines = file($bt[$l]['file']);
            $callerLine = $lines[$bt[$l]['line']-1];
            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', $callerLine, $matches);
        } while ($matches[1] === 'parent' && $matches[1]);

        return $matches[1];
    }
}

abstract class Singleton implements LazyLoading
{

    public static function createEmpty()
    {
        return self::getInstance();
    }

    private function __construct()
    {
    }

    private static $instances = array();

    final public static function getInstance()
    {
        $calledClass = get_called_class();
        if (!isset(self::$instances[$calledClass])) {
            self::$instances[$calledClass] = new $calledClass();
        }

        return self::$instances[$calledClass];
        
    }

}
