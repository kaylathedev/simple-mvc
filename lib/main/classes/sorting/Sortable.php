<?php

abstract class Sortable
{
    private $options = array();

    public function getOption($key)
    {
        return isset($options[$key]) ? $options[$key] : null;
    }

    public function setOption($key, $value)
    {
        $options[$key] = $value;
    }

    abstract public function sortDescending($data, $key);

    public function sortAscending($data, $key)
    {
        return array_reverse($this->sortDescending($data, $key));
    }

}
