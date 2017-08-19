<?php

class UpdateQuery extends Query
{

    public function getSql()
    {
        $values = array();
        foreach ($this->columnValues as $key => $value) {
            $values[] = $key . '=' . $value;
        }
        $text = 'UPDATE ' . $this->tableName . ' SET ' . implode(',', array_values($values));
        if (isset($this->segments['where'])) {
            $text .= ' WHERE ' . $this->segments['where'];
        }
        return $text;
    }

}