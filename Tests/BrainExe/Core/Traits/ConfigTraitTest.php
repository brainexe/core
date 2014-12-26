<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\ConfigTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class ConfigTest
{
    use ConfigTrait;

    public function testGetParameter($key)
    {
        return $this->getParameter($key);
    }
}

class ConfigTraitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ConfigTrait
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $mockContainer;

    public function setUp()
    {
        $this->mockContainer = $this->getMock(Container::class);

        $this->subject = new ConfigTest();
        $this->subject->setContainer($this->mockContainer);
    }

    public function testGetConfig()
    {
        $key   = 'key';
        $value = 'value';

        $this->mockContainer
            ->expects($this->once())
            ->method('getParameter')
            ->with($key)
            ->willReturn($value);

        $actualResult = $this->subject->testGetParameter($key);

        $this->assertEquals($value, $actualResult);
    }
}
