<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\Time;

/**
 * @api
 */
trait SerializableTrait
{
    function jsonSerialize()
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
