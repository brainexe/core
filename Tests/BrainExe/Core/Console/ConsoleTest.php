<?php

namespace BrainExe\Tests\Core\Application;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsoleTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Application
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
