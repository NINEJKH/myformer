<?php

namespace App\Transformer;

use Exception;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;

class Transform
{
    protected $mappings = [];

    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    public function transform($row)
    {
        if (!preg_match('~^INSERT INTO `([^`]+)`~', $row, $match)) {
            return $row;
        }

        $table = $match[1];

        if (!isset($this->mappings[$table])) {
            return $row;
        }

        try {
            $parser = new Parser($row);
        } catch(Exception $e) {
            return $row;
        }

        $insert = $parser->statements[0];
        $mappings = $this->mappings[$table];

        $new_values = $this->map($insert, $mappings);

        $insert->values[0] = new \PhpMyAdmin\SqlParser\Components\ArrayObj(array_values($new_values));

        return $insert->build();
    }

    protected function map(InsertStatement $insert, array $mappings)
    {
        $values = array_combine($insert->into->columns, $insert->values[0]->raw);

        foreach ($mappings as $column => $rule) {
            $class_name = 'App\\Transformer\\Transformers\\' . key($rule);
            $transformer = new $class_name;
            $transformer->setParam(current($rule));
            $values[$column] = $transformer->transform($values, $column);
            unset($transformer);
        }

        return $values;
    }
}
