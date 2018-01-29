<?php

namespace App\Transformer;

class Rule
{
    public $column;

    public $name;

    public $param;

    public $condition;

    public function __construct($column, array $rule)
    {
        $this->column = $column;

        $this->extractNameAndParam($rule);
    }

    protected function extractNameAndParam(array $rule)
    {
        if (count($rule) === 1) {
            $this->name = key($rule);
            $this->param = current($rule);
        } else {
            if (isset($rule['Condition'])) {
                $condition_class = 'App\\Transformer\\Conditions\\' . key($rule['Condition']);
                $this->condition = new $condition_class(current($rule['Condition']));
                //$this->condition = [
                //    'name' => key($rule['Condition']),
                //    'param' => current($rule['Condition']),
                //];

                unset($rule['Condition']);
            }

            $this->name = key($rule);
            $this->param = current($rule);
        }
    }
}
