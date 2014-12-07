<?php

namespace Tests\BrainExe\Core\Middleware\LocaleMiddleware;

use BrainExe\Core\Application\Locale;
use BrainExe\Core\Middleware\LocaleMiddleware;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Route;

/**
 * @Covers BrainExe\Core\Middleware\LocaleMiddleware
 */
class LocaleMiddlewareTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LocaleMiddleware
	 */
	private $subject;

	/**
	 * @var Locale|MockObject
	 */
	private $mockLocale;

	public function setUp() {
		$this->mockLocale = $this->getMock(Locale::class);

		$this->subject = new LocaleMiddleware($this->mockLocale);
	}

	public function testProcessRequestWithLocaleInQuery() {
		$request    = new Request();
		$route      = new Route('/route/');
		$session    = new Session(new MockArraySessionStorage());
		$route_name = null;

		$request->setSession($session);
		$request->query->set('locale', 'en_EN');

		$this->mockLocale
			->expects($this->once())
			->method('setLocale')
			->with('en_EN');

		$this->mockLocale
			->expects($this->once())
			->method('getLocales')
			->willReturn(['en_EN', 'de_DE']);

		$this->subject->processRequest($request, $route, $route_name);
	}
	public function testProcessRequestWithInvalueLocaleInQuery() {
		$request    = new Request();
		$route      = new Route('/route/');
		$session    = new Session(new MockArraySessionStorage());
		$route_name = null;

		$request->setSession($session);
		$request->query->set('locale', 'fo_ba');

		$this->mockLocale
			->expects($this->once())
			->method('setLocale')
			->with('en_EN');

		$this->mockLocale
			->expects($this->once())
			->method('getLocales')
			->willReturn(['en_EN', 'de_DE']);

		$this->subject->processRequest($request, $route, $route_name);
	}

	public function testProcessRequestWithoutLocaleInQuery() {
		$request    = new Request();
		$route      = new Route('/route/');
		$session    = new Session(new MockArraySessionStorage());
		$route_name = null;

		$request->setSession($session);

		$this->subject->processRequest($request, $route, $route_name);
	}

	public function testProcessResponse() {
		$request   = new Request();
		$response  = new Response();

		$this->subject->processResponse($request, $response);

		$this->assertEquals('', $response->getContent());
	}
}
