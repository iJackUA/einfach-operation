<?php

namespace einfach\operation\test;

use einfach\operation\Railway;
use einfach\operation\step\TryCatch;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isError;
use function einfach\operation\response\isValidResponse;

class TryCatchTest extends \PHPUnit\Framework\TestCase
{
    public function testOnSuccessTrackOk()
    {
        $closure = function ($params) {
            return ok($params);
        };
        $step = new TryCatch($closure);
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
        $step = new TryCatch($closure);
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
        $step = new TryCatch($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isOk($result));
        $this->assertFalse(isError($result));
        $this->assertTrue($step->isSkipped());
    }

    public function testOnFailureTrackError()
    {
        $closure = function ($params) {
            return error($params);
        };
        $step = new TryCatch($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertTrue($step->isSkipped());
    }

    public function testOnSuccessTrackCatch()
    {
        $closure = function ($params) {
           throw new \Exception('Oups!');
        };
        $step = new TryCatch($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
        $this->assertEquals($result['params']['__errors'][0], 'Oups!');
    }

    public function testNestedRailwayException()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                throw new \Exception('Oups!');
            })
            ->runWithParams([]);
        };

        $step = new TryCatch($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());
        $this->assertEquals($result['params']['__errors'][0], 'Oups!');
    }
}
