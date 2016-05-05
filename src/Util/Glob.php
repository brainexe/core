<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Glob", public=false)
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
