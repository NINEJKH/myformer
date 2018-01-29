<?php

namespace App\Transformer;

interface ConditionInterface
{
    public function evaluate($against);
}
