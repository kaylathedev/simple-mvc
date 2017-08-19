<?php

function strallpos($haystack, $needle, $offset = 0, &$count = null)
{
    $match = array();
    for ($count=0; ($pos = strpos($pajar, $aguja, $offset)) !== false; $count++) {
        $match[] = $pos;
        $offset = $pos + strlen($aguja);
    }
    return $match; 
}

class JSONDatabase extends AbstractDatabase
{
    private $pdoConnection;
    private $lastInsertId;

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }
    
    public function query($query, array $context = array())
    {
        return true;
    }
    
    private $cachedDatabase;
    
    public function execute(Query $queryObject)
    {
        try {
            if ($this->cachedDatabase === null) {
                $this->cachedDatabase = json_decode(file_get_contents($this->getConnectionString()), true);
            }
            if ($this->cachedDatabase !== null) {
                $tableName = $queryObject->getFrom();
                if (isset($this->cachedDatabase['tables'][$tableName])) {
                    if ($queryObject instanceof SelectQuery) {
                        return $this->executeSelect($queryObject, $this->cachedDatabase['tables'][$tableName]);
                    }
                    if ($queryObject instanceof InsertQuery) {
                        $newTable = $this->executeInsert($queryObject, $this->cachedDatabase['tables'][$tableName]);
                        $this->cachedDatabase['tables'][$tableName] = $newTable;
                        file_put_contents($this->getConnectionString(), json_encode($this->cachedDatabase));
                    }
                    if ($queryObject instanceof UpdateQuery) {
                        return $this->executeUpdate($queryObject, $this->cachedDatabase['tables'][$tableName]);
                    }
                    if ($queryObject instanceof DeleteQuery) {
                        return $this->executeDelete($queryObject, $this->cachedDatabase['tables'][$tableName]);
                    }
                }
                throw new DatabaseException(print_r(json_last_error(), true));
            }
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
        throw new DatabaseException(print_r(json_last_error(), true));
    }
    
    private function executeSelect(SelectQuery $queryObject, array $rawTable)
    {
        $data = array();
        foreach ($rawTable['data'] as $rowId => $row) {
            $newRow = array();
            foreach ($rawTable['columns'] as $column => $columnExtra) {
                $newRow[$column] = isset($row[$column]) ? $row[$column] : null;
            }
            $data[$rowId] = $newRow;
        }
        unset($rawTable['data']);
        
        $where = $queryObject->getWhere();
        if ($where === null) {
            return $data;   
        }
        
        $evaluatedData = $this->evaluateWhere($queryObject, $data);
        
        $columns = $queryObject->getColumns();
        if (count($columns) !== 0) {
            $data = array();
            foreach ($evaluatedData as $rowId => $row) {
                $newRow = array();
                foreach ($columns as $column) {
                    $newRow[$column] = isset($row[$column]) ? $row[$column] : null;
                }
                $data[$rowId] = $newRow;
            }
            return $data;
        }
        return $evaluatedData;
    }
    
    private function executeInsert(InsertQuery $queryObject, array $rawTable)
    {
        $nextId = 0;
        foreach ($rawTable['data'] as $key => $value) {
            if($key > $nextId) {
                $nextId = $key;
            }
        }
        $newRow = $queryObject->getColumns();
        $rawTable['data'][$nextId] = $newRow;
        $this->lastInsertId = $nextId;
        return $rawTable;
    }
    
    private function executeUpdate(UpdateQuery $queryObject, array $rawTable)
    {
    
    }
    
    private function executeDelete(DeleteQuery $queryObject, array $rawTable)
    {
    
    }
    
    private function evaluateWhere(Query $queryObject, array $data)
    {
        $binded = $queryObject->getBinded();
        foreach ($binded as $key => $bind) {
            $binded[$key] = 'base64_decode("' . base64_encode($bind) . '")';
        }
        
        $where = $queryObject->getWhere();
        $where = preg_replace('#and#i', '&&', $where);
        $where = preg_replace('#!#i', '&&', $where);
        $where = preg_replace('#or#i', '||', $where);
        $where = preg_replace('#(\b(?<![\'"])[a-zA-Z_][a-zA-Z_0-9]*\b(?![\'"]))#i', '\$row[\'$1\']', $where);
        $where = str_replace(array('?'), $binded, $where);
        $where = preg_replace('#=+#', '==', $where);
        $where = 'return (bool)(' . $where . ');';
        $limit = $queryObject->getLimit();
        $ret = array();
        $countRet = 0;
        foreach ($data as $id => $row) {
            if ($limit > 0 && $countRet > $limit) {
                break;
            }
            if (eval($where)) {
                $ret[$id] = $row;
                $countRet++;
            }
        }
        return $ret;
    }
    
}
