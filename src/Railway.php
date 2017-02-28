<?php

namespace einfach\operation;


use einfach\operation\step\Base;
use einfach\operation\step\Fail;
use einfach\operation\step\Proxy;
use einfach\operation\step\TryCatch;
use einfach\operation\step\Wrap;

class Railway
{
    /**
     * @var SplQueue
     */
    protected $stepsQueue;

    function __construct($params)
    {
        $this->stepsQueue = new \SplQueue();
    }

    static function with($params)
    {
        return new static($params);
    }

    /**
     *
     * @param IStep $stepObject
     * @param array $opt
     * @return $this
     */
    function rawStep(IStep $stepObject, $opt = [])
    {
        $this->stepsQueue->enqueue($stepObject);
        return $this;
    }

    function step(callable $callable, $opt = [])
    {
        return $this->rawStep(new Base($callable), $opt);
    }

    function proxy(callable $callable, $opt = [])
    {
        return $this->rawStep(new Proxy($callable), $opt);
    }

    function fail(callable $callable, $opt = [])
    {
        return $this->rawStep(new Fail($callable), $opt);
    }

    function tryCatch(callable $callable, $opt = [])
    {
        return $this->rawStep(new TryCatch($callable), $opt);
    }

    function wrap(callable $callable, $opt = [])
    {
        // check if Result -> evaluate
        // if bool -> passthrough

        return $this->rawStep(new Wrap($callable), $opt);
    }

    /**
     * @return Result
     */
    function run()
    {

        // loop through success
        // on any success Step fails, stop success Track
        // start failure track and loop through it till the end

        foreach ($this->stepsQueue as $step) {

        }

        return new Result();
    }
}
