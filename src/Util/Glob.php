<?php

namespace BrainExe\Core\Util;

use BrainExe\Core\Annotations\Service;

/**
 * @Service("Glob")
 * @api
 */
class Glob
{

    /**
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public function execGlob(string $pattern, $flags = null) : array
    {
        return glob($pattern, $flags);
    }
}
