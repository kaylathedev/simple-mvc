<?php

class FileLogger implements LoggerInterface
{

    private $filename = 'main.log';
    public function setFilename($location)
    {
        $this->filename = $location;
    }

    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }
    
    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }
    
    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }
    
    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }
    
    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }
    
    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }
    
    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }
    
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        file_put_contents($this->filename, date(time()) . ' - ' . $level . ' - ' . $message . "\r\nContext: " . implode(', ', $context) . "\r\n", FILE_APPEND);
    }

}
