<?php

namespace App\Transformer\Transformers;

class RandomInt extends Transformer
{
    public function transform(array $columns_n, array $values, $column)
    {
        return $this->replaceValue($values[$column], $this->randomNumber());
    }

    protected function randomNumber()
    {
        $min = (int) ('1' . str_repeat('0', $this->param - 1));
        $max = (int) str_repeat('9', $this->param);

        if (function_exists('random_int')) {
            return random_int($min, $max);
        } else {
            return rand($min, $max);
        }
    }
}
