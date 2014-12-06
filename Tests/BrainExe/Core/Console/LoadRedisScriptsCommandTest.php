<?php

namespace Tests\BrainExe\Core\Console\LoadRedisScriptsCommand;

use BrainExe\Core\Console\LoadRedisScriptsCommand;
use BrainExe\Core\Redis\RedisScripts;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Redis;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class LoadRedisScriptsCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LoadRedisScriptsCommand
	 */
	private $_subject;

	/**
	 * @var RedisScripts|MockObject
	 */
	private $_mockRedisScripts;

	/**
	 * @var Redis|MockObject
	 */
	private $_mockRedis;

	public function setUp() {
		$this->_mockRedisScripts = $this->getMock(RedisScripts::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new LoadRedisScriptsCommand($this->_mockRedisScripts);
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testExecute() {
		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);
		$scripts = [
			$sha1_1 = 'hash_1' => $script_1 = 'script 1',
			$sha1_2 = 'hash_2' => $script_2 = 'script 2',
			$sha1_3 = 'hash_3' => $script_3 = 'script 3',
		];

		$this->_mockRedisScripts
			->expects($this->once())
			->method('getAllScripts')
			->will($this->returnValue($scripts));

		$this->_mockRedis
			->expects($this->at(0))
			->method('script')
			->with('EXISTS', $sha1_1)
			->will($this->returnValue([0 =>'Already Loaded']));

		$this->_mockRedis
			->expects($this->at(1))
			->method('script')
			->with('EXISTS', $sha1_2)
			->will($this->returnValue([0 => null]));

		$this->_mockRedis
			->expects($this->at(2))
			->method('script')
			->with('LOAD', $script_2)
			->will($this->returnValue(true));

		$this->_mockRedis
			->expects($this->at(3))
			->method('script')
			->with('EXISTS', $sha1_3)
			->will($this->returnValue([0 => null]));

		$this->_mockRedis
			->expects($this->at(4))
			->method('script')
			->with('LOAD', $script_3)
			->will($this->returnValue(false));

		$this->_mockRedis
			->expects($this->at(5))
			->method('getLastError')
			->will($this->returnValue('error'));

		$commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
		$output = $commandTester->getDisplay();

		$expected_result = "Load Redis Scrips...
Script hash_1 was already loaded
Loaded script hash_2 (script 2)
Error: error
script 3
done in";

		$this->assertStringStartsWith($expected_result, $output);
	}

}
