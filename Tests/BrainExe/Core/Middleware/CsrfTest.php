<?php

namespace Tests\BrainExe\Core\Middleware\CsrfMiddleware;

use BrainExe\Core\Middleware\Csrf;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

class CsrfTest extends TestCase
{

    /**
     * @var Csrf
     */
    private $subject;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->idGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new Csrf();
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testProcessGetRequestWithoutToken()
    {
        $currentCsrf  = '';
        $newCsrf = 'random';
        $session      = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('GET');
        $request->cookies->set(Csrf::CSRF, $currentCsrf);

        $response  = new Response();
        $route     = new Route('/route/');

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(Csrf::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(Csrf::CSRF));
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
        $session->set(Csrf::CSRF, $expectedToken);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(Csrf::CSRF, $currentCsrf);

        $response = new Response();
        $route    = new Route('/route/');

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(Csrf::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(Csrf::CSRF));
    }

    public function testProcessPostRequestValidToken()
    {
        $currentCsrf = 'token';
        $newCsrf     = 'new token';
        $expectedToken = $currentCsrf;

        $session = new Session(new MockArraySessionStorage());
        $session->set(Csrf::CSRF, $expectedToken);

        $request = new Request();
        $request->setSession($session);
        $request->setMethod('POST');
        $request->cookies->set(Csrf::CSRF, $currentCsrf);

        $response   = new Response();
        $route      = new Route('/route/');

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($newCsrf);

        $this->subject->processRequest($request, $route);
        $this->subject->processResponse($request, $response);

        $expectedCookie = new Cookie(Csrf::CSRF, $newCsrf);

        $this->assertEquals([$expectedCookie], $response->headers->getCookies());
        $this->assertEquals($newCsrf, $session->get(Csrf::CSRF));
    }
}
