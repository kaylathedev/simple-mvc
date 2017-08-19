<?php

class Controller
{
    public static function createDynamically($name)
    {
        $className = ucfirst($name) . 'Controller';
        if (class_exists($className)) {
            $ret = new $className;
            $ret->controllerName = lcfirst($name);

            return $ret;
        }

        return null;
    }
	
    private $controllerName;
    private $layoutFilename;
    private $variablesSet = array();

    public function _lock()
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('You must be logged in!', 'warning');
            $this->redirect('/members/login');
        }
    }

    public function _lockWithPermission($permission, $redirectedPage = '/', $message = 'You do not have permission to do this!')
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('You must be logged in!', 'warning');
            $this->redirect('/members/login');
        }
        $r = new Role();
        if (!$r->isAllowed(Session::getCurrentUser('username'), $permission)) {
            Session::setFlash($message, 'warning');
            $this->redirect($redirectedPage);
        }
    }
	
    public function redirect($url, $status = 302)
    {
        $autoAddRoot = Config::get('auto_add_root', false);
        if ($autoAddRoot) {
            $url = rtrim(WEB_ROOT, '\\/') . '/' . trim($url, '\\/');
        }
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status . ' Status');
        header('Location: ' . $url);
        exit;
    }

    public function is($what)
    {
        if ($what === 'post') {
            return $_SERVER['REQUEST_METHOD'] === 'POST';
        }
        if ($what === 'get') {
            return $_SERVER['REQUEST_METHOD'] === 'GET';
        }
        return false;
    }

    public function set($key, $value)
    {
        $this->variablesSet[$key] = $value;
    }

    public function setLayout($value)
    {
        $this->layoutFilename = $value;
    }

    public function getViewOutput($viewName)
    {
        $viewFilename = APP . DS . 'views' . DS . $this->controllerName . DS . $viewName . '.php';
        $view = View::create($viewFilename, $this->layoutFilename);
        $view->setVariables($this->variablesSet);

        return $view->getOutput();
    }

}
