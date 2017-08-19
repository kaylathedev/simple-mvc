<?php
/* Should I use another class, called LoggerAware? */
abstract class AbstractDatabase
{
    private $currentLogger;
    private $connectionString;
    private $username;
    private $password;
    private $options;

    public function getLogger()
    {
        return $this->currentLogger;
    }
	
    public function setLogger(LoggerInterface $newLogger)
    {
        $this->currentLogger = $newLogger;
    }

    public function getConnectionString()
    {
        return $this->connectionString;
    }

    public function setConnectionString($newConnectionString)
    {
        $this->connectionString = $newConnectionString;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($newUsername)
    {
        $this->username = $newUsername;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($newPassword)
    {
        $this->password = $newPassword;
    }

    public function getAllOptions()
    {
        return $this->options;
    }

    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    public function setOption($key, $newOption)
    {
        $this->options[$key] = $newOption;
    }

    public function open()
    {
    
    }
    
    public function close()
    {
    
    }

    public function query($query, array $context)
    {
        throw new DatabaseException('This database does not support queries!');
    }
    
    abstract public function execute(Query $queryObject);
    abstract public function lastInsertId();
    
    public function insert()
    {
        return new InsertQuery($this);
    }

    public function select()
    {
        return new SelectQuery($this);
    }

    public function update()
    {
        return new UpdateQuery($this);
    }

    public function delete()
    {
        return new DeleteQuery($this);
    }

}
