<?php

namespace Tests\BrainExe\Core\Console\ClearCacheCommand;

use BrainExe\Core\MessageQueue\Command\ExecuteJob;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\MessageQueue\Worker;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ExecuteJobTest extends TestCase
{
    const DUMMY_EVENT  = 'TzozMDoiQnJhaW5FeGVcQ29yZVxNZXNzYWdlUXVldWVcSm9iIjo0OntzOjU6ImV2ZW50IjtPOjM5OiJCcmFpbkV4ZVxDb3JlXEV2ZW50RGlzcGF0Y2hlclxDcm9uRXZlbnQiOjQ6e3M6MTA6ImV4cHJlc3Npb24iO3M6NzoiQGhvdXJseSI7czo1OiJldmVudCI7Tzo0ODoiQnJhaW5FeGVcQ29yZVxFdmVudERpc3BhdGNoZXJcRXZlbnRzXFRpbWluZ0V2ZW50IjozOntzOjg6InRpbWluZ0lkIjtzOjY6ImhvdXJseSI7czoxMDoiZXZlbnRfbmFtZSI7czo2OiJ0aW1pbmciO3M6NTk6IgBTeW1mb255XENvbXBvbmVudFxFdmVudERpc3BhdGNoZXJcRXZlbnQAcHJvcGFnYXRpb25TdG9wcGVkIjtiOjA7fXM6MTA6ImV2ZW50X25hbWUiO3M6MTg6Im1lc3NhZ2VfcXVldWUuY3JvbiI7czo1OToiAFN5bWZvbnlcQ29tcG9uZW50XEV2ZW50RGlzcGF0Y2hlclxFdmVudABwcm9wYWdhdGlvblN0b3BwZWQiO2I6MDt9czo1OiJqb2JJZCI7czoyMjoibWVzc2FnZV9xdWV1ZS5jcm9uOjMwNiI7czo5OiJ0aW1lc3RhbXAiO2k6MTQ0ODIyOTYwMDtzOjEyOiJlcnJvckNvdW50ZXIiO2k6MDt9';

    /**
     * @var ExecuteJob
     */
    public $subject;

    /**
     * @var Worker|MockObject
     */
    private $worker;

    public function setUp()
    {
        $this->worker = $this->getMock(Worker::class, [], [], '', false);

        $this->subject = new ExecuteJob($this->worker);
    }

    public function testExecute()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMock(Application::class, ['run']);
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $this->worker
            ->expects($this->once())
            ->method('executeJob')
            ->with($this->isInstanceOf(Job::class));

        $commandTester->execute(['job' => 'type:1#' . self::DUMMY_EVENT]);
        $output = $commandTester->getDisplay();

        $this->assertEquals("", $output);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid job: type:1#sdd
     */
    public function testExecuteInvalid()
    {
        /** @var Application|MockObject $application */
        $application = $this->getMock(Application::class, ['run']);
        $this->subject->setApplication($application);

        $commandTester = new CommandTester($this->subject);

        $this->worker
            ->expects($this->never())
            ->method('executeJob');

        $commandTester->execute(['job' => 'type:1#' . 'sdd']);
        $output = $commandTester->getDisplay();

        $this->assertEquals("", $output);
    }
}
