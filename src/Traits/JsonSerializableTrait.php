<?php

namespace BrainExe\Core\Traits;

/**
 * @api
 */
trait JsonSerializableTrait
{
    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
