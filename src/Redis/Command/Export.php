<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation("Redis.Command.Export")
 * @codeCoverageIgnore
 */
class Export extends SymfonyCommand
{

    use RedisTrait;

    const BULK_SIZE = 100;

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

        $content = implode("\n\n", $parts);

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
            case 'list':
                $this->processList($key, $parts);
                break;
            default:
                throw new Exception(sprintf('Unsupported type "%s" for key %s', $type, $key));
        }

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
        $zsets = $this->redis->zrange($key, 0, -1, 'WITHSCORES');
        if (!$zsets) {
            return;
        }

        foreach (array_chunk($zsets, self::BULK_SIZE, true) as $zset) {
            $part = [];
            $part[] = 'ZADD';
            $part[] = $this->escape($key);

            foreach ($zset as $value => $score) {
                $part[] = $this->escape($score);
                $part[] = $this->escape($value);
            }
            $parts[] = implode(' ', $part);
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    protected function processSet($key, array &$parts)
    {
        $sets = $this->redis->smembers($key);
        if (!$sets) {
            return;
        }

        foreach (array_chunk($sets, self::BULK_SIZE) as $set) {
            $set = array_map([$this, 'escape'], $set);
            $parts[] = sprintf('SADD %s %s', $key, implode(' ', $set));
        }
    }
    /**
     * @param string $key
     * @param array $parts
     */
    protected function processList($key, array &$parts)
    {
        $members = $this->redis->lrange($key, 0, 100000);
        if (!$members) {
            return;
        }

        foreach ($members as $member) {
            $parts[] = sprintf('LPUSH %s %s', $key, $this->escape($member));
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    private function processHash($key, array &$parts)
    {
        $hashes = $this->redis->hgetall($key);
        if (!$hashes) {
            return;
        }

        foreach (array_chunk($hashes, self::BULK_SIZE, true) as $hash) {
            $part = [];
            $part[] = 'HMSET';
            $part[] = $this->escape($key);
            foreach ($hash as $k => $val) {
                $part[] = $this->escape($k);
                $part[] = $this->escape($val);
            }
            $parts[] = implode(' ', $part);
        }
    }

    /**
     * @param string $key
     * @param array $parts
     */
    private function processString($key, array &$parts)
    {
        $parts[] = sprintf('SET %s %s', $key, $this->escape($this->redis->get($key)));
    }
}
