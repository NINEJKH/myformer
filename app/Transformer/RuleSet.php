<?php

namespace App\Transformer;

use Iterator;

class RuleSet implements Iterator
{
    protected $table;

    protected $rules = [];

    protected $columns = [];

    protected $pos;

    public function __construct($table, array $rules)
    {
        $this->table = $table;
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
                $rules[] = new Rule($this->table, $column, $each);
            }

            return $rules;
        } else {
            return new Rule($this->table, $column, $rule);
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
