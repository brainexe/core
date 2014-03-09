<?php

namespace Matze\Tests\Core\Application;

use Matze\Core\Application\ErrorView;
use Matze\Core\Application\UserException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ErrorViewTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ErrorView
	 */
	private $_subject;

	private $_value_debug = true;
	private $_value_error_template = 'error.html.twig';

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|Twig_Environment
	 */
	private $_mock_twig;

	public function setup() {
		$this->_mock_twig = $this->getMock('Twig_Environment');

		$this->_subject = new ErrorView($this->_value_debug, $this->_value_error_template);
		$this->_subject->setTwig($this->_mock_twig);
	}

	public function testRender() {
		$exception = new UserException('Test-Exception');
		$request = new Request();
		$expected_content = 'Exception...';

		$this->_mock_twig
			->expects($this->once())
			->method('render')
			->with($this->_value_error_template, [
				'debug' => $this->_value_debug,
				'exception' => $exception,
				'request' => $request
			])
			->will($this->returnValue($expected_content));

		$response = $this->_subject->renderException($request, $exception);

		$this->assertEquals($expected_content, $response);
	}
} 
