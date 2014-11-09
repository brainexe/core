<?php

namespace BrainExe\Tests\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\DependencyInjection\CompilerPass\TranslationCompilerPass;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TranslationCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TranslationCompilerPass
	 */
	private $_subject;

	/**
	 * @var ContainerBuilder|PHPUnit_Framework_MockObject_MockObject $container
	 */
	private $_mock_container;

	public function setUp() {
		$this->_subject = new TranslationCompilerPass();
		$this->_mock_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
	}

	public function testProcessWithInvalidRoot() {
		$this->_subject->process($this->_mock_container);
	}

} 
