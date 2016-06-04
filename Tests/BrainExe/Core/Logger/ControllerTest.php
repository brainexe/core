<?php

namespace BrainExe\Tests\Logger\Index;

use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Logger\Controller;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Logger\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new Controller();
        $this->subject->setLogger($this->logger);
    }

    public function testLogError()
    {
        $request = new Request();
        $request->request->set('message', 'myMessage');

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::ERROR, 'myMessage');

        $actual = $this->subject->logFrontend($request);

        $this->assertTrue($actual);
    }
}
