<?php

namespace App\Transformer;

class Rule
{
    public $table;

    public $column;

    public $name;

    public $param;

    public $condition;

    public $transformer;

    public $persistentKey;

    public function __construct($table, $column, array $rule)
    {
        $this->table = $table;

        $this->column = $column;

        $this->extractNameAndParam($rule);

        $this->getTransformer();
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

            if (isset($rule['PersistentKey'])) {
                $this->persistentKey = $rule['PersistentKey'];
                unset($rule['PersistentKey']);
            }

            $this->name = key($rule);
            $this->param = current($rule);
        }
    }

    protected function getTransformer()
    {
        $class_name = 'App\\Transformer\\Transformers\\' . $this->name;
        $this->transformer = new $class_name;
        $this->transformer->setPersistentKey($this->persistentKey);
        $this->transformer->setParam($this->param);
    }
}
