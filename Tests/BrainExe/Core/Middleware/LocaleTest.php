<?php

namespace Tests\BrainExe\Core\Middleware;

use BrainExe\Core\Application\Locale as LocaleModel;
use BrainExe\Core\Middleware\Locale;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @covers \BrainExe\Core\Middleware\Locale
 */
class LocaleTest extends TestCase
{

    /**
     * @var Locale
     */
    private $subject;

    /**
     * @var LocaleModel|MockObject
     */
    private $locale;

    public function setUp()
    {
        $this->locale  = $this->createMock(LocaleModel::class);
        $this->subject = new Locale($this->locale);
    }

    public function testProcessRequestWithLocaleInQuery()
    {
        $request   = new Request();
        $route     = new Route('/route/');
        $session   = new Session(new MockArraySessionStorage());

        $request->setSession($session);
        $request->query->set('locale', 'en_EN');

        $this->locale
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_EN');

        $this->locale
            ->expects($this->once())
            ->method('getLocales')
            ->willReturn(['en_EN', 'de_DE']);

        $this->subject->processRequest($request, $route);
    }

    public function testProcessRequestWithInvalidLocaleInQuery()
    {
        $request    = new Request();
        $route      = new Route('/route/');
        $session    = new Session(new MockArraySessionStorage());

        $request->setSession($session);
        $request->query->set('locale', 'fo_ba');

        $this->locale
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_EN');

        $this->locale
            ->expects($this->once())
            ->method('getLocales')
            ->willReturn(['en_EN', 'de_DE']);

        $this->subject->processRequest($request, $route);
    }

    public function testProcessRequestWithoutLocaleInQuery()
    {
        $request    = new Request();
        $route      = new Route('/route/');
        $session    = new Session(new MockArraySessionStorage());

        $request->setSession($session);

        $this->subject->processRequest($request, $route);

        $actual = $request->attributes->get('locale');

        $this->assertNull($actual);
    }

    public function testProcessResponse()
    {
        $request   = new Request();
        $response  = new Response();

        $this->subject->processResponse($request, $response);

        $this->assertEquals('', $response->getContent());
    }
}
