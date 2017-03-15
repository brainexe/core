<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Translation\TranslationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Authentication")
 */
class Authentication extends AbstractMiddleware
{

    use TranslationTrait;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @param LoadUser $loadUser
     */
    public function __construct(LoadUser $loadUser)
    {
        $this->loadUser = $loadUser;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        if ($request->attributes->has('user')) {
            $user = $request->attributes->get('user');
        } else {
            $session = $request->getSession();
            $userId  = (int)$session->get('user_id');

            $user = $this->loadUser($userId);
        }

        $request->attributes->set('user', $user);
        $request->attributes->set('user_id', $user->getId());

        $this->checkForRole($route, $user);

        if ($route->hasDefault('_guest')) {
            return null;
        }

        if (empty($user->getId())) {
            return $this->handleNotAuthenticatedRequest($request);
        }

        return null;
    }

    /**
     * @param Route $route
     * @param UserVO $user
     * @throws MethodNotAllowedException
     */
    protected function checkForRole(Route $route, UserVO $user) : void
    {
        if ($route->hasDefault('_role')) {
            $role = $route->getDefault('_role');
            if (!in_array($role, $user->roles, true)) {
                throw new MethodNotAllowedException([], sprintf('Need role %s', $role));
            }
        }
    }

    /**
     * @param int $userId
     * @return AnonymusUserVO|UserVO
     */
    private function loadUser(int $userId) : UserVO
    {
        if ($userId > 0) {
            try {
                return $this->loadUser->loadUserById($userId);
            } catch (UserNotFoundException $e) {
            }
        }

        return new AnonymusUserVO();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws UserException
     */
    private function handleNotAuthenticatedRequest(Request $request) : RedirectResponse
    {
        if ($request->isXmlHttpRequest()) {
            throw new MethodNotAllowedException([]);
        }

        return new RedirectResponse('/#/login');
    }
}
