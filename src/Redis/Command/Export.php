<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Core\Traits\RedisTrait;
use Exception;
use Predis\Client;
use string;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Redis.Command.Export", public=false)
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
        $redis = $this->getRedis();
        $keys  = $redis->keys("*");

        $parts = [];
        foreach ($keys as $key) {
            $parts = array_merge($parts, $this->dump($redis, $key));
        }

        $content = implode("\n", $parts);

        $output->writeln($content);

        $file = $input->getArgument('file');
        if ($file) {
            file_put_contents($file, $content);
        }
    }

    /**
     * @param RedisInterface $redis
     * @param string $key
     * @return string[]
     * @throws Exception
     */
    protected function dump(RedisInterface $redis, $key)
    {
        $parts = [];

        $type = $redis->type($key);

        echo "$key $type\n";

        switch ($type) {
            case 'string':
                $parts[] = sprintf(
                    "SET %s %s",
                    $key,
                    $this->escape($redis->get($key))
                );
                break;
            case 'hash':
                $hash = $redis->hgetall($key);
                foreach ($hash as $k => $val) {
                    $parts[] = sprintf(
                        "HSET %s %s %s",
                        $this->escape($key),
                        $this->escape($k),
                        $this->escape($val)
                    );
                }
                break;
            case 'set':
                $set  = $redis->smembers($key);
                foreach ($set as $k => $val) {
                    $parts[] = sprintf(
                        "SADD %s %s",
                        $this->escape($key),
                        $this->escape($val)
                    );
                }
                break;
            case 'zset':
                $zset = $redis->zrange($key, 0, -1, 'WITHSCORES');
                foreach ($zset as $value => $score) {
                    $parts[] = sprintf(
                        "ZADD %s %s %s",
                        $this->escape($key),
                        $this->escape($value),
                        $this->escape($score)
                    );
                }
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
}
