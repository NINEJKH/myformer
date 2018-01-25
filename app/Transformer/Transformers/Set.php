<?php

namespace App\Transformer\Transformers;

class Set extends Transformer
{
    public function transform(array $columns_n, array $values, $column)
    {
        return $this->replaceValue($values[$column], $this->param);
    }
}
