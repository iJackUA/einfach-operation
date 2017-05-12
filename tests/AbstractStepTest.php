<?php

namespace einfach\operation\test;

use einfach\operation\step\AbstractStep;

class AbstractStepTest extends \PHPUnit\Framework\TestCase
{
    protected $step;
    const STEP_NAME = 'myStepName';

    protected function setUp()
    {
        $constructorParams = [
            function () {
            },
            self::STEP_NAME
        ];

        $this->step = $this->getMockForAbstractClass(AbstractStep::class, $constructorParams);
        // $this->step->expects($this->any())
        //      ->method('abstractMethod')
        //      ->will($this->returnValue(true));
    }
    
    public function testSkipped()
    {
        $this->assertFalse($this->step->isSkipped());
        $this->step->skip();
        $this->assertTrue($this->step->isSkipped());
    }

    public function testCustomFunctionName()
    {
        $this->assertEquals($this->step->functionSignature(), 'Closure::__invoke');
        $this->assertEquals($this->step->name(), self::STEP_NAME);
    }

    public function testDefaultFunctionName()
    {
        $constructorParams = [
            function () {
            }
        ];

        $this->step = $this->getMockForAbstractClass(AbstractStep::class, $constructorParams);
        
        $this->assertEquals($this->step->functionSignature(), 'Closure::__invoke');
        $this->assertEquals($this->step->name(), 'Closure::__invoke');
    }
}
