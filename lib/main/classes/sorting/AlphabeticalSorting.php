<?php

class AlphabeticalSorting extends Sortable
{
    private $lastKey;

    private function internalCompare($a, $b)
    {
        if ($a instanceof Comparable) {
            return $a->compare($b);
        }
        if ($this->lastKey !== null) {
            foreach ($this->lastKey as $key) {
                $a = $a[$key];
            }
            foreach ($this->lastKey as $key) {
                $b = $b[$key];
            }
        }
        if ($a == $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    public function sortDescending($data, $key)
    {
        if (!is_array($key)) {
            $key = array($key);
        }
        $this->lastKey = $key;
        uasort($data, array($this, 'internalCompare'));

        return $data;
    }

}
