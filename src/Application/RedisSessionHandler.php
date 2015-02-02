<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use SessionHandlerInterface;

/**
 * @Service(public=false)
 */
class RedisSessionHandler implements SessionHandlerInterface
{

    const PREFIX = 'session:';

    use RedisTrait;

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        $key = $this->getKey($sessionId);
        return $this->getRedis()->GET($key) ? : '';
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $key = $this->getKey($sessionId);

        $this->getRedis()->setex($key, 86400 * 2, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        return $this->getRedis()->DEL($this->getKey($sessionId));
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        unset($lifetime);
        return true;
    }

    /**
     * @param string $sessionId
     * @return string
     */
    private function getKey($sessionId)
    {
        return self::PREFIX . $sessionId;
    }
}
