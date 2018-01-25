<?php

namespace App\Transformer\Transformers;

class Email extends Transformer
{
    public function transform(array $columns_n, array $values, $column)
    {
        $new_value = sprintf($this->param, substr(md5($values[$column]), 0, 16));
        return $this->replaceValue($values[$column], $new_value);
    }
}
