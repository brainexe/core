<?php

namespace Matze\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

class MessageQueueEvent extends Event {

	/**
	 * @var string
	 */
	public $service_id;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * @var array
	 */
	public $arguments;

	/**
	 * @param string $service_id
	 * @param string $method
	 * @param array $arguments
	 */
	function __construct($service_id, $method, $arguments) {
		$this->method = $method;
		$this->arguments = $arguments;
		$this->service_id = $service_id;
	}


} 