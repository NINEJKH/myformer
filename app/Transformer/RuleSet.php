<?php

namespace App\Transformer;

use Iterator;

class RuleSet implements Iterator
{
    protected $rules = [];

    protected $column = [];

    protected $pos;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
        $this->columns = array_keys($rules);
        $this->pos = 0;
    }

    public function next()
    {
        ++$this->pos;
    }

    public function valid()
    {
        return isset($this->columns[$this->pos]);
    }

    public function current()
    {
        $column = $this->columns[$this->pos];
        $rule = $this->rules[$column];

        if (isset($rule[0])) {
            $rules = [];

            foreach($rule as $each) {
                $rules[] = new Rule($column, $each);
            }

            return $rules;
        } else {
            return new Rule($column, $rule);
        }
    }

    public function rewind()
    {
        $this->pos = 0;
    }

    public function key()
    {
        return $this->pos;
    }
}
