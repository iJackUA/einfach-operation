<?php

namespace einfach\operation;


use const einfach\operation\response\RESPONSE_TYPE_ERROR;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use einfach\operation\step\Step;
use einfach\operation\step\Fail;
use einfach\operation\step\AbstractStep;
use einfach\operation\step\Always;
use einfach\operation\step\TryCatch;
use einfach\operation\step\Wrap;

class Railway
{
    const TRACK_OK = 'ok';
    const TRACK_ERROR = 'error';

    /**
     * @var SplQueue
     */
    protected $stepsQueue;

    function __construct()
    {
        $this->stepsQueue = new \SplQueue();
    }

    /**
     *
     * @param AbstractStep $stepObject
     * @param array $opt
     * @return $this
     */
    function rawStep(AbstractStep $stepObject, $opt = [])
    {
        $this->stepsQueue->enqueue($stepObject);
        return $this;
    }

    function step(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'Step');
        return $this->rawStep(new Step($callable, $name), $opt);
    }

    function always(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'Always');
        return $this->rawStep(new Always($callable), $opt);
    }

    function fail(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'Fail');
        return $this->rawStep(new Fail($callable), $opt);
    }

    function tryCatch(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'TryCatch');
        return $this->rawStep(new TryCatch($callable), $opt);
    }

    function wrap(callable $callable, $opt = [])
    {
        // check if Result -> evaluate
        // if bool -> passthrough
        $name = $this->nextStepName($callable, 'Wrap');
        return $this->rawStep(new Wrap($callable), $opt);
    }

    protected function nextStepName(callable $callable, $stepName){
        $functionName = (is_array($callable) && isset($callable[1]) && is_string($callable[1])) ? $callable[1] : '';
        $counter = $this->stepsQueue->count() + 1;
        return "#$counter | $stepName | $functionName";
    }

    /**
     * @param $params
     * @return Result
     * @throws \Exception
     */
    function runWithParams($params)
    {
        // a bit hardcoded, but let it be :)
        $params['errors'] = [];

        $track = 'ok';
        foreach ($this->stepsQueue as $step) {
            if (
                ($track == self::TRACK_OK && !is_a($step, Fail::class))
                || $track == self::TRACK_ERROR && is_a($step, Fail::class)
                || $track && is_a($step, Always::class)
            ) {
                $track = $this->performStep($step, $params);
            } else {
                // skip step if does not conform to requirements of execution
            }
        }
        return new Result($params, $track);
    }

    /**
     * @param $params
     * @param $step
     * @return string
     * @throws \Exception
     */
    protected
    function performStep($step, &$params)
    {
        $stepResult = $step($params);
        if ($stepResult && is_array($stepResult) && isset($stepResult['type'])) {
            $type = $stepResult['type'];
            if ($type == RESPONSE_TYPE_OK) {
                $track = self::TRACK_OK;
                $appendParams = $stepResult['appendParams'];
                $params = array_merge_recursive($params, $appendParams);
            } elseif ($type == RESPONSE_TYPE_ERROR) {
                $track = self::TRACK_ERROR;
                $appendError = $stepResult['appendError'];
                $params['errors'][] = $appendError;
            }
        } else {
            throw new \Exception("Step returned incorrectly formatted result");
        }
        return $track;
    }
}
