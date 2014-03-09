<?php

namespace Matze\Tests\Core;

use Matze\Core\Core;
use Symfony\Component\DependencyInjection\Container;

class CoreTest extends \PHPUnit_Framework_TestCase {

	public function testGetContainer() {
		$dic = Core::rebuildDIC();
		$this->assertTrue($dic instanceof Container);
	}
} 