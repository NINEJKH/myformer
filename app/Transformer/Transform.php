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

        $new_values = $this->map($insert->columns, $insert->values, $mappings);

        return $this->build($table, $insert->columns, $new_values);
    }

    protected function build($table, array $columns, array $values)
    {
        $joined_values = [];

        foreach ($values as $value_group) {
            $joined_values[] = '(' . implode(',', $value_group) . ')';
        }

        return sprintf(
            'INSERT INTO `%s` (`%s`) VALUES %s;',
            $table,
            implode('`,`', $columns),
            implode(',', $joined_values)
        );
    }

    protected function map(array $columns, array $values, array $mappings)
    {
        $columns_n = array_flip($columns);

        foreach ($mappings as $column => $rule) {
            $rule_name = key($rule);

            if (!isset($this->transformers[$rule_name])) {
                $class_name = 'App\\Transformer\\Transformers\\' . $rule_name;
                $this->transformers[$rule_name] = new $class_name;
                $this->transformers[$rule_name]->setParam(current($rule));
            }

            foreach ($values as $n => $value_group) {
                $column_n = $columns_n[$column];
                $values[$n][$column_n] = $this->transformers[$rule_name]->transform($columns_n, $value_group, $column_n);
            }

        }

        return $values;
    }
}
