<?php

namespace BrainExe\Core\Redis;

use BrainExe\Annotations\Annotations\Service;
use Predis\Client;

/**
 * @api
 * @Service("redis", public=false)
 */
class Predis extends Client implements RedisInterface
{

}
