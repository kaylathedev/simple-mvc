<?php

class InsertQuery extends Query
{

    public function getSql()
    {
        $text = 'INSERT INTO ' . $this->tableName;
        if (count($this->columnValues) !== 0) {
            return $text . '(' . implode(',', array_keys($this->columnValues)) . ')VALUES(' . implode(',', array_values($this->columnValues)) . ')';
        }
        return $text . ' DEFAULT VALUES';
    }

}