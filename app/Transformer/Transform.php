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
        $ruleset = new RuleSet($mappings);

        foreach ($ruleset as $rule) {
            $this->transformValues($rule, $columns_n, $values);
        }

        return $values;
    }

    protected function transformValues($rule, array $columns_n, array &$values)
    {
        foreach ($values as $n => $value_group) {
            $transformer = null;

            if (is_array($rule)) {
                foreach ($rule as $each) {
                    if ($each->condition === null || $each->condition->evaluate($values[$n][$columns_n[$each->column]])) {
                        $column_n = $columns_n[$each->column];
                        $transformer = $this->getTransformer($each);
                        break;
                    }
                }

                if (!$transformer) {
                    continue;
                }

            } else {
                $transformer = $this->getTransformer($rule);
                $column_n = $columns_n[$rule->column];
            }


            $values[$n][$column_n] = $transformer->transform($columns_n, $value_group, $column_n);
        }
    }

    protected function getTransformer(Rule $rule)
    {
        if (!isset($this->transformers[$rule->name])) {
            $class_name = 'App\\Transformer\\Transformers\\' . $rule->name;
            $this->transformers[$rule->name] = new $class_name;
            $this->transformers[$rule->name]->setParam($rule->param);
        }

        return $this->transformers[$rule->name];
    }
}
