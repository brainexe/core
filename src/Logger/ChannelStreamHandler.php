<?php
namespace BrainExe\Core\Logger;

use Monolog\Formatter\LineFormatter;
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
     * @param boolean|integer $level
     * @param string|null $channel
     * @param boolean $bubble
     */
    public function __construct($stream, $level = Logger::DEBUG, $channel = null, $bubble = true)
    {
        parent::__construct($stream, $level, $bubble);

        $this->setFormatter(
            new LineFormatter("[%datetime%] %level_name%: %message% %context% %extra%\n", null, false, true)
        );

        $this->channel = $channel;
    }

    /**
     * @param $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
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

        unset($record['context']['channel']);

        return $supported;
    }

    /**
     * @codeCoverageIgnore
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        parent::write($record);
    }
}
