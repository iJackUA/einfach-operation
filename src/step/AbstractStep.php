<?php

namespace einfach\operation\step;

abstract class AbstractStep
{
    /**
     * @var callable
     */
    public $function;
    public $signature;
    public $track;
    /**
     * Indicates was this step performed or not
     *
     * @var bool
     */
    protected $skipped;

    public function __construct(callable $callable, string $signature)
    {
        $this->function = $callable;
        $this->signature = $signature;
        $this->skipped = false;
    }

    public function isSkipped()
    {
        return true == $this->skipped;
    }

    public function skip()
    {
        return $this->skipped = true;
    }

    abstract public function __invoke(&$params, string $track);
}
