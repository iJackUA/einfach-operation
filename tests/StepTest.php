<?php

namespace einfach\operation\test;

use einfach\operation\Railway;
use einfach\operation\step\Step;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isError;
use function einfach\operation\response\isValidResponse;

class StepTest extends \PHPUnit\Framework\TestCase
{
    const STEP_NAME = 'myTestStep';

    public function testOnSuccessTrackOk()
    {
        $closure = function ($params) {
            $params['newParam1'] = 'value1';
            return ok($params, ['newParam2' => 'value2']);
        };
        $step = new Step($closure, self::STEP_NAME);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertTrue(isOk($result));
        $this->assertFalse(isError($result));
        $this->assertFalse($step->isSkipped());

        $this->assertEquals($step->name(), self::STEP_NAME);
        
        $this->assertFalse(isset($result['appendParams']));
        $this->assertEquals($result['params']['newParam1'], 'value1');
        $this->assertEquals($result['params']['newParam2'], 'value2');
    }

    public function testOnSuccessTrackError()
    {
        $closure = function ($params) {
            return error($params);
        };
        $step = new Step($closure, self::STEP_NAME);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertFalse(isOk($result));
        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
    }

    public function testOnFailureTrackOk()
    {
        $closure = function ($params) {
            return ok($params);
        };
        $step = new Step($closure, self::STEP_NAME);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isValidResponse($result));
        $this->assertNull($result);
        $this->assertTrue($step->isSkipped());
    }

    public function testOnFailureTrackError()
    {
        $closure = function ($params) {
            return error($params);
        };
        $step = new Step($closure, self::STEP_NAME);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isValidResponse($result));
        $this->assertNull($result);
        $this->assertTrue($step->isSkipped());
    }

    public function testNestedRailway()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                return ok($params, ['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams([]);
        };

        $step = new Step($closure, self::STEP_NAME);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertTrue(isOk($result));
        $this->assertFalse(isError($result));
        $this->assertFalse($step->isSkipped());
    }
}
