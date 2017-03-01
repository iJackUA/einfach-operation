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
    /**
     * Indicates was this step performed ot not
     * @var bool
     */
    public $skipped;

    public function __construct(callable $callable, string $name)
    {
        $this->function = $callable;
        $this->name = $name;
        $this->skipped = false;
    }

    abstract public function __invoke(&$params, string $track);
}
