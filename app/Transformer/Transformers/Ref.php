<?php

namespace App\Transformer\Transformers;

class Ref extends Transformer
{
    public function transform(array $values, $column)
    {
        return $this->replaceValue($values[$column], trim($values[$this->param], "'"));
    }
}
