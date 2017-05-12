<?php

namespace einfach\operation\test;

use einfach\operation\Railway;
use einfach\operation\step\Always;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isError;
use function einfach\operation\response\isValidResponse;

class AlwaysTest extends \PHPUnit\Framework\TestCase
{
    const STEP_NAME = 'myTestStep';

    public function testOnSuccessTrackOk()
    {
        $closure = function ($params) {
            return ok($params);
        };
        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isOk($result));
        $this->assertFalse($step->isSkipped());
    }

    public function testOnSuccessTrackError()
    {
        $closure = function ($params) {
            return error($params);
        };
        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
    }

    public function testOnFailureTrackOk()
    {
        $closure = function ($params) {
            return ok($params);
        };
        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
    }

    public function testOnFailureTrackError()
    {
        $closure = function ($params) {
            return error($params);
        };
        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
    }

    public function testNestedRailwayOnSuccess()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                return ok($params, ['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams([]);
        };

        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isOk($result));
        $this->assertArrayHasKey('nestedRwParam', $result['params']);
        $this->assertFalse($step->isSkipped());
    }

       public function testNestedRailwayOnFailureStillExecuted()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                return ok(['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams([]);
        };

        $step = new Always($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertTrue(isError($result));
        $this->assertArrayHasKey('nestedRwParam', $result['params']);
        $this->assertFalse($step->isSkipped());
    }
}
