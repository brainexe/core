<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Middleware\MiddlewareInterface;
use BrainExe\Core\Traits\LoggerTrait;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @Service
 */
class AppKernel implements HttpKernelInterface {

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
	 * @param ControllerResolver $container_resolver
	 * @param RouteCollection $routes
	 */
	public function __construct(ControllerResolver $container_resolver, RouteCollection $routes) {
		$this->_resolver = $container_resolver;
		$this->_routes   = $routes;

		include_once ROOT . 'cache/router_matcher.php';
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
		$context = new RequestContext();
		$context->fromRequest($request);

		$url_matcher = new \ProjectUrlMatcher($context);

		$attributes = $url_matcher->matchRequest($request);
		$request->attributes->add($attributes);

		$route_name = $attributes['_route'];
		$route      = $this->_routes->get($route_name);

		foreach ($this->_middlewares as $middleware) {
			$response = $middleware->processRequest($request, $route, $route_name);
			if ($response) {
				// e.g. RedirectResponse or rendered error page
				return $response;
			}
		}

		/** @var callable $callable */
		$callable  = $this->_resolver->getController($request);
		$arguments = $this->_resolver->getArguments($request, $callable);

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
