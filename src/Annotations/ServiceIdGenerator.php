<?php

namespace BrainExe\Core\Annotations;

class ServiceIdGenerator
{
    /**
     * @param string $className
     * @return string
     */
    public function generate(string $className) : string
    {
        if (false !== ($pos = strrpos($className, '\\'))) {
            return substr($className, $pos + 1);
        }

        return $className;
    }
}
