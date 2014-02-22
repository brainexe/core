<?php

namespace Matze\Core\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController {
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Response;
	 */
	protected $response;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Inject the Request object for further use.
	 *
	 * @param Request $request
	 */
	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * Inject the configuration.
	 *
	 * @param $config array
	 */
	public function setConfiguration($config) {
		$this->config = $config;
	}

	/**
	 * Initializer function to be used by child classes.
	 */
	public function init() {
	}
}