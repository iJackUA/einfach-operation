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

class Railway
{
    const TRACK_SUCCESS = 'success_track';
    const TRACK_FAILURE = 'failure_track';

    /**
     * @var \SplQueue
     */
    protected $stepsQueue;

    public function __construct()
    {
        $this->stepsQueue = new \SplQueue();
    }

    /**
     *
     * @param AbstractStep $stepObject
     * @param array $opt
     * @return $this
     */
    public function rawStep(AbstractStep $stepObject, $opt = []) : Railway
    {
        $this->stepsQueue->enqueue($stepObject);
        return $this;
    }

    public function step(callable $callable, $opt = []) : Railway
    {
        $name = $opt['name'] ?? null;
        return $this->rawStep(new Step($callable, $name), $opt);
    }

    public function always(callable $callable, $opt = []) : Railway
    {
        $name = $opt['name'] ?? null;
        return $this->rawStep(new Always($callable, $name), $opt);
    }

    public function failure(callable $callable, $opt = []) : Railway
    {
        $name = $opt['name'] ?? null;
        return $this->rawStep(new Failure($callable, $name), $opt);
    }

    public function tryCatch(callable $callable, $opt = []) : Railway
    {
        $name = $opt['name'] ?? null;
        return $this->rawStep(new TryCatch($callable, $name), $opt);
    }

    /**
     * @param $params
     * @return Result
     * @throws \Exception
     */
    public function runWithParams($params)
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

                $signaturesPipeline[] = $this->getStepClassName($step);
            } else {
                $actualResult = var_export($stepResult, true);
                throw new \Exception("Step returned incorrectly formatted result: {$actualResult}");
            }
        }

        return $newTrack;
    }

    protected function getStepClassName(AbstractStep $step) : string
    {
        $className = (new \ReflectionClass($step))->getShortName();
        return sprintf("%-10s", $className) . " | " . $step->functionName();
    }
}
