<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\Gentime;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers BrainExe\Core\Middleware\Gentime
 */
class GentimeTest extends TestCase
{

    /**
     * @var Gentime
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new Gentime();
        $this->subject->setLogger($this->logger);
    }

    public function testProcessResponse()
    {
        $request  = new Request();
        $response = new Response();

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with('info', $this->isType('string'), $this->isType('array'));

        $this->subject->processResponse($request, $response);
    }

    public function testProcessResponseWithUser()
    {
        $request  = new Request();
        $response = new Response();
        $user     = new UserVO();

        $request->attributes->set('user', $user);

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with('info', $this->isType('string'), $this->isType('array'));

        $this->subject->processResponse($request, $response);
    }
}
