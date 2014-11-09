<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\Core;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class CoreTest extends PHPUnit_Framework_TestCase {

	public function testGetContainer() {
		$dic = Core::rebuildDIC();
		$this->assertTrue($dic instanceof Container);
	}
} 