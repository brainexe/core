<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Traits\RedisTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Redis.Command.Export", public=false)
 * @codeCoverageIgnore
 */
class Export extends Command
{

    use RedisTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('redis:export')
            ->setDescription('Export Redis database')
            ->addArgument('file', InputArgument::OPTIONAL, 'File to export');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keys = $this->redis->keys("*");

        $parts = [];
        foreach ($keys as $key) {
            $parts = array_merge($parts, $this->dump($key));
        }

        $content = implode("\n", $parts);

        $output->writeln($content);

        $file = $input->getArgument('file');
        if ($file) {
            file_put_contents($file, $content);
        }
    }

    /**
     * @param string $key
     * @return string[]
     * @throws Exception
     */
    protected function dump($key)
    {
        $parts = [];

        $type = $this->redis->type($key);

        switch ($type) {
            case 'string':
                $this->processString($key, $parts);
                break;
            case 'hash':
                $this->processHash($key, $parts);
                break;
            case 'set':
                $this->processSet($key, $parts);
                break;
            case 'zset':
                $this->processZset($key, $parts);
                break;
            default:
                throw new Exception(sprintf('Unsupported type "%s" for key %s', $type, $key));
        }

        $parts[] = "\n";

        return $parts;
    }

    /**
     * @param string $value
     * @return string
     */
    private function escape($value)
    {
        $value = str_replace('"', '\\"', $value);

        return sprintf('"%s"', $value);
    }

    /**
     * @param string $key
     * @param array $parts
     */
    protected function processZset($key, array &$parts)
    {
        $zset = $this->redis->zrange($key, 0, -1, 'WITHSCORES');
        foreach ($zset as $value => $score) {
            $parts[] = sprintf('ZADD %s %s %s', $this->escape($key), $this->escape($value), $this->escape($score));
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    protected function processSet($key, array &$parts)
    {
        $set = $this->redis->smembers($key);
        foreach ($set as $k => $val) {
            $parts[] = sprintf('SADD %s %s', $this->escape($key), $this->escape($val));
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    protected function processHash($key, array &$parts)
    {
        $hash = $this->redis->hgetall($key);
        foreach ($hash as $k => $val) {
            $parts[] = sprintf('HSET %s %s %s', $this->escape($key), $this->escape($k), $this->escape($val));
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    protected function processString($key, array &$parts)
    {
        $parts[] = sprintf('SET %s %s', $key, $this->escape($this->redis->get($key)));
    }
}
