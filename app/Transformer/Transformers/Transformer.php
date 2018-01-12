<?php

namespace App\Transformer\Transformers;

abstract class Transformer
{
    protected $param;

    public function setParam($param)
    {
        $this->param = $param;
    }

    protected function replaceValue($original, $new)
    {
        if ($original === 'NULL' || $original === "''") {
            return $original;
        }

        if ($original[0] === "'" && $original[-1] === "'") {
            return "'{$new}'";
        }
        return $new;
    }
}
