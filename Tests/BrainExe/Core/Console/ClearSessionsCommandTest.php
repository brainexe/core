<?php

namespace Tests\BrainExe\Core\Console\CreateRegisterLinkCommand;

use BrainExe\Core\Console\ClearSessionsCommand;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Redis;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers BrainExe\Core\Console\ClearSessionsCommand
 */
class ClearSessionsCommandTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var ClearSessionsCommand
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new ClearSessionsCommand();
        $this->subject->setRedis($this->redis);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $session = 'session:1234';

        $this->redis
            ->expects($this->once())
            ->method('keys')
            ->with('sessions:*')
            ->willReturn([$session]);

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with($session);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $expectedResult = "Deleted 1 sessions\n";
        $this->assertEquals($expectedResult, $output);
    }
}
