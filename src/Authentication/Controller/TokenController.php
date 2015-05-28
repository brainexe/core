<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Token;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.Controller.TokenController")
 */
class TokenController
{

    /**
     * @var Token
     */
    private $token;

    /**
     * @Inject("@Authentication.Token")
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/", name="authenticate.tokens.get", methods="GET")
     * @return array
     */
    public function getTokens(Request $request)
    {
        $userId = $request->attributes->get('user_id');

        return iterator_to_array($this->token->getTokensForUser($userId));
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/", name="authenticate.tokens.new", methods="POST")
     * @return array
     */
    public function addToken(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $roles  = (array)$request->request->get('roles');

        return $this->token->addToken($userId, $roles);
    }

    /**
     * @param Request $request
     * @Route("/user/tokens/{token}/", name="authenticate.tokens.revoke", methods="DELETE")
     * @param string $token
     * @return bool
     */
    public function revoke(Request $request, $token)
    {
        unset($request);

        $this->token->revoke($token);

        return true;
    }
}
