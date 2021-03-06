<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Middleware\Session;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session  as SessionModel;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Middleware\Session
 */
class SessionTest extends TestCase
{

    /**
     * @var Session
     */
    private $subject;

    /**
     * @var SessionModel|MockObject
     */
    private $session;

    public function setUp()
    {
        $this->session = $this->createMock(SessionModel::class);

        $this->subject = new Session($this->session);
    }

    public function testProcessRequest()
    {
        $request = new Request();
        $route   = new Route('/route/');

        $this->subject->processRequest($request, $route);

        $this->assertInstanceOf(SessionInterface::class, $request->getSession());
    }
}
