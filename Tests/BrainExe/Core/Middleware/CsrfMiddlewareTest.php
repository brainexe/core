<?php

namespace Tests\BrainExe\Core\Middleware\CsrfMiddleware;

use BrainExe\Core\Middleware\CsrfMiddleware;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

class CsrfMiddlewareTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CsrfMiddleware
     */
    private $subject;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;

    public function setUp()
    {
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new CsrfMiddleware();
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testProcessGetRequestWithoutToken()
    {
        $currentCsrf  = '';
        $newCsrf     = 'random';
        $session      = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('GET');
        $request->cookies->set(CsrfMiddleware::CSRF, $currentCsrf);

        $response  = new Response();
        $route     = new Route('/route/');
        $routeName = null;

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route, $routeName);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(CsrfMiddleware::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(CsrfMiddleware::CSRF));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\MethodNotAllowedException
     * @expectedExceptionMessage invalid CSRF token
     */
    public function testProcessPostRequestInvalidToken()
    {
        $currentCsrf   = 'incorrect';
        $expectedToken = 'expected';
        $newCsrf       = 'new token';
        $session       = new Session(new MockArraySessionStorage());
        $session->set(CsrfMiddleware::CSRF, $expectedToken);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(CsrfMiddleware::CSRF, $currentCsrf);

        $response = new Response();
        $route      = new Route('/route/');
        $routeName = null;

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route, $routeName);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(CsrfMiddleware::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(CsrfMiddleware::CSRF));
    }

    public function testProcessPostRequestValidToken()
    {
        $currentCsrf = 'token';
        $newCsrf     = 'new token';
        $expectedToken = $currentCsrf;

        $session = new Session(new MockArraySessionStorage());
        $session->set(CsrfMiddleware::CSRF, $expectedToken);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(CsrfMiddleware::CSRF, $currentCsrf);

        $response   = new Response();
        $route      = new Route('/route/');
        $routeName = null;

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route, $routeName);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(CsrfMiddleware::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(CsrfMiddleware::CSRF));
    }
}
