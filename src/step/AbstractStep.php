<?php

namespace einfach\operation\step;

use const einfach\operation\response\RESPONSE_TYPE_ERROR;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use einfach\operation\Result;

abstract class AbstractStep
{
    /**
     * @var callable
     */
    public $function;
    protected $name;
    /**
     * Indicates was this step performed or not
     *
     * @var bool
     */
    protected $skipped;

    public function __construct(callable $callable, string $name = null)
    {
        $this->function = $callable;
        $this->name = $name;
        $this->skipped = false;
    }

    public function isSkipped() : bool
    {
        return true == $this->skipped;
    }

    public function skip() : bool
    {
        return $this->skipped = true;
    }

    public function functionName() : string
    {
        is_callable($this->function, false, $functionName);
        return $this->name ?? $functionName;
    }

    /**
     * Transform all results into valid resut array form
     * It could be Result object instance for nested steps
     */
    protected function normalizeStepResult($result) : array
    {
        $stepResult = $result;
        
        if (is_a($result, Result::class)) {
            if ($result->isSuccess()) {
                $stepResult = [
                    'type' => RESPONSE_TYPE_OK,
                    'appendParams' => $result->params()
                ];
            } else {
                $stepResult = [
                    'type' => RESPONSE_TYPE_ERROR,
                    'appendError' => $result->errors()
                ];
            }
        }

        return $stepResult;
    }

    abstract public function __invoke(&$params, string $track);
}
