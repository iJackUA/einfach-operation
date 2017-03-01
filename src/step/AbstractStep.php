<?php

namespace einfach\operation\step;

abstract class AbstractStep
{
    /**
     * @var callable
     */
    public $function;
    public $name;
    public $track;

    public function __construct(callable $callable, string $name)
    {
        $this->function = $callable;
        $this->name = $name;
    }

    abstract public function __invoke(&$params, string $track);
}
