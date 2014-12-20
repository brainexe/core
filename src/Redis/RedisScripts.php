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
    private $_scripts = [];

    /**
     * @var string[]
     */
    private $_sha1 = [];

    /**
     * @param string $name
     * @param string $sha1
     * @param string $script
     */
    public function registerScript($name, $sha1, $script)
    {
        $this->_scripts[$sha1] = $script;
        $this->_sha1[$name] = $sha1;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getSha1($name)
    {
        return $this->_sha1[$name];
    }

    /**
     * @return string[]
     */
    public function getAllScripts()
    {
        return $this->_scripts;
    }
}
