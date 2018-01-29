<?php

namespace App\Transformer\Conditions;

use App\Transformer\Condition;

class Contains extends Condition
{
    public function evaluate($against)
    {
        return (bool) stripos($against, $this->param);
    }
}
