<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('LIB_MAIN')) {
    define('LIB_MAIN', dirname(__FILE__));
}
if (!defined('LIB_MAIN_CLASSES')) {
    define('LIB_MAIN_CLASSES', LIB_MAIN . DS . 'classes');
}
if (!defined('LIB')) {
    define('LIB', dirname(LIB_MAIN));
}
if (!defined('ROOT')) {
    define('ROOT', dirname(LIB));
}

if (isset($_SERVER['DOCUMENT_ROOT'])) {
    $serverRoot = $_SERVER['DOCUMENT_ROOT'];
    if (stripos(ROOT, $serverRoot) === 0) {
        $webRoot = substr(ROOT, strlen($serverRoot));
    } else {
        $webRoot = DS;
    }
} else {
    $serverRoot = ROOT;
    $webRoot = DS;
}

if (!defined('WEB_ROOT')) {
    define('WEB_ROOT', $webRoot);
}

if (!defined('APP')) {
    define('APP', ROOT . DS . 'app');
}

if (!function_exists('getWebUrl')) {

    function getWebUrl($url) {
        return '/' . trim(WEB_ROOT, ' \t\n\r\0\x0B\\/') . '/' . trim($url, ' \t\n\r\0\x0B\\/');
    }

}

if (!function_exists('h')) {

    function h($text) {
        return htmlentities($text);
    }

}

final class WaffleAutoloader {

    public static $autoloadPaths = array(
        'Application'
    );

    public static function findInFolder($rootFolder, $baseName, $recursionLimit = 10) {
        if ($recursionLimit < 1) {
            return false;
        }
        $entryPath = $rootFolder . DS . $baseName;
        if (is_file($entryPath)) {
            return $entryPath;
        }
        $entries = scandir($rootFolder);
        foreach ($entries as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                $entryPath = $rootFolder . DS . $entry;
                if (is_dir($entryPath)) {
                    $result = self::findInFolder($entryPath, $baseName, $recursionLimit - 1);
                    if ($result !== false) {
                        return $result;
                    }
                } else {
                    if ($baseName === $entry) {
                        return $entryPath;
                    }
                }
            }
        }
        return false;
    }

    public static function mainAutoload($className) {
        $result = self::findInFolder(dirname(__FILE__) . DS . 'classes', $className . '.php', 3);
        if ($result !== false) {
            include $result;
            return;
        }
        $result = self::findInFolder(APP, $className . '.php', 3);
        if ($result !== false) {
            include $result;
        }
    }

}

spl_autoload_register('WaffleAutoloader::mainAutoload');

function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler('exception_error_handler');

$app = new Application();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = substr_replace($requestUri, '', 0, strlen(WEB_ROOT));

$app->setUri($requestUri);
$app->load();

