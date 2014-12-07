<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Middleware\MiddlewareInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * @Service
 */
class AppKernel implements HttpKernelInterface {

	/**
	 * @var RouteCollection
	 */
	private $routes;

	/**
	 * @var ControllerResolver
	 */
	private $resolver;

	/**
	 * @var MiddlewareInterface[]
	 */
	private $_middlewares;

	/**
	 * @var UrlMatcher
	 */
	private $urlMatcher;

	/**
	 * @Inject({"@ControllerResolver", "@RouteCollection", "@UrlMatcher"})
	 * @param ControllerResolver $container_resolver
	 * @param RouteCollection $routes
	 * @param UrlMatcher $urlMatcher
	 */
	public function __construct(ControllerResolver $container_resolver, RouteCollection $routes, UrlMatcher $urlMatcher) {
		$this->resolver = $container_resolver;
		$this->routes   = $routes;
		$this->urlMatcher = $urlMatcher;
	}

	/**
	 * @param MiddlewareInterface[] $middlewares
	 */
	public function setMiddlewares(array $middlewares) {
		$this->_middlewares = $middlewares;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {
		$response = null;

		try {
			$response = $this->_handleRequest($request);
		} catch (Exception $exception) {
			foreach ($this->_middlewares as $middleware) {
				$response = $middleware->processException($request, $exception);
				if ($response !== null) {
					break;
				}
			}
		}

		$response = $this->_prepareResponse($request, $response);

		$middleware_idx = count($this->_middlewares) - 1;
		for ($i = $middleware_idx; $i >= 0; $i--) {
			$middleware = $this->_middlewares[$i];
			$middleware->processResponse($request, $response);
		}

		return $response;
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	private function _handleRequest(Request $request) {
		$attributes = $this->urlMatcher->match($request);

		$request->attributes->add($attributes);

		$route_name = $attributes['_route'];
		$route      = $this->routes->get($route_name);

		foreach ($this->_middlewares as $middleware) {
			$response = $middleware->processRequest($request, $route, $route_name);
			if ($response) {
				// e.g. RedirectResponse or rendered error page
				return $response;
			}
		}

		/** @var callable $callable */
		$callable  = $this->resolver->getController($request);
		$arguments = $this->resolver->getArguments($request, $callable);

		return call_user_func_array($callable, $arguments);
	}

	/**
	 * @param Request $request
	 * @param Response|mixed $response
	 * @return Response
	 */
	private function _prepareResponse(Request $request, $response) {
		if (!$response instanceof Response) {
			// todo support more content types
			return new JsonResponse($response);
		}

		return $response;
	}

}
