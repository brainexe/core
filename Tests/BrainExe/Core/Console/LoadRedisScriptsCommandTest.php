<?php

namespace Tests\BrainExe\Core\Console\LoadRedisScriptsCommand;

use BrainExe\Core\Console\LoadRedisScriptsCommand;
use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Core\Redis\RedisScripts;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\PhpRedis;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class LoadRedisScriptsCommandTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var LoadRedisScriptsCommand
     */
    private $subject;

    /**
     * @var RedisScripts|MockObject
     */
    private $mockRedisScripts;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedisScripts = $this->getMock(RedisScripts::class, [], [], '', false);
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new LoadRedisScriptsCommand($this->mockRedisScripts);
        $this->subject->setRedis($this->mockRedis);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);
        $scripts = [
            $sha11 = 'hash_1' => $script1 = 'script 1',
            $sha12 = 'hash_2' => $script2 = 'script 2',
            $sha13 = 'hash_3' => $script3 = 'script 3',
        ];

        $this->mockRedisScripts
            ->expects($this->once())
            ->method('getAllScripts')
            ->willReturn($scripts);

        $this->mockRedis
            ->expects($this->at(0))
            ->method('script')
            ->with('EXISTS', $sha11)
            ->willReturn([0 =>'Already Loaded']);

        $this->mockRedis
            ->expects($this->at(1))
            ->method('script')
            ->with('EXISTS', $sha12)
            ->willReturn([0 => null]);

        $this->mockRedis
            ->expects($this->at(2))
            ->method('script')
            ->with('LOAD', $script2)
            ->willReturn(true);

        $this->mockRedis
            ->expects($this->at(3))
            ->method('script')
            ->with('EXISTS', $sha13)
            ->willReturn([0 => null]);

        $this->mockRedis
            ->expects($this->at(4))
            ->method('script')
            ->with('LOAD', $script3)
            ->willReturn(false);

        $this->mockRedis
            ->expects($this->at(5))
            ->method('getLastError')
            ->willReturn('error');

        $commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
        $output = $commandTester->getDisplay();

        $expectedResult = "Load Redis Scrips...
Script hash_1 was already loaded
Loaded script hash_2 (script 2)
Error: error
script 3
done in";

        $this->assertStringStartsWith($expectedResult, $output);
    }
}
