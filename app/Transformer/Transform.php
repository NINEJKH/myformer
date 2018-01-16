<?php

namespace App\Transformer;

use Exception;
use MyFormer\Parser\Insert as InsertParser;

class Transform
{
    protected $mappings = [];

    protected $transformers = [];

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
            $insert = new InsertParser($row);
        } catch(Exception $e) {
            return $row;
        }

        $mappings = $this->mappings[$table];

        $new_values = $this->map(array_combine($insert->columns, $insert->values), $mappings);

        return sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s);',
            $table,
            implode('`, `', array_keys($new_values)),
            implode(', ', array_values($new_values))
        );
    }

    protected function map(array $values, array $mappings)
    {
        foreach ($mappings as $column => $rule) {
            $rule_name = key($rule);

            if (!isset($this->transformers[$rule_name])) {
                $class_name = 'App\\Transformer\\Transformers\\' . $rule_name;
                $this->transformers[$rule_name] = new $class_name;
                $this->transformers[$rule_name]->setParam(current($rule));
            }

            $values[$column] = $this->transformers[$rule_name]->transform($values, $column);
        }

        return $values;
    }
}
