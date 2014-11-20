<?php

namespace BrainExe\Core\Util;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FileSystem extends SymfonyFilesystem {

	/**
	 * @param string $file_name
	 * @param integer|null $flags
	 * @return string
	 */
	public function fileGetContents($file_name, $flags = null) {
		return file_get_contents($file_name, $flags);
	}
}