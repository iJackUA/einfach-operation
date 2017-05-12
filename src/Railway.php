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
    /**
     * Accumulator of passed steps, while running railway
     *
     * @var array
     */
    protected $signaturesPipeline = [];

    public function __construct()
    {
        $this->stepsQueue = new \SplObjectStorage();
    }

    /**
     *
     * @param AbstractStep $stepObject
     * @param array $opt
     * @return $this
     */
    public function rawStep(AbstractStep $stepObject, $opt = []) : Railway
    {
        $this->stepsQueue->attach($stepObject, $opt);
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
        $params['__errors'] = [];

        $track = self::TRACK_SUCCESS;
        foreach ($this->stepsQueue as $step) {
            /** @var $step AbstractStep */
            $track = $this->performStep($step, $params, $track);
        }
        return new Result($params, $track, $this->signaturesPipeline);
    }

    /**
     * @throws \Exception
     */
    protected function performStep($step, &$params, $track)
    {
        $nextTrack = $track;
        $stepResult = $step($params, $track);

        if (!$step->isSkipped()) {
            if (isValidResponse($stepResult)) {
                $type = $stepResult['type'];
                if (isOk($type)) {
                    $nextTrack = self::TRACK_SUCCESS;
                    $appendParams = $stepResult['appendParams'] ?? [];
                    $params = array_merge($params, $appendParams);
                } elseif (isError($type)) {
                    $nextTrack = self::TRACK_FAILURE;
                    $appendError = $stepResult['appendError'] ?? [];
                    if($appendError) {
                        $params['__errors'] = $params['__errors'] + $appendError;
                    }
                }

                $this->signaturesPipeline[] = $step->signature();
            } else {
                $actualResult = var_export($stepResult, true);
                throw new \Exception("Step returned incorrectly formatted result: {$actualResult}");
            }
        }

        return $nextTrack;
    }
}
