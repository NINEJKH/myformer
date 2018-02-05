<?php

namespace App\Transformer;

use Exception;
use MyFormer\Parser\Insert as InsertParser;

class Transform
{
    protected $rule_sets = [];

    protected $transformers = [];

    public function __construct(array $mappings)
    {
        foreach ($mappings as $table => $rules) {
            $this->rule_sets[$table] = new RuleSet($table, $rules);
        }
    }

    public function transform($row)
    {
        if (!preg_match('~^INSERT INTO `([^`]+)`~', $row, $match)) {
            return $row;
        }

        $table = $match[1];

        if (!isset($this->rule_sets[$table])) {
            return $row;
        }

        try {
            $insert = new InsertParser($row);
        } catch(Exception $e) {
            return $row;
        }

        $new_values = $this->map($table, $insert->columns, $insert->values);

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

    protected function map($table, array $columns, array $values)
    {
        $columns_n = array_flip($columns);

        // iterate every rule against values (= value groups)
        // -> that means every rule will only target a specific column of
        // each value group
        foreach ($this->rule_sets[$table] as $rule) {
            $this->transformValues($rule, $columns_n, $values);
        }

        return $values;
    }

    protected function transformValues($rule, array $columns_n, array &$values)
    {
        foreach ($values as $n => $value_group) {
            $apply_rule = null;

            if (is_array($rule)) {
                foreach ($rule as $each) {
                    if (!isset($columns_n[$each->column])) {
                        continue 2;
                    }

                    if ($each->condition === null || $each->condition->evaluate($values[$n][$columns_n[$each->column]])) {
                        $column_n = $columns_n[$each->column];
                        $apply_rule = $each;
                        break;
                    }
                }

                if (!$apply_rule) {
                    continue;
                }
            } else {
                $apply_rule = $rule;
                if (!isset($columns_n[$rule->column])) {
                    continue;
                }
                $column_n = $columns_n[$rule->column];
            }

            $values[$n][$column_n] = $apply_rule->transformer->transform($columns_n, $value_group, $column_n);
        }
    }
}
