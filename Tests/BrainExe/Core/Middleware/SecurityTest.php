<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Application\Locale as LocaleModel;
use BrainExe\Core\Middleware\Locale;
use BrainExe\Core\Middleware\Security;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Middleware\Security
 */
class SecurityTest extends TestCase
{

    /**
     * @var Security
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Security(['socket.localhost:8080'], false);
    }

    public function testProcessResponse()
    {
        $this->subject = new Security(['socket.localhost:8080'], false);

        $request  = new Request();
        $response = new Response();

        $request->server->set('HTTPS', 'on');

        $this->subject->processResponse($request, $response);

        $expectedCSP = "default-src 'self'; img-src *; style-src 'self' 'unsafe-inline'; connect-src 'self' 'self' socket.localhost:8080";
        $this->assertEquals($expectedCSP, $response->headers->get('Content-Security-Policy'));
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertTrue($response->headers->has('Strict-Transport-Security'));
    }

    public function testProcessResponseRelativeSocketServer()
    {
        $this->subject = new Security(['/socket'], false);

        $request  = new Request();
        $response = new Response();

        $request->headers->set('HOST', 'my.host.de');
        $request->server->set('HTTPS', 'on');

        $this->subject->processResponse($request, $response);

        $expectedCSP = "default-src 'self'; img-src *; style-src 'self' 'unsafe-inline'; connect-src 'self' 'self' my.host.de";
        $this->assertEquals($expectedCSP, $response->headers->get('Content-Security-Policy'));
    }

    public function testProcessAjaxResponse()
    {
        $request  = new Request();
        $response = new Response();

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->subject->processResponse($request, $response);

        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('X-Frame-Options'));
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }
}
