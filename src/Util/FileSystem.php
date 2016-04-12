<?php

namespace BrainExe\Core\Util;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * @api
 */
class FileSystem extends SymfonyFilesystem
{

    /**
     * @deprecated use Process component
     * @param string $fileName
     * @param integer|null $flags
     * @return string
     */
    public function fileGetContents($fileName, $flags = null)
    {
        return file_get_contents($fileName, $flags);
    }
}
