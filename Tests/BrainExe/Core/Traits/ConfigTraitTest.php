<?php

use BrainExe\Core\Traits\ConfigTrait;
use BrainExe\Core\Util\Config;
use Symfony\Component\DependencyInjection\Container;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ConfigTest {
	use ConfigTrait;

	public function get($key) {
		return $this->getParameter($key);
	}
}

class ConfigTraitTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigTrait
	 */
	private $_subject;

	/**
	 * @var Container|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockContainer;

	public function setUp() {
		$this->_mockContainer = $this->getMock(Container::class);

		$this->_subject = new ConfigTest();
		$this->_subject->setContainer($this->_mockContainer);
	}

	public function testGetConfig() {
		$key   = 'key';
		$value = 'value';

		$this->_mockContainer
			->expects($this->once())
			->method('getParameter')
			->with($key)
			->willReturn($value);

		$actual_result = $this->_subject->get($key);

		$this->assertEquals($value, $actual_result);
	}
}
