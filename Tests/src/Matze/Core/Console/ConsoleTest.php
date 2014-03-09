<?php

namespace Matze\Tests\Core\Application;

use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsoleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Console
	 */
	private $_subject;

	public function setUp() {
		/** @var $dic ContainerBuilder */
		global $dic;

		$this->_subject = $dic->get('Console');
	}

	public function testBoot() {

	}
}
