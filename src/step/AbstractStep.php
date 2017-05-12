<?php

namespace einfach\operation\step;

use const einfach\operation\response\RESPONSE_TYPE_ERROR;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use function einfach\operation\response\isValidResponse;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isError;
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

    public function functionSignature() : string
    {
        is_callable($this->function, false, $functionName);
        return $functionName;
    }

    /**
     * Step name, respecting custom name
     */
    public function name() : string
    {
        return $this->name ?? $this->functionSignature();
    }

    public function signature($template = "%-10s | %s") : string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return sprintf($template, $className, $this->name());
    }

    /**
     * Transform all results into valid resut array form
     * It could be Result object instance for nested steps
     */
    protected function normalizeStepResponse($result) : array
    {
        $stepResult = $result;
        
        if (is_a($result, Result::class)) {
             $stepResult = [
                    'params' => $result->params(),
                    'type' => ($result->isSuccess()) ? RESPONSE_TYPE_OK : RESPONSE_TYPE_ERROR
                ];
        }

        if (!isValidResponse($stepResult)) {
            $actualResult = var_export($stepResult, true);
            throw new \Exception("Step '{$this->name()}' returned incorrectly formatted result. \
            Maybe you forgot to return `ok(\$params)` or `error(\$params)`. \
            Current return: {$actualResult}");
        }

        if (isOk($stepResult)) {
            $appendParams = $stepResult['appendParams'] ?? [];
            $stepResult['params'] = $stepResult['params'] ?? [];
            $stepResult['params'] = array_merge($stepResult['params'], $appendParams);
            unset($stepResult['appendParams']);
        } elseif (isError($stepResult)) {
            $appendError = $stepResult['appendError'] ?? [];
            $stepResult['params']['__errors'] = $stepResult['params']['__errors'] ?? [];
            if ($appendError) {
                $stepResult['params']['__errors'] = $stepResult['params']['__errors'] + $appendError;
            }
            unset($stepResult['appendError']);
        }

        return $stepResult;
    }

    abstract public function __invoke(&$params, string $track);
}
