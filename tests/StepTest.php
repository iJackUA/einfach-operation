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
    public function testOnSuccessTrackOk()
    {
        $closure = function () {
            return ok();
        };
        $step = new Step($closure, 'myTestStep');
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertTrue(isOk($result['type']));
        $this->assertFalse(isError($result['type']));
        $this->assertFalse($step->isSkipped());

        $this->assertEquals($step->functionName(), 'myTestStep');
    }

    public function testOnSuccessTrackError()
    {
        $closure = function () {
            return error();
        };
        $step = new Step($closure, 'myTestStep');
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertFalse(isOk($result['type']));
        $this->assertTrue(isError($result['type']));
        $this->assertFalse($step->isSkipped());
    }

    public function testOnFailureTrackOk()
    {
        $closure = function () {
            return ok();
        };
        $step = new Step($closure, 'myTestStep');
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isValidResponse($result));
        $this->assertNull($result);
        $this->assertTrue($step->isSkipped());
    }

    public function testOnFailureTrackError()
    {
        $closure = function () {
            return error();
        };
        $step = new Step($closure, 'myTestStep');
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isValidResponse($result));
        $this->assertNull($result);
        $this->assertTrue($step->isSkipped());
    }

    public function testNestedRailway()
    {
        $closure = function () {
            return (new Railway)
            ->step(function ($params) {
                //return error('Nested Railway failed!');
                return ok(['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams($params);
        };

        $step = new Step($closure, 'myTestStep');
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isValidResponse($result));
        $this->assertTrue(isOk($result['type']));
        $this->assertFalse(isError($result['type']));
        $this->assertFalse($step->isSkipped());
    }
}
