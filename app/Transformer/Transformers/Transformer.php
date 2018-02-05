<?php

namespace App\Transformer\Transformers;

use App\Transformer\Persistence;

abstract class Transformer implements TransformerInterface
{
    protected $param;

    protected $persistent_key;

    public function setParam($param)
    {
        $this->param = $param;
    }

    public function setPersistentKey($persistent_key)
    {
        $this->persistent_key = $persistent_key;
    }

    protected function replaceValue($original, $new)
    {
        if ($original === 'NULL' || $original === "''") {
            return $original;
        }

        if ($this->persistent_key) {
            $full_persistent_key = sprintf('%s/%s', $this->persistent_key, $original);

            $persistent_value = Persistence::get($full_persistent_key);
            if ($persistent_value !== null) {
                $new = $persistent_value;
            } else {
                Persistence::set($full_persistent_key, $new);
            }
        }

        if ($original[0] === "'" && $original[-1] === "'") {
            return "'{$new}'";
        }

        return $new;
    }
}
