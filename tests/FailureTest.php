<?php

namespace einfach\operation\test;

use einfach\operation\Railway;
use einfach\operation\step\Failure;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;
use function einfach\operation\response\isOk;
use function einfach\operation\response\isError;
use function einfach\operation\response\isValidResponse;

class FailureTest extends \PHPUnit\Framework\TestCase
{
    public function testOnSuccessTrackOk()
    {
        $closure = function ($params) {
            $params['newParam1'] = 'value1';
            return ok($params, ['newParam2' => 'value2']);
        };
        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertFalse(isOk($result));
        $this->assertFalse(isError($result));
        $this->assertTrue($step->isSkipped());

        $this->assertFalse(isset($result['appendParams']));
        $this->assertFalse(isset($result['params']['newParam1']));
        $this->assertFalse(isset($result['params']['newParam2']));
    }

    public function testOnFailureTrackOk()
    {
        $closure = function ($params) {
            $params['newParam1'] = 'value1';
            return ok($params, ['newParam2' => 'value2']);
        };
        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isOk($result));
        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());

        $this->assertFalse(isset($result['appendParams']));
        $this->assertEquals($result['params']['newParam1'], 'value1');
        $this->assertEquals($result['params']['newParam2'], 'value2');
    }

    public function testOnFailureTrackError()
    {
        $closure = function ($params) {
            $params['newParam1'] = 'value1';
            return error($params, 'Oups!');
        };
        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isOk($result));
        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());

        $this->assertEquals($result['params']['newParam1'], 'value1');
        $this->assertEquals($result['params']['__errors'][0], 'Oups!');
    }

    public function testOnSuccessTrackError()
    {
        $closure = function ($params) {
            $params['newParam1'] = 'value1';
            return error($params, 'Oups!');
        };
        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_SUCCESS);

        $this->assertFalse(isOk($result));
        $this->assertFalse(isError($result));
        $this->assertTrue($step->isSkipped());

        $this->assertFalse(isset($result['appendParams']));
        $this->assertFalse(isset($result['appendError']));
        $this->assertFalse(isset($result['params']['newParam1']));
        $this->assertFalse(isset($result['__errors'][0]));
    }

    public function testNestedRailwayOk()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                return ok($params, ['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams([]);
        };

        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isOk($result));
        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());

        $this->assertEquals($result['params']['nestedRwParam'], 'nestedRwValue');
    }

        public function testNestedRailwayError()
    {
        $closure = function ($params) {
            return (new Railway)
            ->step(function ($params) {
                return error($params, 'Oups!');
            })
            ->runWithParams([]);
        };

        $step = new Failure($closure);
        $params = [];
        $result = $step($params, Railway::TRACK_FAILURE);

        $this->assertFalse(isOk($result));
        $this->assertTrue(isError($result));
        $this->assertFalse($step->isSkipped());

        $this->assertEquals($result['params']['__errors'][0], 'Oups!');
    }
}
