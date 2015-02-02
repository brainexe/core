<?php

namespace BrainExe\Core\Redis;

/**
 * @Service(public=false)
 */
class RedisScripts
{

    /**
     * @var string[]
     */
    private $scripts = [];

    /**
     * @var string[]
     */
    private $sha1s = [];

    /**
     * @param string $name
     * @param string $sha1
     * @param string $script
     */
    public function registerScript($name, $sha1, $script)
    {
        $this->scripts[$sha1] = $script;
        $this->sha1s[$name]   = $sha1;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getSha1($name)
    {
        return $this->sha1s[$name];
    }

    /**
     * @return string[]
     */
    public function getAllScripts()
    {
        return $this->scripts;
    }
}
