<?php

namespace Tests\BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\TwigExtensionDefinitionBuilder;
use BrainExe\Core\Annotations\TwigExtension;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

class TwigExtensionTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TwigExtension
	 */
	private $subject;

	public function __construct() {
		$this->subject = new TwigExtension([]);
	}

	public function testGetBuilder() {
		/** @var MockObject|Reader $reader */
		$reader = $this->getMock(Reader::class);

		$actual_result = $this->subject->getBuilder($reader);

		$this->assertInstanceOf(TwigExtensionDefinitionBuilder::class, $actual_result);
	}

}
