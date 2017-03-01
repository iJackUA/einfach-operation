<?php

namespace einfach\operation;


use function einfach\operation\response\isError;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isValidResponse;
use const einfach\operation\response\RESPONSE_TYPE_ERROR;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use einfach\operation\step\Step;
use einfach\operation\step\Failure;
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
        return $this->rawStep(new Always($callable, $name), $opt);
    }

    function failure(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'Fail');
        return $this->rawStep(new Failure($callable, $name), $opt);
    }

    function tryCatch(callable $callable, $opt = [])
    {
        $name = $this->nextStepName($callable, 'TryCatch');
        return $this->rawStep(new TryCatch($callable, $name), $opt);
    }

    function wrap(callable $callable, $opt = [])
    {
        // check if Result -> evaluate
        // if bool -> passthrough
        $name = $this->nextStepName($callable, 'Wrap');
        return $this->rawStep(new Wrap($callable, $name), $opt);
    }

    protected function nextStepName(callable $callable, $stepName)
    {
        is_callable($callable, false, $functionName);
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
        $path = [];

        $track = 'ok';
        foreach ($this->stepsQueue as $step) {
            /** @var $step AbstractStep */
            $track = $this->performStep($step, $params, $track, $path);
        }
        return new Result($params, $track, $path);
    }

    /**
     * @param $params
     * @param $step
     * @return string
     * @throws \Exception
     */
    protected function performStep($step, &$params, $track, &$path)
    {
        $newTrack = $track;
        $stepResult = $step($params, $track);

        if (!$step->skipped) {
            if (isValidResponse($stepResult)) {
                $type = $stepResult['type'];
                if (isOk($type)) {
                    $newTrack = self::TRACK_OK;
                    $appendParams = $stepResult['appendParams'] ?? [];
                    $params = array_merge_recursive($params, $appendParams);
                } elseif (isError($type)) {
                    $newTrack = self::TRACK_ERROR;
                    $appendError = $stepResult['appendError'] ?? '';
                    $params['errors'][] = $appendError;
                }

                $path[] = $step->name;
            } else {
                throw new \Exception("Step returned incorrectly formatted result");
            }
        }

        return $newTrack;
    }
}
