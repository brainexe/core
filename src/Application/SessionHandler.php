<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Predis\ClientInterface;
use Predis\Session\Handler;

/**
 * @Service("Core.Application.SessionHandler", public=false)
 */
class SessionHandler extends Handler
{

    const KEY = 'sessions:';

    /**
     * @Inject({
     *     "@redis",
     *     "%session.lifetime%"
     * })
     * @param ClientInterface $client
     * @param int $lifetime
     */
    public function __construct(ClientInterface $client, $lifetime)
    {
        parent::__construct($client, [
            'gc_maxlifetime' => $lifetime
        ]);
    }

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
