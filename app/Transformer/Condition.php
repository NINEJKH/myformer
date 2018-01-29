<?php

namespace App\Transformer;

abstract class Condition implements ConditionInterface
{
    protected $param;

    public function __construct($param)
    {
        $this->param = $param;
    }
}
