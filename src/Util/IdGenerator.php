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

    const ID_LENGTH = 10;
    const LAST_ID   = 'idgenerator:lastid';

    use RedisTrait;

    /**
     * @return string
     */
    public function generateUniqueId()
    {
        $newId = $this->getRedis()->INCR(self::LAST_ID);

        return $newId;
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
