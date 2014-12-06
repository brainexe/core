<?php

namespace BrainExe\Tests\Core\Logger;

use BrainExe\Core\Logger\ChannelStreamHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase;

class ChannelStreamHandlerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ChannelStreamHandler
	 */
	private $subject;

	public function setup() {
		$stream = 'stream';
		$channel = 'channel';

		$this->subject = new ChannelStreamHandler($stream, Logger::INFO, $channel);
	}

	/**
	 * @dataProvider provideIsHandling
	 * @param array $record
	 * @param string $channel
	 * @param bool $expected_result
	 */
	public function testIsHandling(array $record, $channel, $expected_result) {
		$this->subject->setChannel($channel);

		$actual_result = $this->subject->isHandling($record);

		$this->assertEquals($expected_result, $actual_result);

	}

	public function provideIsHandling() {
		return [
			[['level' => Logger::DEBUG], 'channel', false],
			[['level' => Logger::INFO, 'context' => ['channel' => true]], '', false],
			[['level' => Logger::INFO, 'context' => ['channel' => false]], '', true],
			[['level' => Logger::INFO, 'context' => ['channel' => false]], 'context', false],
			[['level' => Logger::INFO, 'context' => ['channel' => 'context']], 'context', true],
			[['level' => Logger::INFO, 'context' => ['channel' => 'wrongcontext']], 'context', false],
		];
	}
}
