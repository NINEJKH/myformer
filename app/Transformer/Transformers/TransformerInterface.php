<?php

namespace App\Transformer\Transformers;

interface TransformerInterface
{
    public function transform(array $columns_n, array $values, $column);
}
