<?php

namespace Matze\Core\Application;

use Exception;
use Matze\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Service(public=false)
 */
class ErrorView {

	use TwigTrait;

	/**
	 * @var boolean
	 */
	private $_value_debug;

	/**
	 * @var string
	 */
	private $_value_error_template;

	/**
	 * @Inject({"%debug%", "%application.error_template%"})
	 */
	public function __construct($debug, $error_template) {
		$this->_value_debug = $debug;
		$this->_value_error_template = $error_template;
	}

	/**
	 * @param Request $request
	 * @param Exception $e
	 * @return string
	 */
	public function renderException(Request $request, Exception $e) {
		$content = $this->render($this->_value_error_template, [
			'exception' => $e,
			'debug' => $this->_value_debug,
			'request' => $request,
		]);

		return $content;
	}
} 
