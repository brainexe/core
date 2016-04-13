<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\LocaleCompilerPass;
use BrainExe\Core\Util\Glob;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LocaleCompilerPassTest extends TestCase
{

    /**
     * @var LocaleCompilerPass
     */
    private $subject;

    /**
     * @var ContainerBuilder|MockObject $container
     */
    private $container;

    public function setUp()
    {
        $this->subject = new LocaleCompilerPass();

        $this->container  = $this->getMock(ContainerBuilder::class, [
            'get',
            'setParameter',
        ]);
    }

    public function testProcess()
    {
        $glob = $this->getMock(Glob::class);

        $glob
            ->expects($this->once())
            ->method('execGlob')
            ->with(ROOT . 'lang/*.po')
            ->willReturn([
                '/lang/en.po',
                '/lang/es.po',
            ]);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('Glob')
            ->willReturn($glob);

        $this->container
            ->expects($this->once())
            ->method('setParameter')
            ->with('locales', ['en', 'es']);

        $this->subject->process($this->container);
    }
}
