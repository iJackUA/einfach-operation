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
    public $stepsQueue;
    /**
     * Accumulator of passed steps, while running railway
     *
     * @var array
     */
    protected $signaturesPipeline = [];

    public function __construct()
    {
        $this->stepsQueue = [];
    }

    protected function findTargetStepIndex($stepName)
    {
        $steps = array_column($this->stepsQueue, 'step');
        $names = array_map(function ($step) {
            return $step->name();
        }, $steps);

        $targetIndex = array_search($stepName, $names);
        return $targetIndex;
    }

    protected function insertStepBefore(string $stepName, AbstractStep $stepObject, array $opt)
    {
        $targetIndex = $this->findTargetStepIndex($stepName);
        $step = [
            'step' => $stepObject,
            'opt' => $opt
        ];
        array_splice($this->stepsQueue, $targetIndex, 0, [$step]);
    }

    protected function insertStepAfter(string $stepName, AbstractStep $stepObject, array $opt)
    {
        $targetIndex = $this->findTargetStepIndex($stepName);
        $step = [
            'step' => $stepObject,
            'opt' => $opt
        ];
        array_splice($this->stepsQueue, $targetIndex + 1, 0, [$step]);
    }

    protected function replaceStep(string $stepName, AbstractStep $stepObject, array $opt)
    {
        $targetIndex = $this->findTargetStepIndex($stepName);
        $step = [
            'step' => $stepObject,
            'opt' => $opt
        ];
        array_splice($this->stepsQueue, $targetIndex, 1, [$step]);
    }

    protected function addStep(AbstractStep $stepObject, array $opt)
    {
        $this->stepsQueue[] = [
            'step' => $stepObject,
            'opt' => $opt
        ];
    }

    /**
     *
     * @param AbstractStep $stepObject
     * @param array        $opt
     * @return $this
     */
    public function rawStep(AbstractStep $stepObject, $opt = []) : Railway
    {
        $before = $opt['before'] ?? null;
        $after = $opt['after'] ?? null;
        $replace = $opt['replace'] ?? null;

        if ($before) {
            $this->insertStepBefore($before, $stepObject, $opt);
        } elseif ($after) {
            $this->insertStepAfter($after, $stepObject, $opt);
        } elseif ($replace) {
            $this->replaceStep($replace, $stepObject, $opt);
        } else {
            $this->addStep($stepObject, $opt);
        }

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

    public function removeStep(string $stepName)
    {
        $targetIndex = $this->findTargetStepIndex($stepName);
        unset($this->stepsQueue[$targetIndex]);
        return $this;
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
        foreach ($this->stepsQueue as $item) {
            $step = $item['step'];
            $opt = $item['opt'];
            /**
             * @var $step AbstractStep
            */
            $track = $this->performStep($step, $params, $opt, $track);

            $failFast = $opt['failFast'] ?? null;
            if($failFast && $track == self::TRACK_FAILURE) {
                 break;
            }
        }
        return new Result($params, $track, $this->signaturesPipeline);
    }

    /**
     * @throws \Exception
     */
    protected function performStep($step, &$params, $opt, $track)
    {
        $nextTrack = $track;
        $stepResult = $step($params, $track, $opt);

        if (!$step->isSkipped()) {
            if (isValidResponse($stepResult)) {
                if (isOk($stepResult)) {
                    $nextTrack = self::TRACK_SUCCESS;
                } elseif (isError($stepResult)) {
                    $nextTrack = self::TRACK_FAILURE;
                }
                $params = $stepResult['params'];
                $this->signaturesPipeline[] = $step->signature();
            } else {
                $actualResult = var_export($stepResult, true);
                throw new \Exception("Step returned incorrectly formatted result: {$actualResult}");
            }
        }

        return $nextTrack;
    }
}
