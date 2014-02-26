<?php

namespace Matze\Core\Application;

use Matze\Core\Controller\ControllerInterface;
use Matze\Core\Traits\LoggerTrait;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Parser;

/**
 * @Service
 */
class AppKernel {

	use LoggerTrait;

	/**
	 * @var Request
	 */
	private $_request;

	/**
	 * @var RouteCollection
	 */
	private $_routes;

	/**
	 * @var ControllerResolver
	 */
	private $_resolver;

	/**
	 * @Inject({"@ControllerResolver", "@RouteCollection"})
	 */
	public function __construct(ControllerResolver $container_resolver, RouteCollection $routes) {
		$this->_resolver = $container_resolver;
		$this->_routes = $routes;
	}

	/**
	 * @param Request $request
	 */
	public function handle(Request $request) {
		$this->_request = $request;
		try {
			$this->matchRoute();
			$response = $this->_loadResource();
			if (is_string($response)) {
				$response = new Response($response);
			}
		} catch (ResourceNotFoundException $e) {
			$response = new Response();
			$response->setStatusCode(404);
		} catch (MethodNotAllowedException $e) {
			$response = new Response();
			$response->setStatusCode(405);
		}

		$response->prepare($this->_request);

		$start_time = $_SERVER['REQUEST_TIME_FLOAT'];
		$diff = microtime(true) - $start_time;
		$this->debug(sprintf('Response time: %0.2fms', $diff*1000));

		$response->send();
	}

	/**
	 * @return Response
	 */
	private function _loadResource() {
		/** @var ControllerInterface[] $callable */
		$callable = $this->_resolver->getController($this->_request);
		$arguments = $this->_resolver->getArguments($this->_request, $callable);

		$response = call_user_func_array($callable, $arguments);

		return $response;
	}

	private function matchRoute() {
		$context = new RequestContext();
		$context->fromRequest($this->_request);
		$matcher = new UrlMatcher($this->_routes, $context);

		$attributes = $matcher->match($this->_request->getPathInfo());
		$this->_request->attributes->add($attributes);
	}

}
