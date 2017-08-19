<?php

class View
{
    public static function create($filename, $layoutFilename)
    {
        $ret = new View;
        $ret->isFile = true;
        $ret->content = $filename;
        $ret->layoutFilename = $layoutFilename;

        return $ret;
    }

    public static function createFromText($text, $layoutFilename)
    {
        $ret = new View;
        $ret->isFile = false;
        $ret->content = $text;
        $ret->layoutFilename = $layoutFilename;

        return $ret;
    }

    private $isFile;
    private $layoutFilename;
    private $content;
    private $variables = array();

    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    public function flash($type = 'main')
    {
        $key = 'messages.' . $type;
        $message = Session::get($key);
        if ($message !== null) {
            Session::remove($key);
            $v = View::createFromText($message['message'], $message['layout'] . '.php');
            $v->setVariables(array('type' => $type));
            echo $v->getOutput();
        }
    }

    public function getOutput()
    {
        foreach ($this->variables as $key => $value) {
            $$key = $value;
        }

        if ($this->isFile) {
            if (is_file($this->content)) {
                ob_start();
                try {
                    include $this->content;
                    $content = ob_get_contents();
                } catch (Exception $e) {
                }
                ob_end_clean();
                if (isset($e)) {
                    throw $e;
                }
            } else {
                throw new ActionNotFoundException();
            }
        } else {
            $content = $this->content;
        }
        if (is_file($this->layoutFilename)) {
            ob_start();
            try {
                include $this->layoutFilename;
                $layoutContent = ob_get_contents();
            } catch (Exception $e) {
            }
            ob_end_clean();
            if (isset($e)) {
                throw $e;
            }

            return $layoutContent;
        } else {
            return $content;
        }
    }

}
