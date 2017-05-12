<?php

namespace einfach\operation\test;

use function einfach\operation\response\{ok, error, isOk, isError, isValidResponse};
use const einfach\operation\response\{RESPONSE_TYPE_OK, RESPONSE_TYPE_ERROR};

class FunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testOk()
    {
        $this->assertEquals(
            ok(['a' => 'b']),
            ['type' => RESPONSE_TYPE_OK, 'appendParams' => ['a' => 'b']]
        );
    }

    public function testError()
    {
        $this->assertEquals(
            error('Oh!'),
            ['type' => RESPONSE_TYPE_ERROR, 'appendError' => ['Oh!']]
        ); 
    }

    public function testisOk()
    {
        $this->assertTrue(isOk(RESPONSE_TYPE_OK));
        $this->assertFalse(isOk(RESPONSE_TYPE_ERROR));
    }

    public function testisError()
    {
        $this->assertTrue(isError(RESPONSE_TYPE_ERROR));
        $this->assertFalse(isError(RESPONSE_TYPE_OK));
    }

    public function testisValidResponse()
    {
        $this->assertTrue(isValidResponse(['type' => RESPONSE_TYPE_OK, 'appendParams' => []]));
        $this->assertFalse(isValidResponse(['appendParams' => []]));
    }
}
