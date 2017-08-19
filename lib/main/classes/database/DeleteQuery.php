<?php

class DeleteQuery extends Query
{

    public function getSql()
    {
        $text = 'DELETE FROM ' . $this->tableName;
        if (isset($this->segments['where'])) {
            $text .= ' WHERE ' . $this->segments['where'];
        }
        return $text;
    }

}