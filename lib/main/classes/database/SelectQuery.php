<?php

class SelectQuery extends Query
{

    public function getSql()
    {
        $text = 'SELECT ' . (count($this->columnValues) === 0 ? '*' : implode(',', $this->columnValues)) . ' FROM ' . $this->tableName;
        if (isset($this->segments['where'])) {
            $text .= ' WHERE ' . $this->segments['where'];
        }
        if (isset($this->segments['limit'])) {
            $text .= ' LIMIT ' . $this->segments['limit'];
        }
        if (isset($this->segments['orderBy'])) {
            $text .= ' ORDER BY ' . $this->segments['orderBy'];
        }
        return $text;
    }

}