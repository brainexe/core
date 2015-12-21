<?php

namespace BrainExe\Core\Traits;



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
