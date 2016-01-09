<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Middleware\Stats;

use BrainExe\Core\Stats\MultiEvent;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

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
        $request->attributes->set('_route', 'route');
        $request->attributes->set('user_id', 42);

        $event = new MultiEvent(MultiEvent::INCREASE, [
            'request:route:route' => 1,
            'response:code:200'   => 1,
            'request:user:42'     => 1
        ]);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->processResponse($request, $response);
    }

    public function testProcessException()
    {
        $request   = new Request();
        $exception = new Exception('test');

        $event = new MultiEvent(MultiEvent::INCREASE, [
            'response:code:500'    => 1,
            'exception:Exception' => 1,
        ]);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->processException($request, $exception);
    }
}
