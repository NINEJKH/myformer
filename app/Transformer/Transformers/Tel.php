<?php

namespace App\Transformer\Transformers;

class Tel extends Transformer
{
    public function transform(array $columns_n, array $values, $column)
    {
        return $this->replaceValue($values[$column], sprintf('999%d', rand(1000000000, 9999999999)));
    }
}
