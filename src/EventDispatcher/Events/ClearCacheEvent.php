<?php

namespace BrainExe\Core\EventDispatcher\Events;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class ClearCacheEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const NAME = 'cache.clear';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }
}
