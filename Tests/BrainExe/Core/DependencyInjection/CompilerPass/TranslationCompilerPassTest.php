<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\TranslationCompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TranslationCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TranslationCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $mockContainer;

    public function setUp()
    {
        $this->subject = new TranslationCompilerPass();

        $this->mockContainer = $this->getMock(ContainerBuilder::class);
    }

    public function testProcessWithInvalidRoot()
    {
        $this->subject->process($this->mockContainer);
    }
}
