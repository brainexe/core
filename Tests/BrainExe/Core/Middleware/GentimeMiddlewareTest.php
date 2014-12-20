<?php

namespace Tests\BrainExe\Core\Middleware\GentimeMiddleware;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Middleware\GentimeMiddleware;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\GentimeMiddleware
 */
class GentimeMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var GentimeMiddleware
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $mockLogger;

    public function setUp()
    {
        $this->mockLogger = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new GentimeMiddleware();
        $this->subject->setLogger($this->mockLogger);
    }

    public function testProcessResponse()
    {
        $request = new Request();
        $response = new Response();

        $this->mockLogger
        ->expects($this->once())
        ->method('log')
        ->with('info', $this->isType('string'), ['channel' => 'gentime']);

        $this->subject->processResponse($request, $response);
    }

    public function testProcessResponseWithUser()
    {
        $request  = new Request();
        $response = new Response();
        $user     = new UserVO();

        $request->attributes->set('user', $user);

        $this->mockLogger
        ->expects($this->once())
        ->method('log')
        ->with('info', $this->isType('string'), ['channel' => 'gentime']);

        $this->subject->processResponse($request, $response);
    }
}
