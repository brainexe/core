<?php

namespace BrainExe\Core\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ChannelStreamHandler extends StreamHandler
{

    /**
     * @var string
     */
    private $channel;

    /**
     * @param string $stream
     * @param bool|int $level
     * @param string|null $channel
     * @param bool $bubble
     */
    public function __construct(
        string $stream,
        int $level = Logger::DEBUG,
        $channel = null,
        $bubble = true
    ) {
        parent::__construct($stream, $level, $bubble);

        $formatter = new Formatter(
            null,
            null,
            false,
            true
        );

        $this->setFormatter(
            $formatter
        );

        $this->channel = $channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel)
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record) : bool
    {
        if (count($record) == 1) {
            return parent::isHandling($record);
        }

        if (!parent::isHandling($record)) {
            return false;
        }

        if (empty($this->channel)) {
            return empty($record['context']['channel']);
        }

        if (empty($record['context']['channel'])) {
            return false;
        }

        $supported = $this->channel === $record['context']['channel'];

        return $supported;
    }
}
