<?php

namespace BrainExe\Core\Traits;

/**
 * @api
 */
trait SerializableTrait
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
