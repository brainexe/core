<?php

namespace Matze\Core\Application;

use Exception;
use Matze\Core\Controller\ControllerInterface;
use Matze\Core\Middleware\MiddlewareInterface;
use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @Service
 */
class AppKernel {

	/**
	 * @var RouteCollection
	 */
	private $_routes;

	/**
	 * @var ControllerResolver
	 */
	private $_resolver;

	/**
	 * @var MiddlewareInterface[]
	 */
	private $_middlewares;

	/**
	 * @Inject({"@ControllerResolver", "@RouteCollection"})
	 */
	public function __construct(ControllerResolver $container_resolver, RouteCollection $routes) {
		$this->_resolver = $container_resolver;
		$this->_routes = $routes;
	}

	/**
	 * @param MiddlewareInterface $middleware
	 */
	public function addMiddleware(MiddlewareInterface $middleware) {
		$this->_middlewares[] = $middleware;
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function handle(Request $request) {
		$response = null;

		try {
			$response = $this->_handleRequest($request);

			if (is_string($response)) {
				$response = new Response($response);
			}

		} catch (Exception $e) {
			if (empty($response)) {
				$response = new Response();
			}
			foreach ($this->_middlewares as $middleware) {
				$middleware->processException($request, $response, $e);
			}
		}

		$middleware_count = count($this->_middlewares);
		for ($i = $middleware_count-1; $i >= 0; $i--) {
			$middleware = $this->_middlewares[$i];
			$middleware->processResponse($request, $response);
		}

		$response->prepare($request);
		$response->send();

		return $response;
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	private function _handleRequest(Request $request) {
		$context = new RequestContext();
		$context->fromRequest($request);
		$matcher = new UrlMatcher($this->_routes, $context);

		$attributes = $matcher->matchRequest($request);
		$request->attributes->add($attributes);

		$route = $this->_routes->get($attributes['_route']);
		foreach ($this->_middlewares as $middleware) {
			$response = $middleware->processRequest($request, $route, $attributes['_route']);
			if ($response) {
				return $response;
			}
		}

		/** @var ControllerInterface[] $callable */
		$callable = $this->_resolver->getController($request);
		$arguments = $this->_resolver->getArguments($request, $callable);

		$response = call_user_func_array($callable, $arguments);

		return $response;
	}

}
