<?php

abstract class Query
{
    
    public function __construct(AbstractDatabase $database){
        $this->db = $database;
    }
    
    protected $db;
    protected $binded = array();
    protected $command;
    protected $tableName;
    protected $segments = array();
    protected $columnValues = array();
    
    public function execute()
    {
        return $this->db->execute($this);
    }
    
    public function getBinded()
    {
        return $this->binded;
    }

    public function getFrom()
    {
        return $this->tableName;
    }

    public function getWhere()
    {
        return isset($this->segments['where']) ? $this->segments['where'] : null;
    }

    public function getLimit()
    {
        return isset($this->segments['limit']) ? $this->segments['limit'] : null;
    }

    public function getColumns()
    {
        return $this->columnValues;
    }

    public function addColumn($column)
    {
        $this->columnValues[] = $column;
        return $this;
    }
    
    public function set($key, $value = null)
    {
        $this->columnValues[$key] = '?';
        $this->binded[] = $value;
        return $this;
    }
    
    public function setMany(array $values)
    {
        foreach ($values as $key => $value) {
            $this->columnValues[$key] = '?';
            $this->binded[] = $value;
        }
        return $this;
    }
    
    public function setCommand($value)
    {
        $this->command = $value;
        return $this;
    }
    
    public function bind($value)
    {
        $this->binded[] = $value;
        return $this;
    }
    
    public function bindMany(array $values)
    {
        $this->binded = array_merge($this->binded, $values);
        return $this;
    }
    
    public function from($segment)
	{
        $this->tableName = $segment;
        return $this;
	}
	
    public function limit($segment)
	{
        $this->segments['limit'] = $segment;
        return $this;
	}
	
    public function where($segment)
	{
        $this->segments['where'] = $segment;
        return $this;
	}
	
    public function orderBy($segment)
	{
        $this->segments['orderBy'] = $segment;
        return $this;
	}
    
    abstract public function getSql();

}