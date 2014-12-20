<?php

namespace BrainExe\Core\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=10)
 */
class SessionMiddleware extends AbstractMiddleware
{

    /**
     * @var Session
     */
    private $redisSession;

    /**
     * @Inject({"@RedisSession"})
     * @param Session $redisSession
     */
    public function __construct(Session $redisSession)
    {
        $this->redisSession = $redisSession;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route, $routeName)
    {
        $request->setSession($this->redisSession);
    }
}
