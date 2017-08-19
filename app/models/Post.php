<?php

class Post extends Model
{
    private $tableColumns = array('title', 'content');
	
    public function create(array $data)
	{
        foreach ($this->tableColumns as $column) {
            if (isset($data[$column])) {
                $bindValues[$column] = $data[$column];
            } else {
                $bindValues[$column] = '';
            }
        }
		parent::create($data);
	}
}