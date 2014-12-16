<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RebuildTest extends PHPUnit_Framework_TestCase {

	public function testRebuildContainer() {
		$subject = new Rebuild();

		$actual_result = $subject->rebuildDIC(false);

		$this->assertInstanceOf(ContainerBuilder::class, $actual_result);
	}

	public function testRebuildWithBootContainer() {
		$this->markTestIncomplete();
		$subject = new Rebuild();

		$actual_result = $subject->rebuildDIC(true);

		$this->assertInstanceOf(Container::class, $actual_result);
	}
}
