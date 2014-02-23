<?php

namespace Matze\Core\Application;

use DirectoryIterator;
use Matze\Core\Controller\AbstractController;
use Matze\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Yaml\Parser;

class AppKernel {
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $routes = [];

	/**
	 * @var ControllerResolver
	 */
	private $_resolver;

	/**
	 * @param Request $request
	 * @param Container $container
	 */
	public function __construct(Request $request, Container $container) {
		$this->_resolver = new ControllerResolver($container);

		$this->routes = include ROOT. '/cache/routes.php';

		$this->request = $request;
	}

	public function handle() {
		try {
			$this->matchRoute();
			$response = $this->loadResource();
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

		$response->prepare($this->request);
		$response->send();
	}

	private function loadResource() {
		/** @var ControllerInterface[] $callable */
		$callable = $this->_resolver->getController($this->request);
		$arguments = $this->_resolver->getArguments($this->request, $callable);

		$response = call_user_func_array($callable, $arguments);
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