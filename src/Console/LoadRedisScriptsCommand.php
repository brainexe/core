<?php

namespace Matze\Core\Console;

use Matze\Core\Redis\RedisScripts;
use Matze\Core\Traits\RedisTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class LoadRedisScriptsCommand extends AbstractCommand {

	use RedisTrait;

	/**
	 * @var RedisScripts
	 */
	private $_redis_scripts;

	/**
	 * @Inject({"@RedisScripts"})
	 */
	public function __construct(RedisScripts $redis_scripts) {
		parent::__construct();

		$this->_redis_scripts = $redis_scripts;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('redis:scripts:load')
			->setDescription('Load Redis Scrips');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$redis = $this->getRedis();

		foreach ($this->_redis_scripts->getAllScripts() as $sha1 => $script) {
			if ($redis->script('EXISTS', $sha1)[0]) {
				if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
					$output->writeln(sprintf("Script %s was already loaded", $sha1));
				}
			} else {
				if (!$redis->script('LOAD', $script)) {
					$output->writeln(sprintf('<error>Error in %s</error>', $redis->getLastError()));
					$output->writeln(sprintf('<error>%s</error>', $script));
				} elseif (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
					$output->writeln(sprintf("Loaded script %s (%s)", $sha1, $script));
				}
			}
		}
	}

} 
