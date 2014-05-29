<?php
namespace Matze\Core\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ChannelStreamHandler extends StreamHandler {
	/**
	 * @var null
	 */
	private $channel;

	/**
	 * @param string $stream
	 * @param boolean|integer $level
	 * @param string|null $channel
	 * @param boolean $bubble
	 */
	public function __construct($stream, $level = Logger::DEBUG, $channel = null, $bubble = true) {
		parent::__construct($stream, $level, $bubble);
		$this->channel = $channel;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHandling(array $record) {
		if (!parent::isHandling($record)) {
			return false;
		}

		if (empty($this->channel)) {
			return empty($record['context']['channel']);
		}

		if (empty($record['context']['channel'])) {
			return false;
		}

		return $this->channel == $record['context']['channel'];
	}

    /**
     * {@inheritdoc}
     */
    protected function write(array $record) {
        unset($record['context']['channel']);
        parent::write($record);
    }

}