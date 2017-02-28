<?php

namespace einfach\operation\step;

class TryCatch extends AbstractStep
{
    function __invoke(&$params)
    {
        return call_user_func($this->function, $params);
    }
}
