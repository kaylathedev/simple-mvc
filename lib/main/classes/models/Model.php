<?php

if (function_exists('is_assoc')) {
    function is_assoc($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) !== 0;
    }
}

class Model
{

    public function create(array $values)
    {
        return $this->insert()->setMany($values)->execute();
    }

    public function tableExists($tableName = null)
    {
        if ($tableName === null) {
            $tableName = $this->tableName;
        }
        $bindValues = array($tableName);
        $results = self::getDatabaseInstance()->query('SHOW TABLES LIKE ?', $bindValues);
        if (isset($results[0])) {
            return true;
        }

        return false;
    }

    protected $tableName;
    
    public function __construct(){
        if($this->tableName === null){
            $this->tableName = Language::tableize(get_class($this));
        }
    }
    
    protected static function getDatabaseInstance()
    {
        return Config::get('db');
    }
    
    public function lastInsertId($name = null)
    {
        return self::getDatabaseInstance()->lastInsertId($name);
    }

    public function exists($where, $binded = array())
    {
        $data = $this->select()->where($where)->bindMany($binded)->execute();
        //$data = $this->query('SELECT id FROM ' . $this->tableName . ' WHERE ' . , array_values($values));
        return isset($data[0]);
    }
    
    public function findAll()
    {
        return $this->select()->execute();
    }
    
    private function _parseWordsIntoExpression(array $words)
    {
        $columns = array();
        $nextColumn = array();
        foreach ($words as $word) {
            if (strcasecmp($word, 'And') === 0) {
                $columns[] = implode('_', $nextColumn) . '=?';
                $columns[] = 'AND';
                $nextColumn = array();
            } elseif (strcasecmp($word, 'Or') === 0) {
                $columns[] = implode('_', $nextColumn) . '=?';
                $columns[] = 'OR';
                $nextColumn = array();
            } else {
                $nextColumn[] = strtolower($word);
            }
        }
        $columns[] = implode('_', $nextColumn) . '=?';
        return implode(' ', $columns);
    }
    
    public function __call($method, $arguments)
    {
        $words = Language::getWords($method);
        if (count($words > 2)) {
            if (strcasecmp($words[0], 'find') === 0) {
                if (strcasecmp($words[1], 'By') === 0) {
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 2));
                    $results = $this->select()->where($whereExpression)->bindMany($arguments)->limit(1)->execute();
                    return isset($results[0]) ? $results[0] : null;
                } elseif (strcasecmp($words[1], 'All') === 0 && strcasecmp($words[2], 'By') === 0) {
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 3));
                    return $this->select()->where($whereExpression)->bindMany($arguments)->execute();
                }
            } elseif (strcasecmp($words[0], 'delete') === 0) {
                if (strcasecmp($words[1], 'By') === 0) {
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 2));
                    $results = $this->delete()->where($whereExpression)->bindMany($arguments)->limit(1)->execute();
                    return isset($results[0]) ? $results[0] : null;
                } elseif (strcasecmp($words[1], 'All') === 0 && strcasecmp($words[2], 'By') === 0) {
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 3));
                    return $this->delete()->where($whereExpression)->bindMany($arguments)->execute();
                }
            } elseif (strcasecmp($words[0], 'update') === 0) {
                if (strcasecmp($words[1], 'By') === 0) {
                    $whereArguments = array_slice($arguments, 0, count($arguments) - 1);
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 2));
                    return $this->update()->setMany(end($arguments))->where($whereExpression)->bindMany($whereArguments)->limit(1)->execute();
                } elseif (strcasecmp($words[1], 'All') === 0 && strcasecmp($words[2], 'By') === 0) {
                    $whereArguments = array_slice($arguments, 0, count($arguments) - 1);
                    $whereExpression = $this->_parseWordsIntoExpression(array_slice($words, 3));
                    return $this->update()->setMany(end($arguments))->where($whereExpression)->bindMany($whereArguments)->execute();
                }
            }
        }
    }
    
    public function insert()
    {
        return self::getDatabaseInstance()->insert()->from($this->tableName);
    }
    
    public function select()
    {
        return self::getDatabaseInstance()->select()->from($this->tableName);
    }
    
    public function update()
    {
        return self::getDatabaseInstance()->update()->from($this->tableName);
    }
    
    public function delete()
    {
        return self::getDatabaseInstance()->delete()->from($this->tableName);
    }
    
}
