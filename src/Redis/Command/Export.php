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
            ->addArgument('file', InputArgument::OPTIONAL, 'File to export', 'database.txt');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $redis = $this->getRedis();
        $keys  = $redis->keys("*");

        $file   = [];

        foreach ($keys as $key) {
            $file += $this->dump($redis, $key);
        }


        $content = implode("\n", $file);

        $content = str_replace('"', '\\"', $content);

        $output->writeln($content);

        file_put_contents($input->getArgument('file'), $content);
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

        switch ($type) {
            case 'string':
                $parts[] = "SET $key " . $redis->get($key);
                break;
            case 'hash':
                $hash = $redis->hgetall($key);
                foreach ($hash as $k => $val) {
                    $parts[] = "HSET $key $k $val";
                }
                break;
            case 'set':
                $set  = $redis->smembers($key);
                foreach ($set as $k => $val) {
                    $parts[] = "SADD $key $val";
                }
                break;
            case 'zset':
                $zset = $redis->zrange($key, 0, -1, 'WITHSCORES');
                foreach ($zset as $value => $score) {
                    $parts[] = "ZADD $key $value $score";
                }
                break;
            default:
                throw new Exception(sprintf('Unsupported type "%s" for key %s', $type, $key));
        }

        $parts[] = "\n";

        return $parts;
    }

}
