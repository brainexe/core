<?php

namespace Matze\Core\EventDispatcher;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class ClearCacheEvent extends Event {
	const CLEAR = 'cache.clear';

	/**
	 * @var OutputInterface
	 */
	public $output;

	public function __construct(OutputInterface $output) {
		$this->output = $output;
	}
} 