<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\ConfigCompilerPass;
use BrainExe\Core\Environment;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigCompilerPassTest extends TestCase
{

    /**
     * @var ConfigCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    public function setUp()
    {
        $this->container  = $this->getMockBuilder(ContainerBuilder::class)->setMethods([
            'hasParameter',
            'getParameter',
            'setParameter',
        ])->getMock();
        $this->subject = new ConfigCompilerPass();
    }

    public function testProcessWithInvalidRoot()
    {
        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('environment')
            ->willReturn(Environment::DEVELOPMENT);
        $this->container
            ->expects($this->exactly(1))
            ->method('setParameter');

        $this->subject->process($this->container);
    }
}
