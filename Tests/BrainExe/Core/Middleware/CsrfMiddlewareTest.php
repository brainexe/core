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
        $current_csrf = '';
        $new_csrf     = 'random';
        $session      = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('GET');
        $request->cookies->set(CsrfMiddleware::CSRF, $current_csrf);

        $response  = new Response();
        $route      = new Route('/route/');
        $route_name = null;

        $this->mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomId')
        ->will($this->returnValue($new_csrf));

        $this->subject->processRequest($request, $route, $route_name);
        $this->subject->processResponse($request, $response);

        $expected_cookie = new Cookie(CsrfMiddleware::CSRF, $new_csrf);

        $this->assertEquals([$expected_cookie], $response->headers->getCookies());
        $this->assertEquals($new_csrf, $session->get(CsrfMiddleware::CSRF));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\MethodNotAllowedException
     * @expectedExceptionMessage invalid CSRF token
     */
    public function testProcessPostRequestInvalidToken()
    {
        $current_csrf = 'incorrect';
        $expected_token = 'expected';
        $new_csrf     = 'new token';
        $session      = new Session(new MockArraySessionStorage());
        $session->set(CsrfMiddleware::CSRF, $expected_token);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(CsrfMiddleware::CSRF, $current_csrf);

        $response = new Response();
        $route      = new Route('/route/');
        $route_name = null;

        $this->mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomId')
        ->will($this->returnValue($new_csrf));

        $this->subject->processRequest($request, $route, $route_name);
        $this->subject->processResponse($request, $response);

        $expected_cookie = new Cookie(CsrfMiddleware::CSRF, $new_csrf);

        $this->assertEquals([$expected_cookie], $response->headers->getCookies());
        $this->assertEquals($new_csrf, $session->get(CsrfMiddleware::CSRF));
    }

    public function testProcessPostRequestValidToken()
    {
        $current_csrf = 'token';
        $new_csrf     = 'new token';
        $expected_token = $current_csrf;

        $session = new Session(new MockArraySessionStorage());
        $session->set(CsrfMiddleware::CSRF, $expected_token);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(CsrfMiddleware::CSRF, $current_csrf);

        $response   = new Response();
        $route      = new Route('/route/');
        $route_name = null;

        $this->mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomId')
        ->will($this->returnValue($new_csrf));

        $this->subject->processRequest($request, $route, $route_name);
        $this->subject->processResponse($request, $response);

        $expected_cookie = new Cookie(CsrfMiddleware::CSRF, $new_csrf);

        $this->assertEquals([$expected_cookie], $response->headers->getCookies());
        $this->assertEquals($new_csrf, $session->get(CsrfMiddleware::CSRF));
    }
}
