<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session as SessionModel;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=10)
 */
class Session extends AbstractMiddleware
{

    /**
     * @var SessionModel
     */
    private $session;

    /**
     * @Inject({"@RedisSession"})
     * @param SessionModel $redisSession
     */
    public function __construct(SessionModel $redisSession)
    {
        $this->session = $redisSession;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route, $routeName)
    {
        $request->setSession($this->session);
    }
}
