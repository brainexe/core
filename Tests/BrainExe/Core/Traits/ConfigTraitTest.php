<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\ConfigTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ConfigTest
{
    use ConfigTrait;

    public function testGetParameter($key)
    {
        return $this->getParameter($key);
    }
}

class ConfigTraitTest extends TestCase
{

    /**
     * @var ConfigTest
     */
    private $subject;

    /**
     * @var ParameterBag|MockObject
     */
    private $parameterBag;

    public function setUp()
    {
        $this->parameterBag = $this->createMock(ParameterBag::class);
        $this->subject      = new ConfigTest();
    }

    public function testGetConfig()
    {
        $key   = 'key';
        $value = 'value';

        $this->parameterBag
            ->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $this->subject->setParameterBag($this->parameterBag);
        $actual = $this->subject->testGetParameter($key);

        $this->assertEquals($value, $actual);
    }
}
