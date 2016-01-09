<?php

namespace BrainExe\Core\Application;

use Predis\Session\Handler;

class SessionHandler extends Handler
{

    const KEY = 'sessions:';

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if ($data = $this->client->get(self::KEY . $sessionId)) {
            return $data;
        }

        return '';
    }
    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $this->client->setex(self::KEY . $sessionId, $this->ttl, $sessionData);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->client->del(self::KEY . $sessionId);

        return true;
    }
}
