<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 * @api
 */
class IdGenerator
{

    const ID_LENGTH = 10;

    /**
     * @return integer
     */
    public function generateRandomNumericId()
    {
        return mt_rand();
    }

    /**
     * @param integer $length
     * @return string
     */
    public function generateRandomId($length = self::ID_LENGTH)
    {
        $randomId = md5(microtime() . mt_rand()) . mt_rand();

        return substr(base_convert($randomId, 10, 36), 0, $length);
    }
}
