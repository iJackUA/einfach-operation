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
    const TRACK_SUCCESS = 'success_track';
    const TRACK_FAILURE = 'failure_track';

    /**
     * @var \SplQueue
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
    function rawStep(AbstractStep $stepObject, $opt = []) : Railway
    {
        $this->stepsQueue->enqueue($stepObject);
        return $this;
    }

    function step(callable $callable, $opt = []) : Railway
    {
        $signature = $this->nextStepSignature($callable, 'Step', $opt);
        return $this->rawStep(new Step($callable, $signature), $opt);
    }

    function always(callable $callable, $opt = []) : Railway
    {
        $signature = $this->nextStepSignature($callable, 'Always', $opt);
        return $this->rawStep(new Always($callable, $signature), $opt);
    }

    function failure(callable $callable, $opt = []) : Railway
    {
        $signature = $this->nextStepSignature($callable, 'Failure', $opt);
        return $this->rawStep(new Failure($callable, $signature), $opt);
    }

    function tryCatch(callable $callable, $opt = []) : Railway
    {
        $signature = $this->nextStepSignature($callable, 'TryCatch', $opt);
        return $this->rawStep(new TryCatch($callable, $signature), $opt);
    }

    function wrap(callable $callable, $opt = []) : Railway
    {
        // check if Result -> evaluate
        // if bool -> passthrough
        $signature = $this->nextStepSignature($callable, 'Wrap', $opt);
        return $this->rawStep(new Wrap($callable, $signature), $opt);
    }

    protected function nextStepSignature(callable $callable, $stepName, $opt) : string
    {
        is_callable($callable, false, $functionName);
        // assign custom step name from operation if provided
        $functionName = $opt['name'] ?? $functionName;
        $counter = $this->stepsQueue->count() + 1;
        return "#" . sprintf("%02d", $counter) . " | " . sprintf("%-10s", $stepName) . " | $functionName";
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
        $signaturesPipeline = [];

        $track = self::TRACK_SUCCESS;
        foreach ($this->stepsQueue as $step) {
            /** @var $step AbstractStep */
            $track = $this->performStep($step, $params, $track, $signaturesPipeline);
        }
        return new Result($params, $track, $signaturesPipeline);
    }

    /**
     * @param $params
     * @param $step
     * @return string
     * @throws \Exception
     */
    protected function performStep($step, &$params, $track, &$signaturesPipeline)
    {
        $newTrack = $track;
        $stepResult = $step($params, $track);
//TODO: Extract method
        if (is_a($stepResult, Result::class)) {
            if ($stepResult->isSuccess()) {
                $stepResult = [
                    'type' => RESPONSE_TYPE_OK,
                    'appendParams' => $stepResult->params()
                ];
            } else {
                $stepResult = [
                    'type' => RESPONSE_TYPE_ERROR,
                    'appendError' => $stepResult->errors()
                ];
            }
        }

        if (!$step->isSkipped()) {
            if (isValidResponse($stepResult)) {
                $type = $stepResult['type'];
                if (isOk($type)) {
                    $newTrack = self::TRACK_SUCCESS;
                    $appendParams = $stepResult['appendParams'] ?? [];
                    $params = array_merge($params, $appendParams);
                } elseif (isError($type)) {
                    $newTrack = self::TRACK_FAILURE;
                    $appendError = [$stepResult['appendError']] ?? [];
                    // TODO: Fix errors merge
                    print_r($params['errors']);
                    print_r($appendError);
                    $params['errors'] = $params['errors'] + $appendError;
                }

                $signaturesPipeline[] = $step->signature;
            } else {
                $actualResult = var_export($stepResult, true);
                throw new \Exception("Step returned incorrectly formatted result: {$actualResult}");
            }
        }

        return $newTrack;
    }
}
