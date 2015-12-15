<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as SessionModel;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Session")
 */
class Session extends AbstractMiddleware
{

    /**
     * @var SessionModel
     */
    private $session;

    /**
     * @Inject({"@RedisSession"})
     * @param SessionModel $session
     */
    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        $request->setSession($this->session);
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        $this->session->save();
    }
}
