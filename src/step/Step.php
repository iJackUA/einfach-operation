<?php

namespace einfach\operation\step;

class Step implements IStep
{
    /**
     * @var callable
     */
    public $function;

    public function __construct(callable $callable)
    {
        $this->function = $callable;
    }

    function __invoke(&$params)
    {
        return call_user_func($this->function, $params);
    }
}
