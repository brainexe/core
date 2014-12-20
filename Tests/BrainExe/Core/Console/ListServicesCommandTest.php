<?php

namespace Tests\BrainExe\Core\Console\ListServicesCommand;

use BrainExe\Core\Console\ListServicesCommand;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers BrainExe\Core\Console\ListServicesCommand
 */
class ListServicesCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ListServicesCommand
     */
    private $subject;

    /**
     * @var Rebuild|MockObject
     */
    private $mockRebuild;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockRebuild = $this->getMock(Rebuild::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new ListServicesCommand($this->mockRebuild);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testExecuteShowAll()
    {
        $commandTester = $this->_setupMocks();

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("List all services...+------------+------------+
| service-id | visibility |
+------------+------------+
| service_1  | public     |
| service_2  | private    |
+------------+------------+
done\n", $output);
    }

    public function testExecuteFilterPublic()
    {
        $commandTester = $this->_setupMocks();

        $commandTester->execute(['visibility' => 'public']);
        $output = $commandTester->getDisplay();

        $this->assertEquals("List all services...+------------+------------+
| service-id | visibility |
+------------+------------+
| service_1  | public     |
+------------+------------+
done\n", $output);
    }
    public function testExecuteFilterPrivate()
    {
        $commandTester = $this->_setupMocks();

        $commandTester->execute(['visibility' => 'private']);
        $output = $commandTester->getDisplay();

        $this->assertEquals("List all services...+------------+------------+
| service-id | visibility |
+------------+------------+
| service_2  | private    |
+------------+------------+
done\n", $output);
    }

    /**
     * @return CommandTester
     */
    private function _setupMocks()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $container_builder = $this->getMock(ContainerBuilder::class);
        $definition_1      = $this->getMock(Definition::class);
        $definition_2      = $this->getMock(Definition::class);

        $this->mockRebuild->expects($this->once())->method('rebuildDIC')->with(false)->will($this->returnValue($container_builder));

        $service_ids = [
        'service_2',
        'service_1',
        'service_3',
        ];

        $container_builder->expects($this->at(0))->method('getServiceIds')->will($this->returnValue($service_ids));

        $container_builder->expects($this->at(1))->method('hasDefinition')->with('service_1')->will($this->returnValue(true));
        $container_builder->expects($this->at(2))->method('getDefinition')->with('service_1')->will($this->returnValue($definition_1));

        $container_builder->expects($this->at(3))->method('hasDefinition')->with('service_2')->will($this->returnValue(true));
        $container_builder->expects($this->at(4))->method('getDefinition')->with('service_2')->will($this->returnValue($definition_2));

        $container_builder->expects($this->at(5))->method('hasDefinition')->with('service_3')->will($this->returnValue(false));

        $definition_1->expects($this->once())->method('isPublic')->will($this->returnValue(true));
        $definition_2->expects($this->once())->method('isPublic')->will($this->returnValue(false));

        return $commandTester;
    }
}
