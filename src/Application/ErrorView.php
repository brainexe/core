<?php

namespace BrainExe\Core\Application;

use Exception;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Service
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
	 * @param boolean $debug
	 * @param string $error_template
	 */
	public function __construct($debug, $error_template) {
		$this->_value_debug          = $debug;
		$this->_value_error_template = $error_template;
	}

	/**
	 * @param Request $request
	 * @param Exception $exception
	 * @return string
	 */
	public function renderException(Request $request, Exception $exception) {
		$content = $this->render($this->_value_error_template, [
			'exception' => $exception,
			'debug' => $this->_value_debug,
			'request' => $request,
			'current_user' => $request->attributes->get('user') ?: new AnonymusUserVO(),
		]);

		return $content;
	}
} 
