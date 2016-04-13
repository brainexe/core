<?php

namespace BrainExe\Core\Redis;

use BrainExe\Annotations\Annotations\Service;
use Predis\Client;

/**
 * @api
 * @deprecated use Predis\Client directly
 * @Service("redis", public=false)
 */
class Predis extends Client
{

}
