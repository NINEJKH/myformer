<?php

namespace App\Transformer\Transformers;

class Tel extends Transformer
{
    protected $prefix = '';

    public function transform(array $columns_n, array $values, $column)
    {
        return $this->replaceValue($values[$column], sprintf('%s999%d', $this->prefix, rand(1000000000, 9999999999)));
    }

    public function setParam($param)
    {
        $this->param = $param;

        if (!empty($param)) {
            $this->prefix = $param;
        }
    }
}
