<?php

namespace App\Model;

class SearchModel
{
    private $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return SearchModel
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}