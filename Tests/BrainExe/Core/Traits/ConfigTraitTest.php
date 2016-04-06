<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\ConfigTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;
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
     * @var Container|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container = $this->getMock(Container::class);
        $this->subject   = new ConfigTest();
    }

    public function testGetConfig()
    {
        $key   = 'key';
        $value = 'value';

        $parameterBag = $this->getMock(ParameterBag::class);
        $this->container
            ->expects($this->once())
            ->method('getParameterBag')
            ->willReturn($parameterBag);

        $parameterBag
            ->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        $this->subject->setContainer($this->container);
        $actual = $this->subject->testGetParameter($key);

        $this->assertEquals($value, $actual);
    }
}
