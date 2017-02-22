<?php

namespace BrainExe\Core\Util;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Redis\Predis;

/**
 * @Service
 * @api
 */
class IdGenerator
{

    const ID_LENGTH    = 10;
    const KEY          = 'idgenerator:%s';
    const DEFAULT_TYPE = 'lastid';

    /**
     * @var Predis
     */
    private $redis;

    /**
     * @param Predis $client
     */
    public function __construct(Predis $client)
    {
        $this->redis = $client;
    }

    /**
     * @param string $type
     * @return int
     */
    public function generateUniqueId(string $type = self::DEFAULT_TYPE) : int
    {
        return (int)$this->redis->incr(sprintf(self::KEY, $type));
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomId(int$length = self::ID_LENGTH) : string
    {
        $randomId = md5(microtime() . mt_rand()) . mt_rand();

        return substr(base_convert($randomId, 10, 36), 0, $length);
    }
}
