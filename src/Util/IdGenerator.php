<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 * @api
 */
class IdGenerator
{

    const ID_LENGTH    = 10;
    const KEY          = 'idgenerator:%s';
    const DEFAULT_TYPE = 'lastid';

    use RedisTrait;

    /**
     * @param string $type
     * @return string
     */
    public function generateUniqueId($type = self::DEFAULT_TYPE)
    {
        return $this->getRedis()->INCR(sprintf(self::KEY, $type));
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomId($length = self::ID_LENGTH)
    {
        $randomId = md5(microtime() . mt_rand()) . mt_rand();

        return substr(base_convert($randomId, 10, 36), 0, $length);
    }
}
