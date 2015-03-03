<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=8)
 */
class AuthenticationMiddleware extends AbstractMiddleware
{

    /**
     * @var
     */
    private $guestsAllowed;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @Inject({"%application.guests_allowed%", "@DatabaseUserProvider"})
     * @param boolean $guestsAllowed
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct($guestsAllowed, DatabaseUserProvider $userProvider)
    {
        $this->guestsAllowed = $guestsAllowed;
        $this->userProvider  = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route, $routeName)
    {
        $session   = $request->getSession();
        $userId    = $session->get('user_id');
        $loggedIn  = $userId > 0;

        if ($loggedIn) {
            $user = $this->userProvider->loadUserById($userId);
        } else {
            $user = new AnonymusUserVO();
        }

        $request->attributes->set('user', $user);
        $request->attributes->set('user_id', $userId);

        if ($this->guestsAllowed) {
            return null;
        }

        if ($route->hasDefault('_guest')) {
            return null;
        }

        if ($route->hasDefault('_role')) {
            $role = $route->getDefault('_role');
            if (!in_array($role, $user->roles)) {
                throw new MethodNotAllowedException([]);
            }
        }

        if (!$loggedIn) {
            return new RedirectResponse('#/login');
        }

        return null;
    }
}
