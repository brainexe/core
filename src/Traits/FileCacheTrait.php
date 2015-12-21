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
    protected function includeFile($name)
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
    protected function dumpCacheFile($name, $content)
    {
        $fileName = $this->getCacheFileName($name);

        file_put_contents($fileName, '<?php' . PHP_EOL . $content);
        @chmod($fileName, 0777);
    }

    /**
     * @param string $name
     * @param mixed $variable
     */
    protected function dumpVariableToCache($name, $variable)
    {
        $this->dumpCacheFile($name, 'return ' . var_export($variable, true) . ';');
    }

    /**
     * @param string $name
     * @return string
     */
    private function getCacheFileName($name)
    {
        return ROOT . 'cache/' . basename($name, '.php')  . '.php';
    }
}
