<?php

namespace Matze\Core\Application;

use Matze\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class AppKernel {
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $routes;

	/**
	 * @param Request $request
	 * @param array $routes
	 */
	public function __construct(Request $request, $routes = array()) {
		$this->request = $request;
		$this->routes = $routes;
	}

	public function handle() {
		try {
			$this->matchRoute();
			$response = $this->loadResource();
		} catch (ResourceNotFoundException $e) {
			$response = new Response();
			$response->setStatusCode(404);
		} catch (MethodNotAllowedException $e) {
			$response = new Response();
			$response->setStatusCode(405);
		}

		$response->prepare($this->request);
		$response->send();
	}

	private function loadResource() {
		$resolver = new ControllerResolver();

		/** @var AbstractController[] $controller */
		$controller = $resolver->getController($this->request);
		$arguments = $resolver->getArguments($this->request, $controller);

		$controller[0]->setRequest($this->request);
		$controller[0]->init();

		$response = call_user_func_array($controller, $arguments);
		return $response;
	}

	private function matchRoute() {
		$routes = new RouteCollection();

		foreach ($this->routes as $key => $route) {
			if (!empty($route['requirements'])) {
				$routes->add($key, new Route($route['pattern'], $route['defaults'], $route['requirements']));
			} else {
				$routes->add($key, new Route($route['pattern'], $route['defaults']));
			}
		}

		$context = new RequestContext();
		$context->fromRequest($this->request);
		$matcher = new UrlMatcher($routes, $context);

		$attributes = $matcher->match($this->request->getPathInfo());
		$this->request->attributes->add($attributes);
	}
}