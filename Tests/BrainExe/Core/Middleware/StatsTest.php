<?php

namespace Tests\BrainExe\Core\Middleware\GentimeMiddleware;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Middleware\Gentime;
use BrainExe\Core\Middleware\Parameter;
use BrainExe\Core\Middleware\Stats;
use BrainExe\Core\Stats\Event;
use Exception;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Tests\BrainExe\Core\EventDispatcher\EventDispatcher\EventDispatcherTest;

/**
 * @covers BrainExe\Core\Middleware\Stats
 */
class StatsTest extends TestCase
{

    /**
     * @var Stats
     */
    private $subject;

    /**
     * @var MockObject|EventDispatcher
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Stats();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testProcessGetRequest()
    {
        $route   = new Route('/route/');
        $request = new Request();
        $request->setMethod('GET');

        $this->subject->processRequest($request, $route);

        $this->assertEquals([], $request->request->all());
    }

    public function testProcessResponse()
    {
        $request  = new Request();
        $response = new Response();

        $event = new Event(Event::INCREASE, 'request:handle');
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->processResponse($request, $response);
    }

    public function testProcessException()
    {
        $request   = new Request();
        $exception = new Exception();

        $event = new Event(Event::INCREASE, 'request:error');
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->processException($request, $exception);
    }
}