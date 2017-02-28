<?php

namespace einfach\operation\step;

abstract class AbstractStep
{
    /**
     * @var callable
     */
    public $function;
    public $name;

    public function __construct(callable $callable, string $name)
    {
        $this->function = $callable;
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    abstract public function __invoke(&$params);
}
