<?php

namespace App\Transformer\Transformers;

class Ref extends Transformer
{
    public function transform(array $columns_n, array $values, $column)
    {
        return $this->replaceValue($values[$column], trim($values[$columns_n[$this->param]], "'"));
    }
}
