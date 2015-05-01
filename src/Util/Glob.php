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
    public function glob($pattern, $flags = null)
    {
        return glob($pattern, $flags);
    }
}
