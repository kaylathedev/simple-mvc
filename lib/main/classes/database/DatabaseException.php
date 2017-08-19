<?php

class DatabaseException extends Exception
{
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $logger = Config::get('logger');
        if ($logger !== null) {
            $logger->error((string)$this);
        }
        parent::__construct($message, $code, $previous);
    }

    /*public function __toString() {
        return get_class() . ' : There was a problem with the database.';
    }*/

}
