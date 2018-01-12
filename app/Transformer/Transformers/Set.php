<?php

namespace App\Transformer\Transformers;

class Set extends Transformer
{
    public function transform(array $values, $column)
    {
        return $this->replaceValue($values[$column], $this->param);
    }
}
