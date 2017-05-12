<?php

namespace einfach\operation\test;

use function einfach\operation\response\{ok, error, isOk, isError, isValidResponse};
use const einfach\operation\response\{RESPONSE_TYPE_OK, RESPONSE_TYPE_ERROR};

class FunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testOk()
    {
        $this->assertEquals(
            ok(['param1' => 1], ['a' => 'b']),
            ['params' => ['param1' => 1], 'type' => RESPONSE_TYPE_OK, 'appendParams' => ['a' => 'b']]
        );
    }

    public function testError()
    {
        $this->assertEquals(
            error(['param1' => 1], 'Oh!'),
            ['params' => ['param1' => 1], 'type' => RESPONSE_TYPE_ERROR, 'appendError' => ['Oh!']]
        ); 
    }

    public function testisOk()
    {
        $this->assertTrue(isOk(ok([])));
        $this->assertFalse(isOk(error([])));
    }

    public function testisError()
    {
        $this->assertTrue(isError(error([])));
        $this->assertFalse(isError(ok([])));
    }

    public function testIsValidResponse()
    {
        $this->assertTrue(isValidResponse(['params' => ['param1' => 1], 'type' => RESPONSE_TYPE_OK, 'appendParams' => []]));
        $this->assertFalse(isValidResponse(['appendParams' => []]));
    }
}
