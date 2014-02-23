<?php

namespace Matze\Core\Application;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface {

	/**
	 * @var Container
	 */
	private $_container;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container) {
		$this->_container = $container;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getController(Request $request) {
		if (!$controller = $request->attributes->get('_controller')) {
			return false;
		}

		if (false === strpos($controller, '::')) {
			throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
		}

		list($service_id, $method) = explode('::', $controller, 2);

		$service = $this->_container->get(sprintf('Controller.%s', $service_id));

		$callable = [$service, $method];

		if (!is_callable($callable)) {
			throw new \InvalidArgumentException(sprintf('The controller for URI "%s" is not callable.', $request->getPathInfo()));
		}

		return $callable;
	}

	public function getArguments(Request $request, $controller) {
		if (is_array($controller)) {
			$r = new \ReflectionMethod($controller[0], $controller[1]);
		} elseif (is_object($controller) && !$controller instanceof \Closure) {
			$r = new \ReflectionObject($controller);
			$r = $r->getMethod('__invoke');
		} else {
			$r = new \ReflectionFunction($controller);
		}

		return $this->doGetArguments($request, $controller, $r->getParameters());
	}

	protected function doGetArguments(Request $request, $controller, array $parameters) {
		$attributes = $request->attributes->all();
		$arguments = array();

		foreach ($parameters as $param) {
			if (array_key_exists($param->name, $attributes)) {
				$arguments[] = $attributes[$param->name];
			} elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
				$arguments[] = $request;
			} elseif ($param->isDefaultValueAvailable()) {
				$arguments[] = $param->getDefaultValue();
			} else {
				if (is_array($controller)) {
					$repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
				} elseif (is_object($controller)) {
					$repr = get_class($controller);
				} else {
					$repr = $controller;
				}

				throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
			}
		}

		return $arguments;
	}
}