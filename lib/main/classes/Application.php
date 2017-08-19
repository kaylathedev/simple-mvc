<?php

function lazy_call_method($obj, $methodName, $parameters) {
    $reflected = new ReflectionMethod($obj, $methodName);
    $methodParameters = $reflected->getParameters();
    $i = count($parameters);
    $count = count($methodParameters);
    while ($i < $count) {
        $tempParameter = $methodParameters[$i];
        $parameters[] = $tempParameter->isOptional() ? $tempParameter->getDefaultValue() : null;
        $i++;
    }
    $reflected->invokeArgs($obj, $parameters);
}

class Application {

    public static function getSegmentsOfUri($uri) {
        return array_values(array_filter(array_map('trim', explode('/', $uri)), 'strlen'));
    }

    public static function optionallyGetKey($haystack, $index, $defaultValue = null) {
        return isset($haystack[$index]) ? $haystack[$index] : $defaultValue;
    }

    public static $mimeTypes = array(
        'html' => 'text/html',
        'htm' => 'text/html',
        'txt' => 'text/plain',
        'css' => 'text/css',
        'js' => 'text/javascript',
    );
    private $controller;
    private $layoutView;
    private $responseStatusCode = '202';
    private $responseBody;

    public function setUri($uri) {
        $this->uri = $uri;
        $this->cachedUriSegments = self::getSegmentsOfUri($uri);
    }

    public function getUri($uri) {
        return $this->uri;
    }

    public function getControllerName() {
        return self::optionallyGetKey($this->cachedUriSegments, 0);
    }

    public function getActionName() {
        return self::optionallyGetKey($this->cachedUriSegments, 1);
    }

    public function getParameters() {
        $parameters = array();
        $i = 2;
        $count = count($this->cachedUriSegments);
        while ($i < $count) {
            $parameters[] = $this->cachedUriSegments[$i];
            $i++;
        }

        return $parameters;
    }

    public function getRawSegments() {
        return $this->cachedUriSegments;
    }

    public function useRouting($routes) {
        foreach ($routes as $before => $after) {
            $beforeUri = self::getSegmentsOfUri($before);
            if (strcasecmp(self::optionallyGetKey($beforeUri, 0), $this->getControllerName()) === 0) {
                if (strcasecmp(self::optionallyGetKey($beforeUri, 1), $this->getActionName()) === 0) {
                    $this->setUri($after);
                }
            }
        }
    }

    public function load() {
        $viewsFolder = APP . DS . 'views' . DS;

        $this->layoutFilename = $viewsFolder . 'layouts' . DS . 'default.php';

        $this->loadAppConfig();

        $this->useRouting(Config::getAllRoutes());

        $controllerName = $this->getControllerName();

        $controller = Controller::createDynamically($controllerName);

        $actionName = $this->getActionName();
        if ($controller != null) {
            if (strlen($actionName) === 0) {
                $actionName = 'index';
            }
            try {
                $controller->setLayout($this->layoutFilename);
                lazy_call_method($controller, $actionName, $this->getParameters());
                $this->responseBody = $controller->getViewOutput($actionName);
            } catch (ReflectionException $e) {
                $this->responseBody = $this->handleException(new ActionNotFoundException($e));
            } catch (Exception $e) {
                $this->responseBody = $this->handleException($e);
            }
        } else {
            $file = APP . DS . 'public' . DS . implode(DS, $this->getRawSegments());
            if (is_file($file)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $mimeType = 'application/object-stream';
                if (isset(Application::$mimeTypes[$ext])) {
                    $mimeType = Application::$mimeTypes[$ext];
                }
                header('Content-type: ' . $mimeType);
                readfile($file);

                return;
            } else {
                $this->responseBody = $this->handleException(new ControllerNotFoundException());
            }
        }
        Application::changeStatus($this->responseStatusCode);
        echo $this->responseBody;
    }

    private static function changeStatus($statusCode) {
        $proto = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header($proto . ' ' . $statusCode . ' CODE');
    }

    private function loadAppConfig() {
        $configFolder = APP . DS . 'config' . DS;

        $configFiles = scandir($configFolder);

        foreach ($configFiles as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (strcasecmp($ext, 'php') === 0) {
                include $configFolder . $file;
            }
        }
    }

    private function handleException($exception) {
        Config::get('logger')->error($exception);
        $appErrorsFolder = APP . DS . 'views' . DS . 'errors' . DS;
        if ($exception instanceof NotFoundException) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 NotFound');
            try {
                $view = View::create($appErrorsFolder . '404.php', $this->layoutFilename);
                $view->setVariables(array('exception' => $exception));

                return $view->getOutput();
            } catch (ActionNotFoundException $e) {
                
            }
            try {
                $view = View::create($appErrorsFolder . '400.php', $this->layoutFilename);
                $view->setVariables(array('exception' => $exception));

                return $view->getOutput();
            } catch (ActionNotFoundException $e) {
                
            }
        }
        try {
            $view = View::create($appErrorsFolder . get_class($exception) . '.php', $this->layoutFilename);
            $view->setVariables(array('exception' => $exception));

            return $view->getOutput();
        } catch (ActionNotFoundException $e) {
            
        }
        try {
            $view = View::create($appErrorsFolder . 'default.php', $this->layoutFilename);
            $view->setVariables(array('exception' => $exception));

            return $view->getOutput();
        } catch (ActionNotFoundException $e) {
            
        }
        try {
            $view = View::createFromText('<p>Unhandled exception!</p><pre>' . h($exception) . '</pre>', $this->layoutFilename);

            return $view->getOutput();
        } catch (ActionNotFoundException $e) {
            
        }
        $handler = Config::read('exception.handler');
        if ($handler != null && $handler($exception)) {
            return;
        }

        return h($exception);
    }

}
