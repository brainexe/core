<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Console\ListServicesCommand;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers BrainExe\Core\Console\ListServicesCommand
 */
class ListServicesCommandTest extends TestCase
{

    /**
     * @var ListServicesCommand
     */
    private $subject;

    /**
     * @var Rebuild|MockObject
     */
    private $rebuild;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->rebuild    = $this->createMock(Rebuild::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new ListServicesCommand($this->rebuild);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecuteShowAll()
    {
        $commandTester = $this->setupMocks();

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("+-------------+------------+
| service-id  | visibility |
+-------------+------------+
| __service_4 | protected  |
| service_1   | public     |
| service_2   | private    |
+-------------+------------+\n", $output);
    }

    public function testExecuteFilterPublic()
    {
        $commandTester = $this->setupMocks();

        $commandTester->execute(['visibility' => 'public']);
        $output = $commandTester->getDisplay();

        $this->assertEquals("+------------+------------+
| service-id | visibility |
+------------+------------+
| service_1  | public     |
+------------+------------+\n", $output);
    }

    public function testExecuteFilterPrivate()
    {
        $commandTester = $this->setupMocks();

        $commandTester->execute(['visibility' => 'private']);
        $output = $commandTester->getDisplay();

        $this->assertEquals("+------------+------------+
| service-id | visibility |
+------------+------------+
| service_2  | private    |
+------------+------------+\n", $output);
    }

    /**
     * @return CommandTester
     */
    private function setupMocks()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $definition1      = $this->createMock(Definition::class);
        $definition2      = $this->createMock(Definition::class);
        $definition4      = $this->createMock(Definition::class);

        $this->rebuild
            ->expects($this->once())
            ->method('rebuildDIC')
            ->with(false)
            ->willReturn($containerBuilder);

        $serviceIds = [
            'service_2',
            'service_1',
            'service_3',
            '__service_4',
        ];

        $containerBuilder
            ->expects($this->at(0))
            ->method('getServiceIds')
            ->willReturn($serviceIds);

        $containerBuilder
            ->expects($this->at(1))
            ->method('hasDefinition')
            ->with('__service_4')
            ->willReturn(true);
        $containerBuilder
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with('__service_4')
            ->willReturn($definition4);
        $containerBuilder
            ->expects($this->at(3))
            ->method('hasDefinition')
            ->with('service_1')
            ->willReturn(true);
        $containerBuilder
            ->expects($this->at(4))
            ->method('getDefinition')
            ->with('service_1')
            ->willReturn($definition1);
        $containerBuilder
            ->expects($this->at(5))
            ->method('hasDefinition')
            ->with('service_2')
            ->willReturn(true);
        $containerBuilder
            ->expects($this->at(6))
            ->method('getDefinition')
            ->with('service_2')
            ->willReturn($definition2);
        $containerBuilder
            ->expects($this->at(7))
            ->method('hasDefinition')
            ->with('service_3')
            ->willReturn(false);

        $definition1
            ->expects($this->once())
            ->method('isPublic')
            ->willReturn(true);
        $definition2
            ->expects($this->once())
            ->method('isPublic')
            ->willReturn(false);
        $definition4
            ->expects($this->once())
            ->method('isPublic')
            ->willReturn(true);

        return $commandTester;
    }
}
