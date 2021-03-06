<?php

namespace BrainExe\Core\Traits;

/**
 * @api
 */
trait FileCacheTrait
{

    /**
     * @param string $name
     * @return mixed
     */
    protected function includeFile(string $name)
    {
        $filename = $this->getCacheFileName($name);
        if (!is_file($filename)) {
            return null;
        }

        return include $filename;
    }

    /**
     * @param string $name
     * @param string $content
     */
    protected function dumpCacheFile(string $name, string $content) : void
    {
        $fileName = $this->getCacheFileName($name);

        file_put_contents($fileName, "<?php \n//@codingStandardsIgnoreFile" . PHP_EOL . $content);
    }

    /**
     * @param string $name
     * @param mixed $variable
     */
    protected function dumpVariableToCache(string $name, $variable)
    {
        $this->dumpCacheFile($name, 'return ' . var_export($variable, true) . ';');
    }

    /**
     * @param string $name
     * @return string
     */
    private function getCacheFileName(string $name) : string
    {
        return ROOT . 'cache/' . basename($name, '.php')  . '.php';
    }
}
