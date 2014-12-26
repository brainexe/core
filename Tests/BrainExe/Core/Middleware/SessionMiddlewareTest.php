<?php

namespace Tests\BrainExe\Core\Middleware\SessionMiddleware;

use BrainExe\Core\Middleware\SessionMiddleware;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\SessionMiddleware
 */
class SessionMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SessionMiddleware
     */
    private $subject;

    /**
     * @var Session|MockObject
     */
    private $mockSession;

    public function setUp()
    {
        $this->mockSession = $this->getMock(Session::class, [], [], '', false);

        $this->subject = new SessionMiddleware($this->mockSession);
    }

    public function testProcessRequest()
    {
        $request    = new Request();
        $route      = new Route('/route/');
        $routeName = null;

        $this->subject->processRequest($request, $route, $routeName);

        $this->assertInstanceOf(SessionInterface::class, $request->getSession());
    }
}
